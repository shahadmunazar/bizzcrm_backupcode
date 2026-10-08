<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CallLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\CallLog::truncate();

        $data = [
            [
                "box_id" => 4,
                "from" => "+917905288635",
                "to" => "+917905288635",
                "type" => "outgoing",
                "status" => "connected",
                "duration" => 8,
                "start_lat" => 25.3586874,
                "start_long" => 82.9986244,
                "start_time" => "2026-04-17 21:11:49",
                "rawTimestamp" => "1776440509506",
                "end_time" => "2026-04-17 21:11:57",
                "end_lat" => 25.3586874,
                "end_long" => 82.9986244,
                "name" => "Papa"
            ],
            [
                "box_id" => 4,
                "from" => "+919369908663",
                "to" => "+919369908663",
                "type" => "incoming",
                "status" => "connected",
                "duration" => 62,
                "start_lat" => 25.3586874,
                "start_long" => 82.9986244,
                "start_time" => "2026-04-17 22:21:57",
                "rawTimestamp" => "1776444717633",
                "end_time" => "2026-04-17 22:22:59",
                "end_lat" => 25.3586874,
                "end_long" => 82.9986244,
                "name" => "Bhaiya"
            ],
            [
                "box_id" => 4,
                "from" => "+919369908663",
                "to" => "+919369908663",
                "type" => "incoming",
                "status" => "connected",
                "duration" => 20,
                "start_lat" => 25.3586874,
                "start_long" => 82.9986244,
                "start_time" => "2026-04-17 22:53:01",
                "rawTimestamp" => "1776446581058",
                "end_time" => "2026-04-17 22:53:21",
                "end_lat" => 25.3586874,
                "end_long" => 82.9986244,
                "name" => "Bhaiya"
            ],
            [
                "box_id" => 4,
                "from" => "+919369908663",
                "to" => "+919369908663",
                "type" => "missed",
                "status" => "connected",
                "duration" => 1,
                "start_lat" => 25.3586874,
                "start_long" => 82.9986244,
                "start_time" => "2026-04-17 22:53:36",
                "rawTimestamp" => "1776446616025",
                "end_time" => "2026-04-17 22:53:37",
                "end_lat" => 25.3586874,
                "end_long" => 82.9986244,
                "name" => "Bhaiya"
            ],
            [
                "box_id" => 4,
                "from" => "+917275143075",
                "to" => "+917275143075",
                "type" => "incoming",
                "status" => "connected",
                "duration" => 27,
                "start_lat" => 25.3586874,
                "start_long" => 82.9986244,
                "start_time" => "2026-04-17 23:03:05",
                "rawTimestamp" => "1776447185909",
                "end_time" => "2026-04-17 23:03:32",
                "end_lat" => 25.3586874,
                "end_long" => 82.9986244,
                "name" => "Mummy"
            ],
            [
                "box_id" => 4,
                "from" => "+917275143075",
                "to" => "+917275143075",
                "type" => "incoming",
                "status" => "connected",
                "duration" => 10,
                "start_lat" => 25.3586874,
                "start_long" => 82.9986244,
                "start_time" => "2026-04-18 01:14:50",
                "rawTimestamp" => "1776455090406",
                "end_time" => "2026-04-18 01:15:00",
                "end_lat" => 25.3586874,
                "end_long" => 82.9986244,
                "name" => "Mummy"
            ],
            [
                "box_id" => 4,
                "from" => "+917275143075",
                "to" => "+917275143075",
                "type" => "outgoing",
                "status" => "connected",
                "duration" => 14,
                "start_lat" => 25.3586874,
                "start_long" => 82.9986244,
                "start_time" => "2026-04-18 01:15:09",
                "rawTimestamp" => "1776455109327",
                "end_time" => "2026-04-18 01:15:23",
                "end_lat" => 25.3586874,
                "end_long" => 82.9986244,
                "name" => "Mummy"
            ],
            [
                "box_id" => 4,
                "from" => "+919369908663",
                "to" => "+919369908663",
                "type" => "missed",
                "status" => "connected",
                "duration" => 25,
                "start_lat" => 25.3586874,
                "start_long" => 82.9986244,
                "start_time" => "2026-04-18 10:50:32",
                "rawTimestamp" => "1776489632556",
                "end_time" => "2026-04-18 10:50:57",
                "end_lat" => 25.3586874,
                "end_long" => 82.9986244,
                "name" => "Bhaiya"
            ],
            [
                "box_id" => 4,
                "from" => "+919369908663",
                "to" => "+919369908663",
                "type" => "missed",
                "status" => "connected",
                "duration" => 30,
                "start_lat" => 25.3586874,
                "start_long" => 82.9986244,
                "start_time" => "2026-04-18 10:52:12",
                "rawTimestamp" => "1776489732301",
                "end_time" => "2026-04-18 10:52:42",
                "end_lat" => 25.3586874,
                "end_long" => 82.9986244,
                "name" => "Bhaiya"
            ],
            [
                "box_id" => 4,
                "from" => "+919369908663",
                "to" => "+919369908663",
                "type" => "outgoing",
                "status" => "no_answer",
                "duration" => 0,
                "start_lat" => 25.3586874,
                "start_long" => 82.9986244,
                "start_time" => "2026-04-18 10:54:06",
                "rawTimestamp" => "1776489846611",
                "end_time" => "2026-04-18 10:54:06",
                "end_lat" => 25.3586874,
                "end_long" => 82.9986244,
                "name" => "Bhaiya"
            ],
            [
                "box_id" => 4,
                "from" => "+919369908663",
                "to" => "+919369908663",
                "type" => "outgoing",
                "status" => "connected",
                "duration" => 112,
                "start_lat" => 25.3586874,
                "start_long" => 82.9986244,
                "start_time" => "2026-04-18 10:55:06",
                "rawTimestamp" => "1776489906433",
                "end_time" => "2026-04-18 10:56:58",
                "end_lat" => 25.3586874,
                "end_long" => 82.9986244,
                "name" => "Bhaiya"
            ],
            [
                "box_id" => 4,
                "from" => "+917275143075",
                "to" => "+917275143075",
                "type" => "outgoing",
                "status" => "connected",
                "duration" => 72,
                "start_lat" => 25.3586874,
                "start_long" => 82.9986244,
                "start_time" => "2026-04-18 11:08:50",
                "rawTimestamp" => "1776490730338",
                "end_time" => "2026-04-18 11:10:02",
                "end_lat" => 25.3586874,
                "end_long" => 82.9986244,
                "name" => "Mummy"
            ],
            [
                "box_id" => 4,
                "from" => "+917275143075",
                "to" => "+917275143075",
                "type" => "missed",
                "status" => "connected",
                "duration" => 4,
                "start_lat" => 25.3586874,
                "start_long" => 82.9986244,
                "start_time" => "2026-04-18 11:10:44",
                "rawTimestamp" => "1776490844590",
                "end_time" => "2026-04-18 11:10:48",
                "end_lat" => 25.3586874,
                "end_long" => 82.9986244,
                "name" => "Mummy"
            ],
            [
                "box_id" => 4,
                "from" => "+917275143075",
                "to" => "+917275143075",
                "type" => "missed",
                "status" => "connected",
                "duration" => 5,
                "start_lat" => 25.3586874,
                "start_long" => 82.9986244,
                "start_time" => "2026-04-18 11:10:53",
                "rawTimestamp" => "1776490853705",
                "end_time" => "2026-04-18 11:10:58",
                "end_lat" => 25.3586874,
                "end_long" => 82.9986244,
                "name" => "Mummy"
            ],
            [
                "box_id" => 4,
                "from" => "+917275143075",
                "to" => "+917275143075",
                "type" => "missed",
                "status" => "connected",
                "duration" => 5,
                "start_lat" => 25.3586874,
                "start_long" => 82.9986244,
                "start_time" => "2026-04-18 11:11:05",
                "rawTimestamp" => "1776490865539",
                "end_time" => "2026-04-18 11:11:10",
                "end_lat" => 25.3586874,
                "end_long" => 82.9986244,
                "name" => "Mummy"
            ]
        ];

        foreach ($data as $item) {
            \App\Models\CallLog::create($item);
        }
    }
}
