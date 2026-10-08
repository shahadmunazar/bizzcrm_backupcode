<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
          "India" => [
            "statesWithDivisions" => [
              [
                "state" => "Arunachal Pradesh",
                "divisions" => [
                  "Itanagar Division",
                  "Tawang Division",
                  "Lohit Division"
                ]
              ],
              [
                "state" => "Assam",
                "divisions" => [
                  "Upper Assam Division",
                  "Lower Assam Division",
                  "North Assam Division",
                  "Barak Valley Division",
                  "Central Assam Division"
                ]
              ],
              [
                "state" => "Bihar",
                "divisions" => [
                  "Patna Division",
                  "Darbhanga Division",
                  "Munger Division",
                  "Purnia Division",
                  "Bhagalpur Division",
                  "Chhapra Division",
                  "Muzaffarpur Division",
                  "Gaya Division",
                  "Madhubani Division"
                ]
              ],
              [
                "state" => "Chhattisgarh",
                "divisions" => [
                  "Surguja Division",
                  "Bilaspur Division",
                  "Raipur Division",
                  "Durg Division",
                  "Bastar Division"
                ]
              ],
              [
                "state" => "Haryana",
                "divisions" => [
                  "Ambala Division",
                  "Rohtak Division",
                  "Gurugram Division",
                  "Hisar Division",
                  "Karnal Division",
                  "Faridabad Division"
                ]
              ],
              [
                "state" => "Himachal Pradesh",
                "divisions" => [
                  "Shimla Division",
                  "Kangra Division",
                  "Mandi Division"
                ]
              ],
              [
                "state" => "Jharkhand",
                "divisions" => [
                  "Palamu Division",
                  "North Chotanagpur Division",
                  "South Chotanagpur Division",
                  "Kolhan Division",
                  "Santhal Pargana Division"
                ]
              ],
              [
                "state" => "Karnataka",
                "divisions" => [
                  "Bangalore Division",
                  "Belgaum Division",
                  "Mysore Division",
                  "Gulbarga Division"
                ]
              ],
              [
                "state" => "Madhya Pradesh",
                "divisions" => [
                  "Bhopal Division",
                  "Indore Division",
                  "Jabalpur Division",
                  "Gwalior Division",
                  "Ujjain Division",
                  "Rewa Division",
                  "Sagar Division",
                  "Chhindwara Division",
                  "Narmadapuram Division",
                  "Ratlam Division"
                ]
              ],
              [
                "state" => "Maharashtra",
                "divisions" => [
                  "Konkan Division",
                  "Pune Division",
                  "Nashik Division",
                  "Aurangabad Division",
                  "Amravati Division",
                  "Nagpur Division"
                ]
              ],
              [
                "state" => "Meghalaya",
                "divisions" => [
                  "East Khasi Hills Division",
                  "West Khasi Hills Division",
                  "Garo Hills Division"
                ]
              ],
              [
                "state" => "Nagaland",
                "divisions" => [
                  "Nagaland Division"
                ]
              ],
              [
                "state" => "Odisha",
                "divisions" => [
                  "Central Odisha Division",
                  "North Odisha Division",
                  "South Odisha Division"
                ]
              ],
              [
                "state" => "Punjab",
                "divisions" => [
                  "Amritsar Division",
                  "Jalandhar Division",
                  "Ludhiana Division",
                  "Firozpur Division",
                  "Patiala Division"
                ]
              ],
              [
                "state" => "Rajasthan",
                "divisions" => [
                  "Ajmer Division",
                  "Jodhpur Division",
                  "Bikaner Division",
                  "Jaipur Division",
                  "Kota Division",
                  "Udaipur Division",
                  "Bharatpur Division",
                  "Sikar Division",
                  "Tonk Division",
                  "Bhilwara Division"
                ]
              ],
              [
                "state" => "Uttar Pradesh",
                "divisions" => [
                  "Agra Division",
                  "Aligarh Division",
                  "Allahabad (Prayagraj) Division",
                  "Bareilly Division",
                  "Basti Division",
                  "Chitrakoot Division",
                  "Devipatan Division",
                  "Faizabad (Ayodhya) Division",
                  "Gorakhpur Division",
                  "Jhansi Division",
                  "Kanpur Division",
                  "Lucknow Division",
                  "Meerut Division",
                  "Moradabad Division",
                  "Saharanpur Division",
                  "Azamgarh Division",
                  "Prayagraj Division",
                  "Varanasi Division"
                ]
              ],
              [
                "state" => "Uttarakhand",
                "divisions" => [
                  "Garhwal Division",
                  "Kumaon Division"
                ]
              ],
              [
                "state" => "West Bengal",
                "divisions" => [
                  "Presidency Division",
                  "Burdwan Division",
                  "Medinipur Division",
                  "Malda Division",
                  "Jalpaiguri Division"
                ]
              ]
            ]
          ]
        ];

        foreach ($data['India']['statesWithDivisions'] as $stateData) {
            $stateName = $stateData['state'];
            $state = \App\Models\State::where('name', $stateName)->first();

            if ($state) {
                foreach ($stateData['divisions'] as $divisionName) {
                    \App\Models\Division::create([
                        'name' => $divisionName,
                        'status' => 'Enable'
                    ]);
                }
            } else {
                // Log or handle missing state
                // echo "State not found: $stateName\n"; 
            }
        }
    }
}
