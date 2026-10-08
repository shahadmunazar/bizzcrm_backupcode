<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GmailMessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        // Unlimited memory just as a safeguard, but chunkById uses very little RAM
        ini_set('memory_limit', '-1');

        \Illuminate\Support\Facades\DB::connection('bizzcrm_migrate')
            ->table('mail_box_emails')
            ->orderBy('id')
            ->chunkById(500, function ($messages) {
                $chunk = [];
                
                foreach ($messages as $msg) {
                    $receivedAt = null;
                    if (!empty($msg->email_date)) {
                        $timestamp = strtotime($msg->email_date);
                        if ($timestamp) {
                            $receivedAt = date('Y-m-d H:i:s', $timestamp);
                        }
                    }

                    $msgId = !empty($msg->mail_id) ? mb_substr($msg->mail_id, 0, 255) : \Illuminate\Support\Str::uuid()->toString();
                    
                    $chunk[] = [
                        'id' => $msg->id,
                        'gmail_inbox_id' => $msg->inbox_id,
                        'message_id' => $msgId,
                        'thread_id' => $msgId,
                        'from_email' => mb_substr($msg->from, 0, 255),
                        'from_name' => null,
                        'subject' => mb_substr($msg->subject ?? '', 0, 255),
                        'snippet' => mb_substr(strip_tags($msg->body ?? ''), 0, 500),
                        'body' => $msg->body,
                        'received_at' => $receivedAt,
                        'lead_id' => $msg->lead_id,
                        'is_processed' => $msg->status === 'Processed' ? 1 : 0,
                        'activity_status' => null,
                        'created_at' => $msg->created_at,
                        'updated_at' => $msg->updated_at,
                    ];
                }

                if (count($chunk) > 0) {
                    \Illuminate\Support\Facades\DB::table('gmail_messages')->insertOrIgnore($chunk);
                    $lastId = end($chunk)['id'];
                    echo "Processed chunk up to ID: {$lastId}\n";
                }
            });

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }
}
