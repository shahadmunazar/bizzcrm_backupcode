<?php

namespace App\Exports;

use App\Models\Lead;
use App\Models\SystemField;
use App\Models\BoxCustomField;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeadsExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading
{
    protected $boxId;
    protected $filters;
    protected $systemFields;
    protected $customFields;

    public function __construct($boxId, $filters = [])
    {
        $this->boxId = $boxId;
        $this->filters = $filters;
        
        // Fetch System Fields for dynamic export
        $this->systemFields = SystemField::where('box_id', $boxId)
                                         ->where('object', 'leads')
                                         ->whereNotNull('field')
                                         ->orderBy('sequence')
                                         ->get();
                                         
        $this->customFields = BoxCustomField::where('box_id', $boxId)
                                            ->where('object', 'leads')
                                            ->orderBy('ordering')
                                            ->get();
    }

    public function query()
    {
        $query = Lead::query()
            ->leftJoin('crm_sources', 'leads.lead_source_id', '=', 'crm_sources.id')
            ->leftJoin('contact_stage', 'leads.lead_status_id', '=', 'contact_stage.id')
            ->leftJoin('users as assigned_users', 'leads.assigned_to', '=', 'assigned_users.id')
            ->leftJoin('countries', 'leads.country_id', '=', 'countries.id')
            ->leftJoin('states', 'leads.state_id', '=', 'states.id')
            ->leftJoin('cities', 'leads.city_id', '=', 'cities.id')
            ->select(
                'leads.*',
                'crm_sources.name as lead_source_name',
                'contact_stage.name as lead_status_name',
                'assigned_users.name as assigned_to_name',
                'countries.name as country_name',
                'states.name as state_name',
                'cities.name as city_name',
                DB::raw("(SELECT GROUP_CONCAT(CONCAT(IFNULL(ls.name,''), ' - ', IFNULL(lc.name,''), ' - ', IFNULL(d.name,''), ' (', IFNULL(dc.bill_name,''), ')') SEPARATOR '\n') 
                          FROM division_areas da 
                          LEFT JOIN states ls ON da.state_id = ls.id 
                          LEFT JOIN cities lc ON da.city_id = lc.id 
                          LEFT JOIN divisions d ON da.division_id = d.id 
                          LEFT JOIN companies dc ON d.company_id = dc.id 
                          WHERE da.lead_id = leads.id AND da.deleted_at IS NULL) as working_locations_info")
            )
            ->where('leads.box_id', $this->boxId);

        // Join Custom Fields Table
        $customTable = 'data_' . $this->boxId;
        if (Schema::hasTable($customTable)) {
            $query->leftJoin($customTable, 'leads.id', '=', "{$customTable}.lead_id");
            foreach ($this->customFields as $field) {
                // Alias custom fields to avoid collision with standard fields
                $query->addSelect("{$customTable}.{$field->col_name} as custom_{$field->col_name}");
            }
        }

        if (!empty($this->filters)) {
            $filters = $this->filters;
             if (isset($filters['field'])) {
                $filters = [$filters];
            }

             $idFields = ['id', 'lead_source_id', 'lead_status_id', 'assigned_to', 'added_by', 'last_updated_by', 'box_id', 'country_id', 'state_id', 'city_id'];

            foreach ($filters as $filter) {
                if (empty($filter['field'])) continue;

                $field = $filter['field'];

                // Map Frontend fields to Database columns
                $fieldMap = [
                    'lead_id' => 'id',
                    'lead_name' => 'name',
                    'owner_id' => 'assigned_to',
                    'owner' => 'assigned_to',
                    'created_by' => 'added_by',
                    'updated_by' => 'last_updated_by',
                    'zip_code' => 'postal_code',
                    'company' => 'company_name',
                ];

                if (array_key_exists($field, $fieldMap)) {
                    $field = $fieldMap[$field];
                }
                $operator = $filter['operator'] ?? 'contains';
                $value = $filter['value'] ?? '';
                
                 // Handle 'leads.' prefix
                $field = str_replace('leads.', '', $field);
                
                // Enforce prefix to avoid ambiguity
                if (!str_contains($field, '.')) {
                    $field = 'leads.' . $field;
                }

                 switch ($operator) {
                    case 'contains':
                         if (in_array(str_replace('leads.', '', $field), $idFields)) {
                            $query->where($field, $value);
                         } else {
                            $query->where($field, 'like', '%' . $value . '%');
                         }
                        break;
                    case 'not_contains':
                         if (in_array(str_replace('leads.', '', $field), $idFields)) {
                            $query->where($field, '!=', $value);
                         } else {
                            $query->where($field, 'not like', '%' . $value . '%');
                         }
                        break;
                    case 'equals':
                        $query->where($field, $value);
                        break;
                    case 'not_equals':
                        $query->where($field, '!=', $value);
                        break;
                    case 'starts_with':
                         $query->where($field, 'like', $value . '%');
                        break;
                    case 'ends_with':
                         $query->where($field, 'like', '%' . $value);
                        break;
                     case 'empty':
                        $query->where(function($q) use ($field) {
                             $q->whereNull($field)->orWhere($field, '');
                        });
                        break;
                    case 'not_empty':
                        $query->where(function($q) use ($field) {
                             $q->whereNotNull($field)->where($field, '!=', '');
                        });
                        break;
                }
            }
        }
        
        // Return raw query to avoid model hydration overhead
        return $query->latest('leads.created_at')->toBase();
    }

    public function headings(): array
    {
        if ($this->systemFields->count() > 0) {
            $headers = $this->systemFields->pluck('field_name')->toArray();
            $fields = $this->systemFields->pluck('field')->toArray();
            
            // Prepend ID if not present
            if (!in_array('id', $fields)) {
                array_unshift($headers, 'ID');
            }
            
            // Append Created At if not present
            if (!in_array('created_at', $fields)) {
                $headers[] = 'Created At';
            }
            
            if ($this->customFields->count() > 0) {
                $headers = array_merge($headers, $this->customFields->pluck('name')->toArray());
            }
            $headers[] = 'Working Location';
            return $headers;
        }
        $headers = array_merge(
            ['ID', 'Name', 'Email', 'Phone', 'Company', 'Lead Source', 'Lead Status', 'Created At'],
            $this->customFields->count() > 0 ? $this->customFields->pluck('name')->toArray() : []
        );
        $headers[] = 'Working Location';
        return $headers;
    }
    
    public function map($lead): array
    {
         if ($this->systemFields->count() > 0) {
             $row = [];
             $fields = $this->systemFields->pluck('field')->toArray();

             foreach ($this->systemFields as $field) {
                 $col = $field->field;
                 
                  switch ($col) {
                      case 'lead_source_id':
                          $val = $lead->lead_source_name;
                          break;
                      case 'lead_status_id':
                          $val = $lead->lead_status_name;
                          break;
                      case 'assigned_to':
                      case 'owner_id':
                          $val = $lead->assigned_to_name;
                          break;
                      case 'country_id':
                          $val = $lead->country_name;
                          break;
                       case 'state_id':
                          $val = $lead->state_name;
                          break;
                       case 'city_id':
                          $val = $lead->city_name;
                          break;
                      default:
                          $val = $lead->$col ?? null;
                  }
                  $row[] = $val;
             }
             
             // Prepend ID Data
             if (!in_array('id', $fields)) {
                 array_unshift($row, $lead->id);
             }
             
             // Append Created At Data
             if (!in_array('created_at', $fields)) {
                 $row[] = $lead->created_at;
             }
             
             // Append Custom Fields Data
             if ($this->customFields->count() > 0) {
                 foreach ($this->customFields as $field) {
                     // Use aliased column name
                     $col = 'custom_' . $field->col_name;
                     $row[] = $lead->$col ?? null;
                 }
             }
             $row[] = $lead->working_locations_info ?? '';
             return $row;
         }
         
         $row = [
            $lead->id,
            $lead->name,
            $lead->email,
            $lead->phone,
            $lead->company_name,
            $lead->lead_source_name,
            $lead->lead_status_name,
            $lead->created_at,
         ];

         // Append Custom Fields Data (Fallback)
         if ($this->customFields->count() > 0) {
             foreach ($this->customFields as $field) {
                 $col = 'custom_' . $field->col_name;
                 $row[] = $lead->$col ?? null;
             }
         }
         $row[] = $lead->working_locations_info ?? '';
         return $row;
    }
    
    public function chunkSize(): int
    {
        return 1000;
    }
}
