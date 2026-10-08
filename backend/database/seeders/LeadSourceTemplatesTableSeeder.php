<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeadSourceTemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $templates = [
            [
                'id' => 1, 'name' => 'Bizz Digital', 'indetifier' => 'bizzDigital',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address","drug_license":"Drug License","gstin":"Gst no."}',
                'source_fields' => '{"Name":"name","Email":"email","Phone":"phone","City":"location","Query":"query","IP-address":"ip_address"}',
                'status' => 'Active', 'created_at' => '2018-09-23 08:01:25', 'updated_at' => '2018-09-23 08:01:25',
                'has_word' => 'bizzdigital.com/wp-content/uploads/2018/05/logo.png,www.bizzdigital.com/LogoBizz.png,im/2056417/d78c43d4217e49f16a30ae4a2ecfd705fe465f36f1ff68e61ac3ac8ab9f2672d.png', 'from' => null
            ],
            [
                'id' => 2, 'name' => 'Trade Myntra', 'indetifier' => 'tradeMyntra',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address","drug_license":"Drug License","gstin":"Gst no."}',
                'source_fields' => '{"Name":"name","Email":"email","Phone":"phone","City":"location","Message":"query","lead_type":"lead_type"}',
                'status' => 'Active', 'created_at' => '2018-09-23 08:01:25', 'updated_at' => '2018-09-23 08:01:25',
                'has_word' => 'Trademaantra', 'from' => null
            ],
            [
                'id' => 3, 'name' => 'Pharma Hopers', 'indetifier' => 'pharmaHopers',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address","drug_license":"Drug License","gstin":"Gst no."}',
                'source_fields' => '{"Name":"name","Email":"email","Mobile":"phone","Address":"address","PCD Area":"location","Message":"query","Drug License":"drug_license","GST Number":"gstin","lead_type":"lead_type"}',
                'status' => 'Active', 'created_at' => '2018-09-23 08:01:25', 'updated_at' => '2018-09-23 08:01:25',
                'has_word' => 'PharmaHopers', 'from' => 'info@pharmahopers.com'
            ],
            [
                'id' => 4, 'name' => 'Top 10 Suppliers', 'indetifier' => 'topTenSuppliers',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address","drug_license":"Drug License","gstin":"Gst no."}',
                'source_fields' => '{"UserName":"name","Email Address":"email","Mobile No":"phone","Location":"location","Message":"query","Category Name":"extra"}',
                'status' => 'Active', 'created_at' => '2018-09-23 08:01:25', 'updated_at' => '2018-09-23 08:01:25',
                'has_word' => 'top10suppliers.in', 'from' => null
            ],
            [
                'id' => 5, 'name' => 'Pharma Biz Connect', 'indetifier' => 'pharmaBizConnect',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address","drug_license":"Drug License","gstin":"Gst no."}',
                'source_fields' => '{"Name":"name","Email":"email","Mobile":"phone","Location":"location","Requirement":"query","Drug license":"drug_license","GSTN":"gstin","Products/Specialty":"extra","Call Receiving Time":"extra"}',
                'status' => 'Active', 'created_at' => '2018-10-01 04:50:18', 'updated_at' => '2018-10-01 04:50:18',
                'has_word' => 'Pharmabizconnect', 'from' => null
            ],
            [
                'id' => 6, 'name' => 'Pharma Xpress', 'indetifier' => 'pharmaXpress',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address"}',
                'source_fields' => '{"Name":"name","Email":"email","Phone":"phone","State": "state_id", "District":"location","Details":"query"}',
                'status' => 'Active', 'created_at' => '2018-10-01 05:48:12', 'updated_at' => '2018-10-01 05:48:12',
                'has_word' => null, 'from' => 'no-reply@pcdpharmaexpress.com'
            ],
            [
                'id' => 7, 'name' => 'Adflair Media', 'indetifier' => 'adflairMedia',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address","drug_license":"Drug License","gstin":"Gst no."}',
                'source_fields' => '{"Name":"name","Email":"email","Phone":"phone","City":"location","Query":"query","Message":"query"}',
                'status' => 'Active', 'created_at' => '2018-09-23 08:01:25', 'updated_at' => '2018-09-23 08:01:25',
                'has_word' => 'Adflair', 'from' => 'adflairadword@gmail.com'
            ],
            [
                'id' => 8, 'name' => 'India Mart', 'indetifier' => 'indiaMart',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address"}',
                'source_fields' => '{"Name":"name","Email":"email","Phone":"phone","Location":"location","City":"city_id","Country":"country_id","State":"state_id","Requirements":"query","lead_type":"lead_type"}',
                'status' => 'Active', 'created_at' => '2018-09-23 08:01:25', 'updated_at' => '2018-09-23 08:01:25',
                'has_word' => '', 'from' => 'buyershelpdesk@indiamart.com,buyershelp+enq@indiamart.com,buyleads@indiamart.com'
            ],
            [
                'id' => 9, 'name' => 'Trade India', 'indetifier' => 'tradeIndia',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Mobile No","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP"}',
                'source_fields' => '{"Name":"name","Email":"email","Mobile No":"phone","Location":"location","City":"location","Country":"country_id","State":"state_id","Requirement Type":"query","IP":"ip_address","lead_type":"lead_type"}',
                'status' => 'Active', 'created_at' => '2018-09-23 02:31:25', 'updated_at' => '2018-09-23 02:31:25',
                'has_word' => 'www.tradeindia.com', 'from' => null
            ],
            [
                'id' => 10, 'name' => 'Franchis Express', 'indetifier' => 'franchisExpress',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Mobile No","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP"}',
                'source_fields' => '{"Name":"name","EmailID":"email","Mobile":"phone","Address":"location","Message":"query"}',
                'status' => 'Active', 'created_at' => '2018-09-23 02:31:25', 'updated_at' => '2018-09-23 02:31:25',
                'has_word' => 'www.franchisexpress.in', 'from' => null
            ],
            [
                'id' => 11, 'name' => 'Bizz CRM Default', 'indetifier' => 'bizzDigital',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address"}',
                'source_fields' => '{"Name":"name","Email":"email","Phone":"phone","City":"location","Query":"query","IP-address":"ip_address"}',
                'status' => 'Active', 'created_at' => '2018-12-22 08:01:25', 'updated_at' => '2018-12-22 08:01:25',
                'has_word' => 'bizzcrm,Bizz Digital Solutions', 'from' => null
            ],
            [
                'id' => 12, 'name' => 'Pharma View', 'indetifier' => 'pharmaView',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Mobile No","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP"}',
                'source_fields' => '{"name":"name","email":"email","phone":"phone","address":"address","query":"query"}',
                'status' => 'Active', 'created_at' => '2019-01-14 02:31:25', 'updated_at' => '2019-01-14 02:31:25',
                'has_word' => null, 'from' => 'pvappserver@gmail.com,pharmaviewinfo@gmail.com'
            ],
            [
                'id' => 13, 'name' => 'Pharma Mart', 'indetifier' => 'pharmaMart',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Mobile No","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP"}',
                'source_fields' => '{"name":"name","email":"email","phone":"phone","location":"location","city":"city_id","state":"state_id","query":"query"}',
                'status' => 'Active', 'created_at' => '2019-01-14 02:31:25', 'updated_at' => '2019-01-14 02:31:25',
                'has_word' => null, 'from' => 'enquirypharmamart@gmail.com'
            ],
            [
                'id' => 14, 'name' => 'Pharma Business', 'indetifier' => 'pharmaBusiness',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Mobile No","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP"}',
                'source_fields' => '{"Name":"name","Email":"email","Contact No.":"phone","State":"location","Address":"address","Message":"query"}',
                'status' => 'Active', 'created_at' => '2019-01-14 02:31:25', 'updated_at' => '2019-01-14 02:31:25',
                'has_word' => null, 'from' => 'pharmabusinessqueries@yahoo.com'
            ],
            [
                'id' => 15, 'name' => 'Trade XL', 'indetifier' => 'tradeXl',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Mobile No","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP"}',
                'source_fields' => '{"Contact Person":"name","Email Id":"email","Contact No":"phone","Location":"location","Address":"address","City":"city_id","State":"state_id","Country":"country_id","Requirement":"query"}',
                'status' => 'Active', 'created_at' => '2019-01-14 02:31:25', 'updated_at' => '2019-01-14 02:31:25',
                'has_word' => null, 'from' => 'server@tradexl.com,admin@tradexl.com,admin@tradexlmail.co.in'
            ],
            [
                'id' => 16, 'name' => 'Pharma Franchise Mart', 'indetifier' => 'pharmafranchisekart',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address"}',
                'source_fields' => '{"Name":"name","Email Id":"email","Phone":"phone","City":"location","Message":"query"}',
                'status' => 'Active', 'created_at' => '2018-12-22 08:01:25', 'updated_at' => '2018-12-22 08:01:25',
                'has_word' => 'pharmafranchisemart.com', 'from' => null
            ],
            [
                'id' => 17, 'name' => 'Pharma Flair', 'indetifier' => 'pharmaFlair',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address"}',
                'source_fields' => '{"Name":"name","Email":"email","Mobile":"phone","Location":"location","State":"state_id","Message":"query"}',
                'status' => 'Active', 'created_at' => '2018-12-22 08:01:25', 'updated_at' => '2018-12-22 08:01:25',
                'has_word' => 'pharmaflair.in', 'from' => null
            ],
            [
                'id' => 18, 'name' => 'pharma Franchise Kart', 'indetifier' => 'pharmafranchisekart',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address","drug_license":"Drug License","gstin":"Gst no."}',
                'source_fields' => '{"Name":"name","Email Id":"email","Phone":"phone","City":"location","Message":"query"}',
                'status' => 'Active', 'created_at' => '2018-09-23 02:31:25', 'updated_at' => '2018-09-23 02:31:25',
                'has_word' => 'pharmafranchisekart.com', 'from' => null
            ],
            [
                'id' => 19, 'name' => 'Pharma Info Tech', 'indetifier' => 'pharmainfotech',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address","drug_license":"Drug License","gstin":"Gst no."}',
                'source_fields' => '{"Name":"name","Email Id":"email","Phone Number":"phone","State":"state","City":"location","Message":"query"}',
                'status' => 'Active', 'created_at' => '2018-09-22 21:01:25', 'updated_at' => '2018-09-22 21:01:25',
                'has_word' => '', 'from' => 'contact@pharmainfotech.com'
            ],
            [
                'id' => 20, 'name' => 'Dawa Charcha', 'indetifier' => 'dawacharcha',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address","drug_license":"Drug License","gstin":"Gst no."}',
                'source_fields' => '{"Name":"name","Email":"email","Contact No.":"phone","Address":"address","City":"location","Message":"query"}',
                'status' => 'Active', 'created_at' => '2018-09-22 21:01:25', 'updated_at' => '2018-09-22 21:01:25',
                'has_word' => '', 'from' => 'dawacharchainquiries@gmail.com'
            ],
            [
                'id' => 21, 'name' => 'Pharma Vends', 'indetifier' => 'pharmavends',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address","drug_license":"Drug License","gstin":"Gst no."}',
                'source_fields' => '{"Name":"name","Email":"email","Contact":"phone","Location":"location","Message":"query","City":"city_id","State":"state_id","Pin Code":"postal_code","GST No":"gstin_number"}',
                'status' => 'Active', 'created_at' => '2018-09-22 21:01:25', 'updated_at' => '2018-09-22 21:01:25',
                'has_word' => '', 'from' => 'pharmavends@pharmavends.com'
            ],
            [
                'id' => 22, 'name' => 'Pharma Franchisee India', 'indetifier' => 'pharmafranchisekart',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address"}',
                'source_fields' => '{"Name":"name","Email Id":"email","Phone":"phone","City":"location","Message":"query"}',
                'status' => 'Active', 'created_at' => '2018-12-22 08:01:25', 'updated_at' => '2018-12-22 08:01:25',
                'has_word' => 'pharmafranchiseeindia.com', 'from' => 'admin@pharmafranchiseeindia.com'
            ],
            [
                'id' => 23, 'name' => 'Pharma Franchise club', 'indetifier' => 'pharmafranchiseclub',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address","drug_license":"Drug License","gstin":"Gst no."}',
                'source_fields' => '{"Contact Person":"name","E-Mail Address":"email","Telphone":"phone","City":"location","State":"state_id","Requirements":"query"}',
                'status' => 'Active', 'created_at' => '2018-09-22 08:31:25', 'updated_at' => '2018-09-22 08:31:25',
                'has_word' => null, 'from' => 'server@livenow.app,info@oneloginsuiteinc.com,server@pharmafranchise.club'
            ],
            [
                'id' => 24, 'name' => 'Pharma Buffet', 'indetifier' => 'pharmabuffet',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address","drug_license":"Drug License","gstin":"Gst no."}',
                'source_fields' => '{"Name":"name","Mail":"email","Phone":"phone","City":"location","State":"state_id","Comment":"query"}',
                'status' => 'Active', 'created_at' => '2018-09-22 08:31:25', 'updated_at' => '2018-09-22 08:31:25',
                'has_word' => '', 'from' => 'info@vizztechsolutions.com'
            ],
            [
                'id' => 25, 'name' => 'TruWeb Media\r\n', 'indetifier' => 'trueWeb',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address","drug_license":"Drug License","gstin":"Gst no."}',
                'source_fields' => '{"Name":"name","E-mail":"email","Mobile":"phone","City":"city_id","State":"state_id","Message":"query","City / State":"location"}',
                'status' => 'Active', 'created_at' => '2022-08-26 19:31:25', 'updated_at' => '2022-08-27 11:30:47',
                'has_word' => 'info@tru-web.com', 'from' => null
            ],
            [
                'id' => 26, 'name' => 'Just Dial', 'indetifier' => 'justdial',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address","drug_license":"Drug License","gstin":"Gst no."}',
                'source_fields' => '{"Name":"name","E-mail":"email","User Phone":"phone","User City":"city_id","User Stat":"state_id","User Requirement":"query","User Area":"location"}',
                'status' => 'Active', 'created_at' => '2022-11-02 04:44:55', 'updated_at' => '2022-08-26 23:00:47',
                'has_word' => '', 'from' => 'instantemail@justdial.com'
            ],
            [
                'id' => 27, 'name' => 'Next Tech Mart', 'indetifier' => 'nexttechmart',
                'system_fields' => '{"name":"Name","email":"Email","phone":"Phone","designation":"Designation","address":"Address","country_id":"Country","state_id":"State","city_id":"City","ip_address":"IP Address","drug_license":"Drug License","gstin":"Gst no."}',
                'source_fields' => '{"Name":"name","EmailId":"email","Mobile":"phone","Message":"query","Location":"location"}',
                'status' => 'Active', 'created_at' => '2023-02-16 06:20:32', 'updated_at' => '2023-02-16 06:20:32',
                'has_word' => '', 'from' => 'info@nexttechmart.com'
            ],
        ];

        foreach ($templates as $template) {
            DB::table('lead_source_templates')->updateOrInsert(['id' => $template['id']], $template);
        }
    }
}
