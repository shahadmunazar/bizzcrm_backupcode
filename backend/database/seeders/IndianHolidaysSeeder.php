<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Box;
use App\Models\HolidayType;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Support\Str;

class IndianHolidaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $boxes = Box::all();

        // 2026 & 2027 Indian Holidays (Approximate for Lunar dates)
        $holidays2026 = [
            ['title' => 'New Year', 'date' => '2026-01-01', 'type' => 'Public Holiday'],
            ['title' => 'Republic Day', 'date' => '2026-01-26', 'type' => 'National Holiday'],
            ['title' => 'Maha Shivaratri', 'date' => '2026-02-15', 'type' => 'Public Holiday'],
            ['title' => 'Holi', 'date' => '2026-03-04', 'type' => 'Public Holiday'],
            ['title' => 'Eid al-Fitr', 'date' => '2026-03-20', 'type' => 'Public Holiday'],
            ['title' => 'Good Friday', 'date' => '2026-04-03', 'type' => 'Public Holiday'],
            ['title' => 'Dr Ambedkar Jayanti', 'date' => '2026-04-14', 'type' => 'Public Holiday'],
            ['title' => 'Independence Day', 'date' => '2026-08-15', 'type' => 'National Holiday'],
            ['title' => 'Raksha Bandhan', 'date' => '2026-08-28', 'type' => 'Public Holiday'],
            ['title' => 'Janmashtami', 'date' => '2026-09-04', 'type' => 'Public Holiday'],
            ['title' => 'Gandhi Jayanti', 'date' => '2026-10-02', 'type' => 'National Holiday'],
            ['title' => 'Dussehra', 'date' => '2026-10-20', 'type' => 'Public Holiday'],
            ['title' => 'Diwali', 'date' => '2026-11-08', 'type' => 'Public Holiday'],
            ['title' => 'Guru Nanak Jayanti', 'date' => '2026-11-24', 'type' => 'Public Holiday'],
            ['title' => 'Christmas', 'date' => '2026-12-25', 'type' => 'Public Holiday'],
        ];

        $holidays2027 = [
            ['title' => 'New Year', 'date' => '2027-01-01', 'type' => 'Public Holiday'],
            ['title' => 'Republic Day', 'date' => '2027-01-26', 'type' => 'National Holiday'],
            ['title' => 'Maha Shivaratri', 'date' => '2027-03-07', 'type' => 'Public Holiday'],
            ['title' => 'Holi', 'date' => '2027-03-23', 'type' => 'Public Holiday'],
            ['title' => 'Eid al-Fitr', 'date' => '2027-03-10', 'type' => 'Public Holiday'],
            ['title' => 'Good Friday', 'date' => '2027-03-26', 'type' => 'Public Holiday'],
            ['title' => 'Dr Ambedkar Jayanti', 'date' => '2027-04-14', 'type' => 'Public Holiday'],
            ['title' => 'Independence Day', 'date' => '2027-08-15', 'type' => 'National Holiday'],
            ['title' => 'Raksha Bandhan', 'date' => '2027-08-17', 'type' => 'Public Holiday'],
            ['title' => 'Janmashtami', 'date' => '2027-08-25', 'type' => 'Public Holiday'],
            ['title' => 'Gandhi Jayanti', 'date' => '2027-10-02', 'type' => 'National Holiday'],
            ['title' => 'Dussehra', 'date' => '2027-10-09', 'type' => 'Public Holiday'],
            ['title' => 'Diwali', 'date' => '2027-10-29', 'type' => 'Public Holiday'],
            ['title' => 'Guru Nanak Jayanti', 'date' => '2027-11-13', 'type' => 'Public Holiday'],
            ['title' => 'Christmas', 'date' => '2027-12-25', 'type' => 'Public Holiday'],
        ];

        $allHolidays = array_merge($holidays2026, $holidays2027);

        foreach ($boxes as $box) {
            
            $types = ['National Holiday', 'Public Holiday'];
            $typeModels = [];
            
            foreach ($types as $typeStr) {
                // Ensure HolidayType exists for the box
                $typeModel = HolidayType::firstOrCreate(
                    [
                        'box_id' => $box->id,
                        'holiday_type' => $typeStr,
                    ],
                    [
                        'holiday_code' => Str::slug($typeStr),
                        'is_mandatory' => 1,
                        'max_limit' => null,
                        'status' => 1,
                        'calculate_in_calendar' => 1,
                    ]
                );
                $typeModels[$typeStr] = $typeModel;
            }

            foreach ($allHolidays as $holiday) {
                $typeModel = $typeModels[$holiday['type']];
                
                Holiday::updateOrCreate(
                    [
                        'box_id' => $box->id,
                        'title' => $holiday['title'],
                        'date' => $holiday['date'],
                    ],
                    [
                        'holiday_code' => Str::slug($holiday['title']),
                        'holiday_type_id' => $typeModel->id,
                        'holiday_type' => $typeModel->holiday_type,
                        'country_region' => ['India'],
                        'description' => $holiday['title'],
                        'status' => 'active',
                    ]
                );
            }
        }
    }
}
