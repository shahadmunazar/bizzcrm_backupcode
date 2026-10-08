<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Box;
use App\Models\SystemField;

class LeadsSystemFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fields = [
            // Sequence 1: Lead Name
            [
                'name' => 'Name',
                'object' => 'leads',
                'field' => 'name',
                'field_name' => 'name',
                'field_type' => 'text',
                'validation_rules' => 'required|string|max:255',
                'is_required' => true,
                'sequence' => 1,
                'placeholder' => 'Enter lead name',
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 2: Email
            [
                'name' => 'Email',
                'object' => 'leads',
                'field' => 'email',
                'field_name' => 'email',
                'field_type' => 'email',
                'validation_rules' => 'required|string|max:255',
                'is_required' => true,
                'sequence' => 2,
                'placeholder' => 'Enter Email Here',
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 3: Secondary Email
            [
                'name' => 'Sec Email',
                'object' => 'leads',
                'field' => 'sec_email',
                'field_name' => 'sec_email',
                'field_type' => 'email',
                'validation_rules' => 'nullable|string|max:255',
                'is_required' => false,
                'sequence' => 3,
                'placeholder' => 'Enter Secondary Email',
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 4: Phone
            [
                'name' => 'Phone',
                'object' => 'leads',
                'field' => 'phone',
                'field_name' => 'phone',
                'field_type' => 'text',
                'validation_rules' => 'required|string|max:20',
                'is_required' => true,
                'sequence' => 4,
                'placeholder' => 'Enter phone number',
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 5: Phone Type
            [
                'name' => 'Phone Type',
                'object' => 'leads',
                'field' => 'phone_type',
                'field_name' => 'phone_type',
                'field_type' => 'select',
                'options' => [
                    ['value' => 'work', 'label' => 'Work'],
                    ['value' => 'home', 'label' => 'Home'],
                ],
                'validation_rules' => 'nullable|string|in:work,home',
                'is_required' => false,
                'sequence' => 5,
                'placeholder' => 'Choose phone type',
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 6: Lead Type
            [
                'name' => 'Lead Type',
                'object' => 'leads',
                'field' => 'lead_type',
                'field_name' => 'lead_type',
                'field_type' => 'radio',
                'options' => [
                    ['value' => 'Direct Lead', 'label' => 'Direct Lead'],
                    ['value' => 'Buy Lead', 'label' => 'Buy Lead'],
                    ['value' => 'Call Lead', 'label' => 'Call Lead'],
                    ['value' => 'Catalogue View', 'label' => 'Catalogue View'],
                ],
                'validation_rules' => 'nullable|string',
                'is_required' => false,
                'sequence' => 6,
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 7: Company Name
            [
                'name' => 'Company Name',
                'object' => 'leads',
                'field' => 'company_name',
                'field_name' => 'company_name',
                'field_type' => 'text',
                'options' => null,
                'validation_rules' => 'nullable|string|max:255',
                'is_required' => false,
                'sequence' => 7,
                'placeholder' => 'Enter company name',
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 8: Designation
            [
                'name' => 'Designation',
                'object' => 'leads',
                'field' => 'designation',
                'field_name' => 'designation',
                'field_type' => 'text',
                'validation_rules' => 'nullable|string|max:100',
                'is_required' => false,
                'sequence' => 8,
                'placeholder' => 'Enter designation',
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 9: Value
            [
                'name' => 'Value',
                'object' => 'leads',
                'field' => 'value',
                'field_name' => 'value',
                'field_type' => 'text',
                'validation_rules' => 'nullable|numeric',
                'is_required' => false,
                'sequence' => 9,
                'placeholder' => 'Enter value',
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 10: Currency
            [
                'name' => 'Currency',
                'object' => 'leads',
                'field' => 'currency',
                'field_name' => 'currency',
                'field_type' => 'select',
                'options' => [
                    ['value' => '$', 'label' => '$'],
                    ['value' => '€', 'label' => '€'],
                ],
                'validation_rules' => 'nullable|string|in:$,€',
                'is_required' => false,
                'sequence' => 10,
                'placeholder' => 'Select currency',
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 11: Lead Status
            [
                'name' => 'Lead Status',
                'object' => 'leads',
                'field' => 'lead_status',
                'field_name' => 'lead_status',
                'field_type' => 'select',
                'options' => [],
                'validation_rules' => 'nullable',
                'is_required' => false,
                'sequence' => 11,
                'placeholder' => 'Choose lead status',
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 12: Source
            [
                'name' => 'Source',
                'object' => 'leads',
                'field' => 'lead_source_id',
                'field_name' => 'lead_source_id',
                'field_type' => 'select',
                'options' => [],
                'validation_rules' => 'nullable',
                'is_required' => false,
                'sequence' => 12,
                'placeholder' => 'Choose source',
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 13: Industry
            [
                'name' => 'Industry',
                'object' => 'leads',
                'field' => 'industry',
                'field_name' => 'industry',
                'field_type' => 'select',
                'options' => [],
                'validation_rules' => 'nullable',
                'is_required' => false,
                'sequence' => 13,
                'placeholder' => 'Choose industry',
                'is_visible' => true,
                'is_editable' => true,
            ],

            // Sequence 15: Country
            [
                'name' => 'Country',
                'object' => 'leads',
                'field' => 'country_id',
                'field_name' => 'country_id',
                'field_type' => 'select',
                'options' => [],
                'validation_rules' => 'nullable|integer',
                'is_required' => false,
                'sequence' => 15,
                'placeholder' => 'Select country',
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 16: State
            [
                'name' => 'State',
                'object' => 'leads',
                'field' => 'state_id',
                'field_name' => 'state_id',
                'field_type' => 'select',
                'options' => [],
                'validation_rules' => 'nullable|integer',
                'is_required' => false,
                'sequence' => 16,
                'placeholder' => 'Select state',
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 17: City
            [
                'name' => 'City',
                'object' => 'leads',
                'field' => 'city_id',
                'field_name' => 'city_id',
                'field_type' => 'select',
                'options' => [],
                'validation_rules' => 'nullable|integer',
                'is_required' => false,
                'sequence' => 17,
                'placeholder' => 'Select city',
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 18: Address
            [
                'name' => 'Address',
                'object' => 'leads',
                'field' => 'address',
                'field_name' => 'address',
                'field_type' => 'text',
                'validation_rules' => 'nullable|string',
                'is_required' => false,
                'sequence' => 18,
                'placeholder' => 'Enter address',
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 19: Postal Code
            [
                'name' => 'Postal Code',
                'object' => 'leads',
                'field' => 'postal_code',
                'field_name' => 'postal_code',
                'field_type' => 'text',
                'validation_rules' => 'nullable|string|max:20',
                'is_required' => false,
                'sequence' => 19,
                'placeholder' => 'Enter Postal Code',
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 20: Location
            [
                'name' => 'Location',
                'object' => 'leads',
                'field' => 'location',
                'field_name' => 'location',
                'field_type' => 'text',
                'validation_rules' => 'nullable|string',
                'is_required' => false,
                'sequence' => 20,
                'placeholder' => 'Enter Location',
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 21: Tags
            [
                'name' => 'Tags',
                'object' => 'leads',
                'field' => 'tags',
                'field_name' => 'tags',
                'field_type' => 'text',
                'validation_rules' => 'nullable|string',
                'is_required' => false,
                'sequence' => 21,
                'placeholder' => 'Enter tags',
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 22: Description
            [
                'name' => 'Description',
                'object' => 'leads',
                'field' => 'description',
                'field_name' => 'description',
                'field_type' => 'textarea',
                'validation_rules' => 'nullable|string',
                'is_required' => false,
                'sequence' => 22,
                'placeholder' => 'Enter description',
                'is_visible' => true,
                'is_editable' => true,
            ],
            // Sequence 24: Visibility
            [
                'name' => 'Visibility',
                'object' => 'leads',
                'field' => 'visibility',
                'field_name' => 'visibility',
                'field_type' => 'radio',
                'options' => [
                    ['value' => 'public', 'label' => 'Public'],
                    ['value' => 'private', 'label' => 'Private'],
                    ['value' => 'select_people', 'label' => 'Select People'],
                ],
                'validation_rules' => 'nullable|string|in:public,private,select_people',
                'is_required' => false,
                'sequence' => 24,
                'is_visible' => true,
                'is_editable' => true,
            ],
        ];

        // Deduplicate fields based on 'field' key
        $fieldNames = [];
        foreach ($fields as $index => $field) {
            if (in_array($field['field'], $fieldNames)) {
                unset($fields[$index]);
            }
            else {
                $fieldNames[] = $field['field'];
            }
        }

        // Ensure array is re-indexed
        $fields = array_values($fields);

        // Run for every Box
        $boxes = Box::all();

        foreach ($boxes as $box) {
            $this->command->info("Processing Box ID: {$box->id}");

            // Track fields that should exist to optionally cleanup others
            $validFieldNames = [];

            foreach ($fields as $fieldConfig) {
                // Ensure correct box_id
                $fieldConfig['box_id'] = $box->id;

                $fieldReference = $fieldConfig['field_name']; // Using field_name as primary identifier
                if (empty($fieldReference)) {
                    $fieldReference = $fieldConfig['field'];
                }

                $validFieldNames[] = $fieldReference;

                // Check if exists by field_name + box_id
                $existingField = SystemField::where('box_id', $box->id)
                    ->where('object', 'leads')
                    ->where(function ($q) use ($fieldConfig) {
                    $q->where('field_name', $fieldConfig['field_name'])
                        ->orWhere('field', $fieldConfig['field']);
                })
                    ->first();

                if ($existingField) {
                    // Update
                    $existingField->update($fieldConfig);
                    $this->command->info("- Updated field: {$fieldConfig['name']}");
                }
                else {
                    // Create
                    SystemField::create($fieldConfig);
                    $this->command->info("- Created field: {$fieldConfig['name']}");
                }
            }

        // Clean up: Delete system fields for 'leads' in this box that are NOT in our valid list?
        // User did not strictly say "delete others", but implied it by providing a strict list.
        // Commenting this out to be safe, or enabling if desired.
        // Given previous manual confusion with 'status' vs 'lead_status', let's be purposeful.
        // We will DELETE fields that explicitly clash with our key fields but are not them, 
        // OR we can rely on the update.
        // But if there is a 'status' field (Active/Inactive) that is NOT in $fields, it should be deleted.

        // Identify fields in DB that are NOT in the $validFieldNames
        /*
         $extraFields = SystemField::where('box_id', $box->id)
         ->where('object', 'leads')
         ->whereNotIn('field_name', $validFieldNames)
         ->whereNotIn('field', $validFieldNames)
         ->get();
         
         if($extraFields->count() > 0) {
         $this->command->info("Found " . $extraFields->count() . " extra fields. Deleting...");
         foreach($extraFields as $extra) {
         $extra->delete();
         $this->command->info("Deleted extra field: {$extra->name} ({$extra->field_name})");
         }
         }
         */
        }
    }
}
