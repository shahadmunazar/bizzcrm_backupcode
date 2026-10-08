<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailInboxesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        $email_inboxes = \Illuminate\Support\Facades\DB::connection('bizzcrm_migrate')
            ->table('email_inboxes')
            ->where('email_type', 'gmail')
            ->get();

        $chunk = [];
        foreach ($email_inboxes as $item) {
            $chunk[] = [
                'id' => $item->id,
                'box_id' => $item->box_id,
                'user_id' => $item->user_id,
                'email' => $item->email,
                'access_token' => $item->email_token,
                'refresh_token' => $item->email_refresh_token,
                'expires_in' => 3599, // default common expiry
                'token_type' => 'Bearer',
                'status' => $item->status === 'Active' ? 'Enabled' : 'Disabled',
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
            
            if (count($chunk) >= 500) {
                \Illuminate\Support\Facades\DB::table('gmail_inboxes')->insertOrIgnore($chunk);
                $chunk = [];
            }
        }

        if (count($chunk) > 0) {
            \Illuminate\Support\Facades\DB::table('gmail_inboxes')->insertOrIgnore($chunk);
        }

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}
