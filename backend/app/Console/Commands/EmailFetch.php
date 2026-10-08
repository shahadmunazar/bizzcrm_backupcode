<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\EmailInbox;
use App\Lead;
use LaravelGmail;
use App\LeadColumn;
use Config;
use DomDocument;
use DomXPath;
use Carbon\Carbon;
use App\MailBoxEmail;
use App\BoxUser;
use App\Http\Controllers\LeadController;
use DB;
use Dacastro4\LaravelGmail\Services\Message\Mail as LaravelGmailMail;
use Auth;
use App\Team;

use App\Box;

use App\Activity;
use App\TeamUser;
use Artisan;




class EmailFetch extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'email:fetch {inbox_id}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Command description';

  protected $production = true;
  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */


  public function testMail($id)
  {
    try {
      $mailData = \App\Models\GmailMessage::find($id);
      if (!$mailData) {
        dump("Invalid ID");
        return;
      }

      $inbox = \App\Models\GmailInbox::where('status', 'Enabled')->where('id', $mailData->gmail_inbox_id)->with('box')->first();

      if (!$inbox) {
        dump("No Inbox");
        return;
      }

      $this->box = $inbox->box;
      $mail = [];
      $mail['subject'] = $mailData->subject;
      $mail['from'] = $mailData->from_email;
      $mail['body'] = html_entity_decode($mailData->body);

      dump($mail['subject']);
      dump($mail['from']);

      $this->from = $mail['from'];
      $filters = \App\Models\MailFilter::with('template')->where('box_id', $inbox->box_id)->where('status', 'Active')->get();
      $filter = $this->getTemplate($filters, $mail);

      if ($filter) {
        $templateName = $filter->template->indetifier;
        dump("Template " . $templateName);

        $data = $this->$templateName($mail);
        dump($data);

        if ($data) {
          $data['email_date'] = \Carbon\Carbon::parse($mailData->received_at);

          $filterData = [
            'id' => $filter->id,
            'filter_type' => 'email',
            'round_robin' => $filter->round_robin ?? 0,
            'round_robin_users' => $filter->round_robin_users ?? [],
            'assigned_to' => $filter->assigned_to ?? $this->box->superadmin_id ?? 0,
            'lead_status_id' => $filter->lead_status_id ?? null,
            'lead_source_id' => $filter->lead_source_id ?? null,
          ];

          $mapper = json_decode($filter->template->source_fields, true);

          if ($filter->template->indetifier == "justdial") {
            $this->saveToPharmaKhabar($this->box, $data, $mapper);
          }

          $LeadController = new \App\Http\Controllers\Leads\LeadsController;
          $lead = $LeadController->saveLead($this->box, $data, $filterData, $mapper, $data['email_date']->format('Y-m-d H:i:s'), $inbox->user_id, $filter->name);

          if ($lead) {
            $mailData->lead_id = $lead->id;
            dump("Imported/Updated Lead");
          }
          else {
            dump("Could not create lead");
          }
        }
        else {
          dump("No Data in mail");
        }
        $mailData->save();
      }

    }
    catch (\Exception $e) {
      dump([$e->getLine(), $e->getFile(), $e->getMessage()]);
    }
  }

  public function saveToPharmaKhabar($box, $data, $mapper)
  {
    $mapped = ['email_date' => $data['email_date']->format('Y-m-d H:i:s')];

    foreach ($data as $key => $value) {
      if ($key != "email_date") {
        if (!isset($mapper[$key])) {
          $mapper[$key] = "extra";
        }

        if ($mapper[$key] == "extra") {
          if (isset($customFieldMapper[$key])) {
            $box_fields[$customFieldMapper[$key]] = $value;
          }
          else {
            $extra[$key] = $value;
          }
        }
        else {
          $mapped[$mapper[$key]] = $value;
        }
      }
    }

    if (isset($mapped['query'])) {
      $mapped['message'] = $mapped['query'];
      unset($mapped['query']);
    }

    if (isset($mapped['phone'])) {
      $phone = explode(", ", $mapped['phone']);
      $mapped['phone'] = preg_replace("/[^0-9]+/", "", $phone[0]);
      $mapped['phone'] = substr(preg_replace("/[^0-9]+/", "", $phone[0]), -10);
    }

    try {
      $client = new \GuzzleHttp\Client([
        'headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Bearer jgsdhjgdsty444GDWSddg@154df']
      ]);

      $response = $client->post('https://www.pharmakhabar.com/api/save-queries',
      ['body' => json_encode($mapped)]
      );
      \Log::error($response->getBody()->getContents());
    }
    catch (\Exception $e) {
      \Log::error($e);
    }
  }

  private function extractBody($payload)
  {
    $body = '';
    if (isset($payload['body']['data'])) {
      $body = base64_decode(strtr($payload['body']['data'], '-_', '+/'));
    }
    elseif (isset($payload['parts'])) {
      $htmlPart = collect($payload['parts'])->firstWhere('mimeType', 'text/html');
      if ($htmlPart) {
        if (isset($htmlPart['body']['data'])) {
          $body = base64_decode(strtr($htmlPart['body']['data'], '-_', '+/'));
        }
        elseif (isset($htmlPart['parts'])) {
          $body = $this->extractBody($htmlPart);
        }
      }
      else {
        foreach ($payload['parts'] as $part) {
          if (isset($part['parts'])) {
            $body .= $this->extractBody($part);
          }
        }
      }
    }
    return $body;
  }

  public function handle()
  {
    $this->info("Starting EmailFetch command logic...");

    if ($this->argument('inbox_id') != null && $this->argument('inbox_id') == "test") {
      $mailData = \App\Models\GmailMessage::where(function ($query) {
        $query->where('is_processed', 0);
        $query->where('subject', 'NOT LIKE', '%Bizz CRM%');
        $query->where('subject', '<>', 'Password reset link');
      })->orderBy('id', 'asc')->get();

      dump(count($mailData));

      foreach ($mailData as $value) {
        dump("Mail ID: " . $value->id);
        $this->testMail($value->id);
      }
      dump("Done");
      exit;
    }

    $inboxes = \App\Models\GmailInbox::where('status', 'Enabled');
    if ($this->argument('inbox_id')) {
      $inboxes = $inboxes->where('id', $this->argument('inbox_id'));
    }
    $inboxes = $inboxes->with('box')->get();

    $googleController = new \App\Http\Controllers\GoogleController();

    foreach ($inboxes as $inbox) {
      $inbox->last_sync_at = \Carbon\Carbon::now();
      $inbox->save();

      $leadCount = 0;
      try {
        if ($inbox->user_id) {
          Auth::loginUsingId($inbox->user_id);
        }

        $this->box = $inbox->box;
        if (!$this->box) {
          continue;
        }

        $this->info("checking Mailbox: " . $inbox->email);

        $googleController->refreshTokenIfExpired($inbox);

        $pageToken = null;
        $maxToScan = 20;
        $scannedCount = 0;

        do {
          $response = \Illuminate\Support\Facades\Http::withToken($inbox->access_token)
            ->get("https://gmail.googleapis.com/gmail/v1/users/me/messages", [
            'maxResults' => 20,
            'q' => 'is:unread',
            'pageToken' => $pageToken
          ]);

          if ($response->status() === 401 && $googleController->refreshToken($inbox)) {
            $response = \Illuminate\Support\Facades\Http::withToken($inbox->access_token)
              ->get("https://gmail.googleapis.com/gmail/v1/users/me/messages", [
              'maxResults' => 20,
              'q' => 'is:unread',
              'pageToken' => $pageToken
            ]);
          }

          if (!$response->successful()) {
            $this->error("Failed to fetch messages for {$inbox->email}");
            break;
          }

          $data = $response->json();
          $messages = $data['messages'] ?? [];
          $pageToken = $data['nextPageToken'] ?? null;

          foreach ($messages as $msg) {
            $scannedCount++;

            try {
              $msgResponse = \Illuminate\Support\Facades\Http::withToken($inbox->access_token)
                ->get("https://gmail.googleapis.com/gmail/v1/users/me/messages/{$msg['id']}?format=full");

              if (!$msgResponse->successful())
                continue;

              $msgData = $msgResponse->json();

              $checkMessage = \App\Models\GmailMessage::where('message_id', $msg['id'])->first();
              if ($checkMessage && $checkMessage->is_processed) {
                try {
                  \Illuminate\Support\Facades\Http::withToken($inbox->access_token)
                    ->post("https://gmail.googleapis.com/gmail/v1/users/me/messages/{$msg['id']}/modify", [
                    'removeLabelIds' => ['UNREAD']
                  ]);
                }
                catch (\Exception $e) {
                }
                continue;
              }

              $headers = collect($msgData['payload']['headers'] ?? []);
              $subject = $headers->firstWhere('name', 'Subject')['value'] ?? '';
              $from = $headers->firstWhere('name', 'From')['value'] ?? '';
              $to = $headers->firstWhere('name', 'To')['value'] ?? '';
              $dateStr = $headers->firstWhere('name', 'Date')['value'] ?? '';

              $msgDate = $dateStr ?\Carbon\Carbon::parse($dateStr)->setTimezone(\Config::get('app.timezone')) : now();

              $body = $this->extractBody($msgData['payload'] ?? []);

              $mail = [];
              $mail['subject'] = $subject;

              preg_match('/<([^>]+)>/', $from, $emailMatch);
              $mail['from'] = $emailMatch ? trim($emailMatch[1]) : trim($from);
              $this->from = $mail['from'];

              $mail['body'] = html_entity_decode($body);

              $this->info("Parsing Email: {$mail['subject']}");

              $logMail = $checkMessage ?: new \App\Models\GmailMessage;
              $logMail->gmail_inbox_id = $inbox->id;
              $logMail->message_id = $msg['id'];
              $logMail->thread_id = $msgData['threadId'] ?? $msg['id'];
              $logMail->from_email = $mail['from'];
              $logMail->subject = $subject;
              $logMail->snippet = $msgData['snippet'] ?? '';
              $logMail->body = $mail['body'];
              $logMail->received_at = $msgDate;

              if (in_array($mail['subject'], ['Bizz CRM - New Lead Assigned', 'Bizz CRM - Reminder Alert', 'Password reset link']) || str_contains($mail['subject'], 'Bizz CRM - ')) {
                $logMail->is_processed = 1;
                $logMail->save();
                try {
                  \Illuminate\Support\Facades\Http::withToken($inbox->access_token)
                    ->post("https://gmail.googleapis.com/gmail/v1/users/me/messages/{$msg['id']}/modify", [
                    'removeLabelIds' => ['UNREAD']
                  ]);
                }
                catch (\Exception $e) {
                }
                continue;
              }

              $filters = \App\Models\MailFilter::with('template')->where('box_id', $inbox->box_id)->where('status', 'Active')->get();
              $filter = $this->getTemplate($filters, $mail);

              if ($filter) {
                $templateName = $filter->template->indetifier;
                $this->info("Template matched: {$templateName}");
                try {
                  $data = $this->$templateName($mail);
                  if ($data) {
                    $data['email_date'] = $msgDate;

                    $filterData = [
                      'id' => $filter->id,
                      'filter_type' => 'email',
                      'round_robin' => $filter->round_robin ?? 0,
                      'round_robin_users' => $filter->round_robin_users ?? [],
                      'assigned_to' => $filter->assigned_to ?? $this->box->superadmin_id ?? 0,
                      'lead_status_id' => $filter->lead_status_id ?? null,
                      'lead_source_id' => $filter->lead_source_id ?? null,
                      'lead_sub_status_id' => null
                    ];

                    $mapper = json_decode($filter->template->source_fields, true);

                    if ($filter->template->indetifier == "justdial") {
                      $this->saveToPharmaKhabar($this->box, $data, $mapper);
                    }

                    $LeadController = new \App\Http\Controllers\Leads\LeadsController;
                    $lead = $LeadController->saveLead($this->box, $data, $filterData, $mapper, $data['email_date']->format('Y-m-d H:i:s'), clone auth()->user(), $filter->name);

                    if ($lead) {
                      $leadCount++;
                      $logMail->lead_id = $lead->id;
                      $filter->update(['last_lead_at' => now()]);
                    }
                  }
                }
                catch (\Exception $e) {
                  $this->error("Template parsing failed: " . $e->getMessage());
                }
              }
              else {
                $this->info("No template match found.");
              }

              $logMail->is_processed = 1;
              $logMail->save();

              try {
                \Illuminate\Support\Facades\Http::withToken($inbox->access_token)
                  ->post("https://gmail.googleapis.com/gmail/v1/users/me/messages/{$msg['id']}/modify", [
                  'removeLabelIds' => ['UNREAD']
                ]);
              }
              catch (\Exception $e) {
              }

            }
            catch (\Exception $e) {
              $this->error("Message fetch error: " . $e->getMessage() . "\n");
              if (str_contains($e->getMessage(), '401 Unauthorized')) {
                $inbox->status = 'Disabled';
              }
            }

            if ($scannedCount >= $maxToScan)
              break 2;
          }
        } while ($pageToken);

        if ($leadCount > 0) {
          $inbox->last_sync_at = \Carbon\Carbon::now();
        }
        $inbox->save();

      }
      catch (\Exception $e) {
        $this->error("Error for inbox {$inbox->email}: " . $e->getMessage() . "\n");
        if (str_contains(strtolower($e->getMessage()), 'invalid_grant') || str_contains(strtolower($e->getMessage()), 'unauthorized')) {
          $inbox->status = 'Disabled';
          $inbox->save();
        }
      }
    }
  }

  private function getTemplate($filters, $mail)
  {




    if ($mail['body'] == "") {

      return false;
    }



    foreach ($filters as $filter) {

      $matches = [];
      $not_matches = [];
      $matched = false;



      if ($filter->template->has_word) {



        $templateWord = explode(",", $filter->template->has_word);



        foreach ($templateWord as $temp_word) {


          preg_match("/" . strtolower(str_replace("/", "\/", $this->cleanFieldValue($temp_word))) . "/", strtolower($mail['body']), $matchedBody);

          // dump(str_replace("/","\/",$this->cleanFieldValue($temp_word)));


          if ($matchedBody) {
            $matched = true;
            $matches["template_has_words"] = $filter->template->has_word;
            break;
          }
        }


      }

      if (!$matched) {

        if ($filter->template->from) {


          $templateFrom = explode(",", $filter->template->from);



          foreach ($templateFrom as $temp_form) {


            $matched = (strtolower($this->cleanFieldValue($temp_form)) == strtolower($this->cleanFieldValue($this->from)));


            if ($matched) {
              $matches["template_from"] = $filter->from;
              break;
            }
          }





        }
      }




      if ($matched) {

        if ($filter->from) {


          $matched = (strtolower($this->cleanFieldValue($filter->from)) == strtolower($this->cleanFieldValue($this->from)));


          if ($matched) {
            $matches["from"] = $filter->from;
          }
          else {
            $not_matches["from"] = $filter->from;
          }

        }

        if ($filter->subject) {

          $matched = (strtolower($this->cleanFieldValue($filter->subject)) == strtolower($this->cleanFieldValue($mail['subject'])));

          if ($matched) {
            $matches["subject"] = $filter->subject;
          }
          else {
            $not_matches["subject"] = $filter->subject;
          }

        }

        if ($filter->has_words) {
          $matched = false;
          //dd($filter->has_words);
          $templateWord = explode(",", $filter->has_words);

          foreach ($templateWord as $temp_word) {


            preg_match("/" . str_replace("/", "\/", $this->cleanFieldValue($temp_word)) . "/", $mail['body'], $matchedBody);

            // dump(str_replace("/","\/",$this->cleanFieldValue($temp_word)));


            if ($matchedBody) {
              $matched = true;
              $matches["has_words"] = $temp_word;
            }
          }


        }

        if ($matched == true) {


          return $filter;
        }



      }







    }
    //  exit;
    return false;
  }

  private function cleanFieldName($label)
  {

    $label = ltrim(rtrim(str_replace(":", "", $label)));

    return $label;
  }

  private function cleanFieldValue($value)
  {

    if (is_array($value)) {
      return $value;
    }
    //$value = ltrim(rtrim($value));

    $value = preg_replace('/[^(\x20-\x7F)\x0A\x0D]*/', '', $value);
    $value = str_replace("\r\n", '', $value);
    $value = str_replace("  ", ' ', $value);

    $value = ltrim(rtrim($value));
    return $value;
  }


  private function cleanSpaces($value)
  {
    $value = trim(preg_replace('/\s+/', ' ', $value));
    return $value;
  }

  private function tradeMyntra($mail)
  {

    $text = $mail['body'];
    // echo $text;
    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);


    $tables = $finder->query("//table");


    $data = [];
    foreach ($tables as $key => $node) {



      $rows = $node->getElementsByTagName('tr');
      foreach ($rows as $row) {

        if (!preg_match('/Congratulations/', $this->cleanFieldName($row->getElementsByTagName('td')[0]->nodeValue))) {
          $key_name = "";
          $key_val = "";
          if ($row->getElementsByTagName('td')->length == 3) {

            $key_name = $this->cleanFieldName($row->getElementsByTagName('td')[0]->nodeValue);
            $key_val = $this->cleanFieldValue($row->getElementsByTagName('td')[2]->nodeValue);
          }
          elseif ($row->getElementsByTagName('td')->length == 2) {

            $key_name = $this->cleanFieldName($row->getElementsByTagName('td')[0]->nodeValue);
            $key_val = $this->cleanFieldValue($row->getElementsByTagName('td')[1]->nodeValue);
          }

          if ($key_name != "" && $key_val) {

            if (in_array($key_name, ['Phone', 'Email'])) {
              if ($key_val == "*********") {
                $key_val = "";
              }
            }


            $data[$key_name] = $key_val;
          }



        }
      }


    }


    return $data;
  }

  private function pharmaHopers($mail)
  {
    // Buy Lead

    $text = $mail['body'];
    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);


    $tables = $finder->query("//table");

    $data = [];

    if (preg_match('/Buy Lead/', $mail['subject'])) {
      $data['lead_type'] = 'Buy Lead';
    }

    foreach ($tables as $key => $node) {

      if ($key == "3") {

        $rows = $node->getElementsByTagName('tr');
        foreach ($rows as $row) {

          if ($row->getElementsByTagName('td')->length == 2) {


            $data[$this->cleanFieldName($row->getElementsByTagName('td')[0]->nodeValue)] = $this->cleanFieldValue($row->getElementsByTagName('td')[1]->nodeValue);
          }
        }
      }

    }

    if (isset($data['Reach us for any help'])) {

      unset($data['Reach us for any help']);
    }
    /*echo $text;
     dd($data);*/
    return $data;
  }


  private function topTenSuppliers($mail, $type = "png")
  {

    $text = $mail['body'];

    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);

    $text = str_replace('https://www.top10suppliers.in', 'https://top10suppliers.in', $text);

    //$tables =  $finder->query("//img[@src='https://top10suppliers.in/assets/images/logo.".$type."']");

    $tables = $finder->query("//p");



    $data = [];
    $tags = [];
    foreach ($tables as $value) {

      $tags[] = $value->nodeValue;
    //dump([$value->parentNode->parentNode->nextSibling->nextSibling->nodeName,$value->parentNode->parentNode->nextSibling->nextSibling->nodeValue]);

    // $queryContainer = $value->parentNode->parentNode->nextSibling->nextSibling;

    // $child  = $queryContainer->getElementsByTagName('div')[0];

    // $fields=[];
    // foreach ($child->getElementsByTagName('p') as $key => $value) {
    //    $fields[]= $this->cleanFieldName($value->nodeValue);
    // }

    // $values  = $queryContainer->getElementsByTagName('div')[1];


    // foreach ($values->getElementsByTagName('p') as $key => $value) {

    //    $data[$fields[$key]]=$this->cleanFieldValue($value->nodeValue);
    // }
    }

    if (!empty($tags)) {

      for ($i = 0; $i < count($tags) / 2; $i++) {
        # code...
        if (isset($tags[$i + 6])) {
          $data[$this->cleanFieldName($tags[$i])] = $this->cleanFieldValue($tags[$i + 6]);
        }

      }
    }
    else {

      $tables = $finder->query("//table");

      $data = [];

      foreach ($tables as $key => $node) {



        $rows = $node->getElementsByTagName('tr');
        foreach ($rows as $row) {

          $data[$this->cleanFieldName($row->getElementsByTagName('th')[0]->nodeValue)] = $this->cleanFieldValue($row->getElementsByTagName('td')[0]->nodeValue);

        }


      }

    }


    // if($type=="png" && empty($data)){

    //    return $this->topTenSuppliers($text,'jpg');
    // }



    return $data;
  }


  private function bizzDigital($mail)
  {

    $text = $mail['body'];

    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);


    $tables = $finder->query("//table");

    $data = [];
    foreach ($tables as $key => $node) {



      $rows = $node->getElementsByTagName('tr');
      foreach ($rows as $row) {

        if ($row->getElementsByTagName('td')->length == 2) {

          $data[$this->cleanFieldName($row->getElementsByTagName('td')[0]->nodeValue)] = $this->cleanFieldValue($row->getElementsByTagName('td')[1]->nodeValue);
        }
      }


    }

    return $data;
  }

  private function pharmaBizConnect($mail)
  {

    $text = $mail['body'];
    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);


    $tables = $finder->query("//table");

    $data = [];
    foreach ($tables as $key => $node) {



      $rows = $node->getElementsByTagName('tr');
      foreach ($rows as $row) {

        if ($row->getElementsByTagName('td')->length == 2) {
          $td = $row->getElementsByTagName('td')[0];
          $b = $td->getElementsByTagName('b')[0];
          $data[$this->cleanFieldName($b->getElementsByTagName('span')[0]->nodeValue)] = $this->cleanFieldValue($row->getElementsByTagName('td')[1]->nodeValue);
        }
      }


    }
    /*echo $text;
     dd($data);*/
    return $data;
  }


  private function pharmaXpress($mail)
  {

    $text = $mail['body'];
    $text = explode("\n", str_replace("\t", "", $text));
    $data = [];
    foreach ($text as $row) {

      //$row =str_replace("&nbsp;"," ",$row);
      $rowData = explode(" : ", str_replace("&nbsp;", " ", htmlentities(strip_tags($row))));
      // $rowData = explode(" : ", $row);
      //    dump($rowData);

      if (count($rowData) == 2) {

        if ($this->cleanFieldName($rowData[0]) == "Name") {

          $data[$this->cleanFieldName($rowData[0])] = $this->cleanFieldValue(preg_replace('/[^a-z ]/i', '', $rowData[1]));
        }
        else {

          $data[$this->cleanFieldName($rowData[0])] = $this->cleanFieldValue($rowData[1]);
        }
      }
    }

    /*dd($text );
     exit;
     
     $dom = new DomDocument();
     libxml_use_internal_errors(true);
     $dom->formatOutput = true;
     $dom->preserveWhiteSpace = false;
     $dom->loadHTML($text);
     libxml_use_internal_errors(false);
     $finder = new DomXPath($dom);
     $div =  $finder->query('//div[@class="gmail_quote"]');
     $child = $div[0]->nodeValue;
     $split = preg_split('<\n>', $child);
     $values = [$split[3],$split[4],$split[5],$split[6],$split[7]];
     $data = [];
     foreach ($values as $key => $value) {
     $field = strtok($value, ':');
     $fields[]= $this->cleanFieldName($field);
     $last = substr($value, strpos($value, ":") + 2);
     $data[$fields[$key]]=$this->cleanFieldValue($last);
     }*/


    return $data;
  }


  private function franchisExpress($mail)
  {

    $text = $mail['body'];
    //echo $text;
    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);


    $div = $finder->query('//div[//a[@href="http://www.franchisexpress.in"]]');

    $div = $finder->query('//div');

    //$div = $finder->query("//div[contains(., 'www.franchisexpress.in')]");
    $data = [];
    if (count($div) == "1") {


      $text = utf8_encode($text);
      //exit;
      //dump($message);
      $div = explode("<br>", $text);



      foreach ($div as $key => $value) {

        if (preg_match('/:/', $value) && !preg_match('/Enquiry from/', $value)) {


          $text = explode(":", str_replace("Â ", "", strip_tags($value)));

          if (count($text) == "2") {

            $data[$this->cleanSpaces($text[0])] = $this->cleanSpaces($text[1]);
          }

        }
      }

    }
    else {
      foreach ($div as $key => $value) {

        if (preg_match('/:/', $value->nodeValue)) {

          $text = explode(":", str_replace("Â ", "", $value->nodeValue));

          if (count($text) == "2") {
            $data[$this->cleanSpaces($text[0])] = $this->cleanSpaces($text[1]);
          }
        }
      }
    }


    if (isset($data['Enquiry from'])) {
      unset($data['Enquiry from']);
    }

    if (isset($data['E-mail ID'])) {

      $data['EmailID'] = $data['E-mail ID'];
      unset($data['E-mail ID']);
    }

    //dd($data);
    return $data;
  }


  private function tradeIndia($mail)
  {

    $text = $mail['body'];
    if (preg_match('/New Buy Leads/', $text)) {
      return $this->tradeIndiaBuy($mail);
    }
    // echo $text;
    $text = str_replace("Mobile No :<br>", "Mobile No : ", $text);
    $text = str_replace("<br>", "\n", $text);

    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);

    $data = [];

    $table = $finder->query('//table');

    if (!isset($table[2])) {

      return;
    }


    $tr = $table[2]->getElementsByTagName('tr');

    if (!isset($tr[14])) {

      return;
    }

    $tr = $tr[14];

    $innerTable = $tr->getElementsByTagName('table');

    if (!isset($innerTable[0])) {

      return;
    }
    $contactInfo = $innerTable[0]->getElementsByTagName('td');

    if (!isset($contactInfo[0])) {
      return;
    }

    $contactInfo = $contactInfo[0];
    $contactInfo = str_replace("&#13;\n", "", $dom->saveXML($contactInfo));
    $contactInfo = str_replace("<br/>", "<br />", $contactInfo);
    $contactInfo = $this->cleanSpaces($contactInfo);
    dump($contactInfo);

    preg_match('/>(.*)</', $contactInfo, $contactData);

    //dd($contactData);

    if (!isset($contactData)) {

      return;
    }

    $contactData = str_replace("<br />", "<br/>", $contactData[0]);
    $dataRow = explode("<br/>", $contactData);

    //dd($dataRow);
    foreach ($dataRow as $text) {
      $text = str_replace(">", "", strip_tags($text));
      $text = str_replace("<", "", $text);

      //dump($text);
      if (preg_match("/:/", $text)) {

        $row = explode(" : ", trim($text));

        if (preg_match("/\//", $row[0]) && isset($row[1])) {
          $values = explode("/", $row[1]);
          $row = explode("/", $row[0]);


          foreach ($row as $subkey => $subrow) {
            if (isset($values[$subkey]))
              $data[$subrow] = $values[$subkey];
          }

        }
        else {

          $data[$row[0]] = $row[1];
        }

      }

    }

    $tr = $table[2]->getElementsByTagName('tr')[11];

    $dataRow = explode("\n", $tr->nodeValue);
    foreach ($dataRow as $text) {
      if (preg_match("/:/", $text)) {

        $row = explode(" : ", trim($text));
        if (isset($row[1])) {

          $data[$row[0]] = $row[1];
        }

      }

    }

    //dd($data);        

    return $data;
  }


  private function tradeIndiaBuy($mail)
  {

    $text = $mail['body'];


    // echo "here";


    // echo $text;
    $text = str_replace("Mobile No.", "Mobile No", $text);
    $text = str_replace("<br>", "\n", $text);

    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);

    $data = [];

    $data['lead_type'] = 'Buy Lead';

    $table = $finder->query('//table');

    if (!isset($table[2])) {

      return;
    }


    $tr = $table[2]->getElementsByTagName('tr');
    //{"Name":"name","Email":"email","Mobile No":"phone","Location":"location","City":"location","Country":"country_id","State":"state_id","Requirement Type":"query","IP":"ip_address"}
    $data = [];
    foreach ($table as $key => $value) {

      //  echo "--------------------------------------------------------".$key."-------------------\n\n\n";
      if ($key == 5) {
        $values = explode("\n", $this->cleanFieldValue($value->nodeValue));


        $data['Moderated On'] = $this->cleanSpaces(str_replace('Moderated On :', '', $values[0]));
        if (isset($values[2])) {

          $data['Location'] = $this->cleanSpaces($values[2]);

          $loc_data = explode(',', $data['Location']);

          if (isset($loc_data[0])) {
            $data['City'] = $loc_data[0];
          }

          if (isset($loc_data[1])) {
            $data['State'] = $loc_data[1];
          }
        }


      }
      else if ($key == 6) {

        $data['Requirements'] = trim(str_replace("\n\n", "\n", $value->nodeValue));

      }
      else if ($key == 8) {

        $values = explode("\n", $this->cleanFieldValue($value->nodeValue));

        foreach ($values as $_key => $_value) {
          $_value = $this->cleanSpaces($_value);
          if ($_value == ":") {
            $data[$this->cleanSpaces($values[($_key - 1)])] = $this->cleanSpaces($values[($_key + 1)]);
          }

        }
      //dump($values);   
      }

    }



    return $data;
  }

  private function indiaMart($mail)
  {

    $text = $mail['body'];

    //if($this->from=="buyershelp+enq@indiamart.com"){

    return $this->indiaMartBuy($mail);
    //}
    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);


    $table = $finder->query('//table[@align="center"]');

    $tr = $table[0]->getElementsByTagName('tr')[0];

    $td = $tr->getElementsByTagName('td')[0];

    $tble = $tr->getElementsByTagName('table')[0];

    $requirements_tr = $tble->getElementsByTagName('tr')[5]->nodeValue;
    $requirements = preg_replace('/\s+/', ' ', trim($requirements_tr));

    $name_tr = $tble->getElementsByTagName('tr')[12]->nodeValue;
    dump($name_tr);
    $name = preg_replace('/\s+/', ' ', trim($name_tr));

    $location_tr = $tble->getElementsByTagName('tr')[13]->nodeValue;
    dump($location_tr);
    $location = preg_replace('/\s+/', ' ', trim($location_tr));

    $email_tr = $tble->getElementsByTagName('tr')[14]->nodeValue;
    $email = preg_replace('/\s+/', ' ', trim($email_tr));
    $email_first = strtok($email, ':');
    $email_last = substr($email, strpos($email, ":") + 2);


    $mobile_tr = $tble->getElementsByTagName('tr')[15]->nodeValue;
    $mobile = preg_replace('/\s+/', ' ', trim($mobile_tr));
    $mobile_first = strtok($mobile, ':');
    $mobile_last = substr($mobile, strpos($mobile, ":") + 2);




    $data = ['Name' => $name, $email_first => $email_last, $mobile_first => $mobile_last, 'Location' => $location, 'Requirements' => $requirements];


    return $data;
  }

  private function indiaMartBuy($mail)
  {
    $text = $mail['body'];
    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);



    preg_match('/(.*) has sent/', $text, $match);
    $name = "";
    if (count($match) == 2) {

      $name = $this->cleanFieldValue($match[1]);
    }

    // echo $text;
    preg_match('/<b>"(.*)"<\/b>/', $text, $match);
    $requirement = "";



    if (count($match) == 2) {

      $requirement = $this->cleanFieldValue($match[1]);
    }

    $data = ['Name' => $name, 'Requirement' => $requirement];

    if (preg_match('/Buy Lead/', $text)) {
      $data['lead_type'] = 'Buy Lead';
    }

    preg_match('/businessonthemove\.jpg/', $text, $template_match);



    if ($template_match) {

      $div = $finder->query('//div[@style="color:#000000;line-height:1.5em;font-family:Arial,Helvetica,sans-serif;font-size:12px;padding:8px; width:97%; clear:both"]');
      $regards_row = '';
      foreach ($div[0]->childNodes as $childNode) {

        $value = $this->cleanFieldValue($childNode->nodeValue);

        $check = explode(", ", $value);



        if (count($check) >= 3 && !isset($data['Location'])) {

          $data['Location'] = $this->cleanFieldValue(preg_replace("/[^a-zA-Z]+/", ' ', $check[count($check) - 3]));
          $data['City'] = $data['Location'];
          $data['State'] = $check[count($check) - 2];

          if (preg_match('/(.*) -/', $data['State'], $matches)) {
            if (isset($matches[1])) {

              $data['State'] = $matches[1];
            }
          }
        // $data['Country'] = $check[count($check)-1];
        }
        else {
          if ($data['Name'] == "" && $value != "") {
            $data['Name'] = $value;

          }

          if ($regards_row == "email" && !isset($data['Email'])) {
            $data['Email'] = $value;
            $regards_row = "2";
          }

          if (preg_match("/E-mail:/", $value)) {
            $regards_row = "email";
          }

          if ($regards_row == "phone" && !isset($data['Phone'])) {
            $data['Phone'] = $value;
            $regards_row = "2";
          }

          if (preg_match("/Click to call:/", $value)) {
            $regards_row = "phone";
          }
        }

      }

      $div = $finder->query('//div[@style="font-size:18px;line-height:18px;"]//strong');


      if ($div->length == 1) {
        $data['Requirement'] = $this->cleanFieldValue($div[0]->nodeValue);

      }

      $div = $finder->query('//div[@style="font-family:Arial,Helvetica,sans-serif;font-size:12px; line-height:1.5em; font-weight:normal; padding-top:8px"]');
      foreach ($div[0]->getElementsByTagName('tr') as $key => $value) {

        $data[$this->cleanFieldValue($value->getElementsByTagName('td')[0]->nodeValue)] = $this->cleanFieldValue($value->getElementsByTagName('td')[2]->nodeValue);

      }


    }
    else {

      $table = $finder->query('//table[@style="margin:auto;width:100%;color:rgb(0,0,0);line-height:17.5px;text-align:center"]');
      if (isset($table[0])) {

        foreach ($table[0]->getElementsByTagName('tr') as $key => $value) {

          $data[$this->cleanFieldValue($value->getElementsByTagName('td')[0]->nodeValue)] = $this->cleanFieldValue($value->getElementsByTagName('td')[2]->nodeValue);

        }
      }

      if ($template_match) {

        $table = $finder->query('//td[@style="font-family: arial, sans-serif; margin: 0px; line-height: 22.5px; vertical-align: top; text-align: left; font-size: 15px; padding-top: 5px; color: rgb(0, 0, 0)"]');
      }
      else {
        $table = $finder->query('//td[@style="font-family:arial,sans-serif;margin:0px;line-height:22.5px;vertical-align:top;text-align:left;font-size:15px;padding-top:5px;color:rgb(0,0,0)"]');
      }



      if ($table[0]) {

        $data['Requirements'] = $this->cleanFieldValue($table[0]->nodeValue);

      }

      if ($template_match) {
        $table = $finder->query('//table[@style="margin:15px auto auto;width:100%;color:rgb(0,0,0); border-top: 1px solid rgb(238, 238, 238);"]');
      }
      else {

        $table = $finder->query('//table[@style="margin:15px auto auto;width:100%;color:rgb(0,0,0);border-top:1px solid rgb(238,238,238)"]');

      }
      //



      if (!isset($table[0])) {
        $table = $finder->query('//table[@style="margin:15px auto auto;width:100%;color:rgb(0,0,0); border-top: 1px solid rgb(238, 238, 238);"]');

      //margin:15px auto auto;width:100%;color:rgb(0,0,0); border-top: 1px solid rgb(238, 238, 238);
      }
      // dump($table[0]);
      if (!isset($table[0])) {
        $table = $finder->query('//table[@style="margin:0px auto auto;width:100%;color:rgb(0,0,0);"]');

      //margin:15px auto auto;width:100%;color:rgb(0,0,0); border-top: 1px solid rgb(238, 238, 238);
      }

      // dump($table[0]);


      if (isset($table[0])) {


        $table = $table[0]->getElementsByTagName('table');
        // dd($table);
        if (isset($table[0])) {
          $regards_row = "";
          foreach ($table[0]->getElementsByTagName('tr') as $key => $value) {


            $dataValue = preg_replace('/\s+/', ' ', $this->cleanFieldValue($value->nodeValue));


            // dump($dataValue);
            if ($dataValue != "Buyer Details" && $dataValue != $data['Name']) {


              if ($regards_row == "1" && $data['Name'] == "") {
                $data['Name'] = $this->cleanFieldValue($dataValue);
                $regards_row = "2";
              }

              if (preg_match("/Regards/", $dataValue)) {
                $regards_row = "1";
              }



              $check = explode(", ", $dataValue);

              if (count($check) >= 3) {

                $data['Location'] = $this->cleanFieldValue(preg_replace("/[^a-zA-Z]+/", ' ', $check[count($check) - 3]));
                $data['City'] = $data['Location'];
                $data['State'] = $check[count($check) - 2];
                $data['Country'] = $check[count($check) - 1];
              }
              else {

                preg_match('/Email: (.*)/', $dataValue, $emailMatch);

                if ($emailMatch) {
                  preg_match('/Not Provided/', $dataValue, $emailNullMatch);



                  if (!$emailNullMatch) {

                    $data['Email'] = $emailMatch[1];
                  }

                }

                preg_match('/Mobile: (.*)/', $dataValue, $mobileMatch);

                if ($mobileMatch) {

                  $data['Phone'] = $mobileMatch[1];
                }
                else {
                  preg_match('/Click to call: (.*)/', $dataValue, $mobileMatch);

                  if ($mobileMatch) {

                    $data['Phone'] = $mobileMatch[1];
                  }
                }


              }


            }
          // $data[$this->cleanFieldValue($value->getElementsByTagName('td')[0]->nodeValue)]= $this->cleanFieldValue($value->getElementsByTagName('td')[2]->nodeValue);

          }
        }
      }

    }
    //

    if ($data['Name'] == "") {

      return;
    }


    return $data;
  }


  private function adflairMedia($mail)
  {

    $text = $mail['body'];

    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);


    $tables = $finder->query("//table");

    $data = [];
    foreach ($tables as $key => $node) {



      $rows = $node->getElementsByTagName('tr');
      foreach ($rows as $row) {

        if ($row->getElementsByTagName('td')->length == 2) {

          $data[$this->cleanFieldName($row->getElementsByTagName('td')[0]->nodeValue)] = $this->cleanFieldValue($row->getElementsByTagName('td')[1]->nodeValue);
        }
      }


    }

    return $data;
  }


  private function pharmaView($mail)
  {

    echo $text = $mail['body'];

    if ($mail['from'] == "pharmaviewinfo@gmail.com") {
      return $this->pharmaView2($mail);
    }

    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);


    $tables = $finder->query("//div");
    $fields = ['name', 'Firm Name', 'address', 'phone', 'email', 'query'];
    $data = [];
    $i = 0;
    foreach ($tables as $key => $node) {

      if ($node->getAttribute('dir') == "") {


        $index = $i;

        if ($index > 5) {
          $index = 5;
        }
        if (!isset($data[$fields[$index]])) {
          $data[$fields[$index]] = "";
        }
        $data[$fields[$index]] .= $node->nodeValue . ($index > 5 ? " " : "");
        $i++;

      }


    }

    dump($data);


    return;
    if (count($data) > 5) {

      $data['query'] = str_replace('Message- ', '', $data['query']);
    }
    else {
      return null;
    }

    return $data;
  }

  private function pharmaView2($mail)
  {

    $text = $mail['body'];
    $text = str_replace("Message- ", "", $text);

    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);


    preg_match('/>(.*)</', $text, $contactData);


    $data = [];

    $fields = ['name', 'Firm Name', 'address', 'phone', 'email', 'query'];

    $i = 0;
    if ($contactData) {

      if (preg_match("<br>", $contactData[1])) {
        $contactData = explode("<br>", $contactData[1]);






        foreach ($contactData as $key => $node) {




          $index = $i;

          if ($index > 5) {
            $index = 5;
          }
          if (!isset($data[$fields[$index]])) {
            $data[$fields[$index]] = "";
          }
          $data[$fields[$index]] .= strip_tags($node) . ($index > 5 ? " " : "");
          $i++;



        }
      }
      else {

        $tables = $finder->query("//div");

        $data = [];
        foreach ($tables as $key => $node) {

          if ($key > 0) {
            $index = $i;

            if ($index > 5) {
              $index = 5;
            }
            if (!isset($data[$fields[$index]])) {
              $data[$fields[$index]] = "";
            }
            $data[$fields[$index]] .= strip_tags($node->nodeValue) . ($index > 5 ? " " : "");
            $i++;
          }

        }
      }




    }

    // dump($data);
    if (isset($data['phone']) && isset($data['email'])) {

      $phone = $data['phone'];
      // dump($phone);
      $email = $data['email'];
      if (preg_match('/\@/', $phone)) {
        $data['phone'] = $email;
        $data['email'] = $phone;
      }
    }
    else {
      return [];
    }
    if (isset($data['query'])) {
      $data['query'] = str_replace('Message- ', '', $data['query']);
      $data['query'] = str_replace('Message - ', '', $data['query']);
    }
    // dump($data);
    dump($data);


    // return ;
    return $data;
  }

  private function pharmaMart($mail)
  {
    $text = $mail['body'];

    //  $text = '<div dir="ltr"><div dir="ltr"><div>Kumar Satyam</div><div>CHHAYA ENTERPRISES</div><div>Dumka, Jharkhand- 814101</div><div>7992379548</div><div><a href="mailto:satyamjaiswal1212@gmail.com">satyamjaiswal1212@gmail.com</a></div><div>Message-  For PCD</div></div></div>';
    //echo $text;
    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);



    $values = [];

    $text = str_replace("<br></div>", "</div>", $text);
    // echo $text;

    if (preg_match("/<br>/", $text)) {
      // dump("Here1");
      $text = str_replace("\r\n", "", $text);
      $br_content = $str = preg_replace('/\<[\/]{0,1}div[^\>]*\>/i', '', $text);

      $values = explode("<br>", $br_content);

    }
    else {
      // dump("Here2");
      $tables = $finder->query("//div");
      foreach ($tables as $key => $value) {

        if (($value->getAttribute('dir') != "ltr" && $value->nodeValue != "") || ($value->childNodes[0]->localName == null || $value->childNodes[0]->localName == 'a')) {
          // dump(['check',$value->getAttribute('dir')]);
          $values[] = $value->nodeValue;
        }
      }
    }


    // dump($values);
    if (empty($values) || count($values) <= 4) {
      return;
    }

    $data['name'] = $this->cleanFieldValue($values[0]);

    if (count($values) == 6 || count($values) == 7) {
      $data['Company'] = $this->cleanFieldValue($values[1]);
      $location = explode("- ", $values[2]);
      $data['city'] = $this->cleanFieldValue($location[0]);
      if (isset($location[1]))
        $data['state'] = $this->cleanFieldValue($location[1]);

      $data['email'] = strip_tags($this->cleanFieldValue($values[3]));
      $data['phone'] = strip_tags($this->cleanFieldValue($values[4]));
      $data['query'] = $this->cleanFieldValue(str_replace("Message- ", "", $values[5]));
    }
    else {
      // dump($values);
      $location = explode("- ", $values[1]);
      // dump( $location);
      $data['city'] = $this->cleanFieldValue($location[0]);
      if (isset($location[1]))
        $data['state'] = $this->cleanFieldValue($location[1]);
      $data['email'] = strip_tags($this->cleanFieldValue($values[2]));
      $data['phone'] = strip_tags($this->cleanFieldValue($values[3]));

      $data['query'] = $this->cleanFieldValue(str_replace("Message- ", "", $values[4]));
    }

    $location = explode(", ", $data['city']);

    if (count($location) == 2) {

      $data['location'] = $location[0];
      $data['city'] = $location[1];

    }
    else {
      $data['location'] = $location[0];
      $data['city'] = $location[0];
    }
    //  dump(filter_var($data['email'],FILTER_VALIDATE_EMAIL));
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      $phone = $data['email'];
      $data['email'] = $data['phone'];
      $data['phone'] = $phone;
    }


    // dump($data);

    return $data;


  }

  private function pharmaBusiness($mail)
  {

    $text = $mail['body'];
    $text = str_replace("\r\n", "", $text);

    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);

    $tables = $finder->query("//div");

    $values = [];
    foreach ($tables as $key => $value) {
      $row = explode(": ", $this->cleanFieldValue($value->nodeValue));

      if (count($row) == 2) {

        $values[$this->cleanFieldName($row[0])] = $row[1];
      }
    //if($value->getAttribute('tr'))
    //dump($value->getElementsByTagName('table'));
    /*foreach ($value->getAttribute('p') as  $val) {
     dump($val->nodeValue);
     }*/


    # code...
    }


    if (empty($values)) {

      preg_match('/<span>Name:(.*)E-mail:(.*)Address:(.*)Contact No.:(.*)Message:(.*)<\/span>/', $text, $matches);

      if (!$matches) {
        preg_match('/<span>Name:(.*)Email:(.*)Address:(.*)Contact No.:(.*)Message:(.*)<\/span>/', $text, $matches);
      }

      if (count($matches) == 6) {
        //{"Name":"name","Email":"email","Contact No.":"phone","State":"location","Address":"address","State":"state_id","Message":"query"}
        $values = [];
        $values['Name'] = $this->cleanFieldValue($matches[1]);
        $values['Email'] = $this->cleanFieldValue($matches[2]);
        $values['State'] = $this->cleanFieldValue($matches[3]);
        $values['Contact No.'] = $this->cleanFieldValue($matches[4]);
        $values['Message'] = $this->cleanFieldValue($matches[5]);

      }

    }
    //dd($values);
    return $values;
  }

  private function tradeXl($mail)
  {

    $text = $mail['body'];

    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);

    $tables = $finder->query("//table");
    $table = $finder->query('//table[@style="text-align:left;border-top-width:1px;border-top-style:solid;border-top-color:rgb(235,235,235);padding-top:5px;color:rgb(22,22,22);font-size:13px;font-family:arial,sans-serif"]');
    //



















    
//        dd($table[0]->nodeValue);

    if (!isset($table[0])) {
      return;
    }
    $tables = $table[0]->getElementsByTagName('tr');
    $values = [];
    foreach ($tables as $key => $value) {
      //echo "--------------------------------------------------------".$key."-------------------\n\n\n";
      $element = explode(" :", $this->cleanSpaces($value->nodeValue));

      // dump($element);
      $values[$element[0]] = $this->cleanSpaces((isset($element[1]) ? $element[1] : ""));

    }
    $location = [];

    if (isset($values['City / Location'])) {

      $area = explode(" / ", $values['City / Location']);

      $values['City'] = $area[0];
      $values['State'] = (isset($area[1]) ? $area[1] : "");

      $values['Country'] = (isset($area[2]) ? $area[2] : "");

      $values['Location'] = $area[0];

      unset($values['City / Location']);

    }
    dump($values);

    return $values;
  }

  private function pharmaFlair($mail)
  {

    $text = $mail['body'];


    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);


    $table = $finder->query('//div[@class="contact-form"]');
    //



    // dump($table[0]->nodeValue);

    if (!isset($table[0])) {
      return;
    }
    $tables = $table[0]->getElementsByTagName('p');
    $values = [];
    foreach ($tables as $key => $value) {
      // dump($value->nodeValue);
//echo "--------------------------------------------------------".$key."-------------------\n\n\n";
      $element = explode(" :", $this->cleanSpaces($value->nodeValue));

      // dump($element);
      if ($element[0] != "" && $element[0] != "Please find Below enquiry")
        $values[$element[0]] = $this->cleanSpaces((isset($element[1]) ? $element[1] : ""));

    }


    $location = [];

    if (isset($values['State'])) {

      /* $area = explode(" / ",$values['City / Location']);
       $values['City'] =  $area[0];
       $values['State'] =   (isset($area[1])?$area[1]:"");
       $values['Country'] =  (isset($area[2])?$area[2]:"");*/

      $values['Location'] = $values['State'];

    // unset($values['City / Location']);

    }

    return $values;
  }


  public function updateActivites()
  {
    echo "asdas";

    /*->whereHas('activities',function($query){
     $query->where('type','task')->where('status','Close')->whereNull('completed_date');
     })*/


    $leads = Lead::where('last_activity_id', '0')->orderBy('id', 'desc')->chunk(50, function ($leads) {


      foreach ($leads as $lead) {

        $last_activity = Activity::where('activity_type', 'App\Lead')->where('activity_id', $lead->id)->where('type', 'task')->where('status', 'Close')->whereNull('completed_date')->orderBy('id', 'desc')->first();
        dump($lead->id);
        if ($last_activity) {
          echo $lead->id;
          $lead->last_activity_id = $last_activity->id;

          $lead->save();
        }

      }



    });





  }

  private function updateTeam()
  {
    $boxes = Box::with('users')->where(function ($query) {
      $query->where('id', '3');
    })->get();

    foreach ($boxes as $box) {

      $teams = Team::where('box_id', $box->id)->get();

      if ($teams->count() == "0") {

        $team = new Team;
        $team->box_id = $box->id;
        $team->owner_id = $box->superadmin_id;
        $team->name = 'Owner';
        $team->save();

        $team2 = new Team;
        $team2->box_id = $box->id;
        $team2->owner_id = $box->superadmin_id;
        $team2->parent_id = $team->id;
        $team2->name = 'Manager';
        $team2->save();

        $team3 = new Team;
        $team3->box_id = $box->id;
        $team3->owner_id = $box->superadmin_id;
        $team3->parent_id = $team2->id;
        $team3->name = 'Executives';
        $team3->save();


      }
      $team_users = TeamUser::where('box_id', $box->id)->get();

      if ($team_users->count() == "0") {
        $users = $box->users();


        foreach ($box->users as $user) {
          dd($user->role);
        }

      }
      echo "ddd";
      exit;
    //dd($team_users);
    }


    echo "Done";

  }


  private function updateLeadColumns()
  {

    $cols = [['name' => 'Name', 'field' => 'name', 'is_visible' => '1'],
      ['name' => 'Email', 'field' => 'email', 'is_visible' => '1'],
      ['name' => 'Sec Email', 'field' => 'sec_email', 'is_visible' => '0'],
      ['name' => 'Phone', 'field' => 'phone', 'is_visible' => '1'],
      ['name' => 'Status', 'field' => 'lead_status', 'is_visible' => '1'],
      ['name' => 'Source', 'field' => 'leadsource', 'is_visible' => '1'],
      ['name' => 'Query', 'field' => 'query', 'is_visible' => '1'],
      ['name' => 'Designation', 'field' => 'designation', 'is_visible' => '0'],
      ['name' => 'IP Address', 'field' => 'ip_address', 'is_visible' => '0'],
      ['name' => 'Address', 'field' => 'address', 'is_visible' => '0'],
      ['name' => 'Postal Code', 'field' => 'postal_code', 'is_visible' => '0'],
      ['name' => 'Country', 'field' => 'country', 'is_visible' => '0'],
      ['name' => 'State', 'field' => 'state', 'is_visible' => '0'],
      ['name' => 'City', 'field' => 'city', 'is_visible' => '0'],
      ['name' => 'Assigned To', 'field' => 'assigned', 'is_visible' => '0'],
      ['name' => 'Added By', 'field' => 'added_by', 'is_visible' => '0'],
      ['name' => 'Last Updated By', 'field' => 'last_updated_by', 'is_visible' => '0'],
      ['name' => 'Last Updated Date', 'field' => 'updated_at', 'is_visible' => '0'],
      ['name' => 'Last Activity', 'field' => 'activity', 'is_visible' => '1'],
      ['name' => 'Location', 'field' => 'location', 'is_visible' => '1'],
      ['name' => 'Is Matured', 'field' => 'is_matured', 'is_visible' => '1'],
      ['name' => 'Matured Date', 'field' => 'matured_date', 'is_visible' => '1'],
      ['name' => 'Amount', 'field' => 'amount', 'is_visible' => '1'],
      ['name' => 'Last Activity Type', 'field' => 'activity_type', 'is_visible' => '0'],
      ['name' => 'Last Activity Status', 'field' => 'activity_status', 'is_visible' => '0'],
      ['name' => 'Lead Type', 'field' => 'lead_type', 'is_visible' => '0']
    ];

    try {

      $boxUsers = BoxUser::orderBy('id', 'asc')->chunk(50, function ($users) use ($cols) {


        foreach ($users as $user) {
          dump($user->user_id);

          foreach ($cols as $key => $col) {

            $column = LeadColumn::where('lead_columns.box_id', $user->box_id)->where('lead_columns.user_id', $user->user_id)->where('field_type', 'system')->where('field', $col['field'])->first();

            if (!$column) {
              $field = new LeadColumn;
              $field->box_id = $user->box_id;
              $field->user_id = $user->user_id;
              $field->field_type = 'system';
              $field->name = $col['name'];
              $field->field = $col['field'];
              $field->system_field_name = $col['field'];

              $field->is_visible = $col['is_visible'];
              $field->sequence = $key;
              $field->field_value_type = null;
              $field->save();
            }
          }


        }



      });

    }
    catch (\Exception $e) {
      dd([$e->getLine(), $e->getFile(), $e->getMessage()]);
    }
    echo "Done";
  }

  private function pharmafranchisekart_old($mail)
  {

    $text = $mail['body'];

    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);


    $tables = $finder->query("//table");

    $data = [];
    foreach ($tables as $key => $node) {



      $rows = $node->getElementsByTagName('tr');
      foreach ($rows as $row) {

        if ($row->getElementsByTagName('td')->length == 2) {

          $data[$this->cleanFieldName($row->getElementsByTagName('td')[0]->nodeValue)] = $this->cleanFieldValue($row->getElementsByTagName('td')[1]->nodeValue);
        }
      }


    }
    unset($data['Reach us for any help']);
    unset($data['Email info@rednirus.in']);

    return $data;
  }

  private function pharmafranchisekart($mail)
  {

    $text = $mail['body'];
    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);

    preg_match("/pharmafranchisemart_asdsad/", $text, $matchedBody);

    preg_match("/Pharmafranchiseeindia_oldddd/", $text, $matchedBodyIndia);

    // dump(str_replace("/","\/",$this->cleanFieldValue($temp_word)));

    // dump([$matchedBody,$matchedBodyIndia]);                   
    $data = [];

    if ($matchedBody) {
      $tables = explode("\n", $text);

      foreach ($tables as $key => $node) {


        $row = $node;

        if (preg_match('/:-/', $row)) {
          $row = explode(":-", $row);

          $data[$this->cleanFieldName($row[0])] = $this->cleanFieldValue($row[1]);
        }
        else if (preg_match('/:/', $row) && !preg_match('/This e-mail was sent from a contact form/', $row)) {
          $row = explode(":", $row);

          $data[$this->cleanFieldName($row[0])] = $this->cleanFieldValue($row[1]);


        }



      }
    }
    else if ($matchedBodyIndia) {
      $tables = $finder->query("//table");

      $data = [];
      foreach ($tables as $key => $node) {



        $rows = $node->getElementsByTagName('tr');
        foreach ($rows as $row) {
          // dump($row->getElementsByTagName('td')->length);
          if ($row->getElementsByTagName('td')->length == 3) {

            $key_val = $this->cleanFieldName($row->getElementsByTagName('td')[0]->nodeValue);
            $val = $this->cleanFieldValue($row->getElementsByTagName('td')[2]->nodeValue);

            if ($key_val != "" && $val) {

              $data[$key_val] = $val;
            }
          }
        }


      }
    }
    else {




      $tables = $finder->query("//table//table//table");
      $data = [];

      if ($tables->length == "0") {

        $tables = $finder->query("//table//table");
      }

      if ($tables->length == "0") {
        $tables = $finder->query("//table");

      }
      foreach ($tables as $key => $node) {

        if ($key > 0) {

          break;
        }



        $rows = $node->getElementsByTagName('tr');
        foreach ($rows as $row) {

          if ($row->getElementsByTagName('td')->length == 2) {

            $key_val = $this->cleanFieldName($row->getElementsByTagName('td')[0]->nodeValue);
            $val = $this->cleanFieldValue($row->getElementsByTagName('td')[1]->nodeValue);

            if ($key_val != "" && $val) {

              $data[$key_val] = $val;
            }
          }
          else if ($row->getElementsByTagName('td')->length == 3) {

            $key_val = $this->cleanFieldName($row->getElementsByTagName('td')[0]->nodeValue);
            $val = $this->cleanFieldValue($row->getElementsByTagName('td')[2]->nodeValue);

            if ($key_val != "" && $val) {

              $data[$key_val] = $val;
            }
          }
        }


      }


    }

    if (empty($data)) {
      // '{"Name":"name","Email Id":"email","Phone":"phone","City":"location","Message":"query"}'

      $mapper = json_decode('{"Name":"Contact Person","Email":"E-Mail Address","Mobile":"Telphone","Subject":"City","Message":"Requirements"}', true);
      $rows = explode("\n", $text);
      foreach ($rows as $row) {
        $row = explode(":-", $row);

        if (count($row) == 2) {

          $key_val = ucwords($this->cleanFieldName($row[0]));
          $val = $this->cleanFieldValue($row[1]);

          if ($key_val == "Email") {
            $key_val = "Email Id";
          }
          if ($key_val != "" && $val) {

            $data[$key_val] = $val;
          }
        }
      }


    }

    unset($data['Message Body']);
    unset($data['From']);


    // dd($data);
    return $data;
  }

  private function pharmainfotech($mail)
  {

    $text = $mail['body'];

    $text = explode("\n", str_replace("\t", "", $text));
    $data = [];
    foreach ($text as $row) {

      $rowData = explode(" -", str_replace("&nbsp;", " ", htmlentities(strip_tags($row))));

      if (count($rowData) == 2) {
        $data[$this->cleanFieldName($rowData[0])] = $this->cleanFieldValue($rowData[1]);
      }
    }

    return $data;
  }

  private function dawacharcha($mail)
  {

    $text = $mail['body'];
    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);


    $tables = $finder->query("//span");
    $data = [];
    $values = [];
    foreach ($tables as $key => $value) {
      //dump($value->getAttribute('dir'));
      if ($value->getAttribute('dir') != "ltr" && $value->nodeValue != "") {
        // dump(['check',$value->getAttribute('dir')]);
        $values[] = $this->cleanFieldValue($value->nodeValue);
      }

    }




    foreach ($values as $row) {

      $rowData = explode(": ", str_replace("&nbsp;", " ", htmlentities(strip_tags($row))));

      if (count($rowData) == 2) {
        $data[$this->cleanFieldName($rowData[0])] = $this->cleanFieldValue($rowData[1]);
      }
    }

    if (!isset($data['Name'])) {
      // $data = [];
      $values = explode("<br>", str_replace("\t", "", $text));

      if (count($values) == 1) {
        $values = explode("<br style=\"\">", str_replace("\t", "", $text));
      }
      foreach ($values as $row) {

        $rowData = explode(": ", str_replace("&nbsp;", " ", htmlentities(strip_tags($row))));

        if (count($rowData) == 2) {
          $data[$this->cleanFieldName($rowData[0])] = html_entity_decode($this->cleanFieldValue($rowData[1]));
        }
      }

      unset($data['E-mail']);
    }



    $div = $finder->query('//a');
    foreach ($div as $key => $value) {
      if ($value->getAttribute('href') == "mailto:" . $value->nodeValue) {

        $data['Email'] = $value->nodeValue;
      }

    }


    return $data;
  }


  private function pharmavends($mail)
  {

    $text = $mail['body'];

    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);


    $tables = $finder->query("//table");

    $data = [];
    foreach ($tables as $key => $node) {



      $rows = $node->getElementsByTagName('tr');
      foreach ($rows as $row) {

        if ($row->getElementsByTagName('td')->length == 2) {

          $key_val = $this->cleanFieldName($row->getElementsByTagName('td')[0]->nodeValue);
          $val = $this->cleanFieldValue($row->getElementsByTagName('td')[1]->nodeValue);

          if ($key_val != "" && $val) {

            $data[$key_val] = $val;
          }
        }
      }


    }

    if (isset($data['City'])) {
      $data['Location'] = $data['City'];
    }
    // dd($data);
    return $data;
  }



  private function pharmafranchiseclub($mail)
  {

    $text = $mail['body'];

    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);


    $tables = $finder->query("//table");

    $data = [];
    foreach ($tables as $key => $node) {



      $rows = $node->getElementsByTagName('tr');
      foreach ($rows as $row) {

        if ($row->getElementsByTagName('td')->length == 2) {

          $data[$this->cleanFieldName($row->getElementsByTagName('td')[0]->nodeValue)] = $this->cleanFieldValue($row->getElementsByTagName('td')[1]->nodeValue);
        }
      }


    }


    if (!isset($data['Contact Person']) && isset($data['Name'])) {

      $mapper = json_decode('{"Name":"Contact Person","Email":"E-Mail Address","Mobile":"Telphone","Subject":"City","Message":"Requirements"}', true);

      foreach ($mapper as $key => $val) {

        if (isset($data[$key])) {
          $data[$val] = $data[$key];
          unset($data[$key]);
        }
      }

    }

    return $data;
  }

  private function pharmabuffet($mail)
  {

    $text = $mail['body'];


    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);


    $tables = $finder->query("//table");

    $data = [];
    foreach ($tables as $key => $node) {



      $rows = $node->getElementsByTagName('tr');
      foreach ($rows as $row) {

        if ($row->getElementsByTagName('td')->length == 2) {

          $data[$this->cleanFieldName($row->getElementsByTagName('td')[0]->nodeValue)] = $this->cleanFieldValue($row->getElementsByTagName('td')[1]->nodeValue);
        }
      }


    }

    return $data;
  }

  private function trueWeb($mail)
  {

    $text = $mail['body'];
    // echo $text; exit;
    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);


    $tables = $finder->query("//table");


    $data = [];
    foreach ($tables as $key => $node) {



      $rows = $node->getElementsByTagName('tr');
      foreach ($rows as $row) {

        if (!preg_match('/Congratulations/', $this->cleanFieldName($row->getElementsByTagName('td')[0]->nodeValue))) {
          $key_name = "";
          $key_val = "";
          if ($row->getElementsByTagName('td')->length == 3) {

            $key_name = $this->cleanFieldName($row->getElementsByTagName('td')[0]->nodeValue);
            $key_val = $this->cleanFieldValue($row->getElementsByTagName('td')[2]->nodeValue);
          }
          elseif ($row->getElementsByTagName('td')->length == 2) {

            $key_name = $this->cleanFieldName($row->getElementsByTagName('td')[0]->nodeValue);
            $key_val = $this->cleanFieldValue($row->getElementsByTagName('td')[1]->nodeValue);
          }

          if ($key_name != "" && $key_val) {

            if (in_array($key_name, ['Phone', 'Email'])) {
              if ($key_val == "*********") {
                $key_val = "";
              }
            }


            $data[$key_name] = $key_val;
          }



        }
      }


    }

    if (isset($data['City / State'])) {

      $loc_data = explode('/', $data['City / State']);

      if (isset($loc_data[0])) {
        $data['City'] = $loc_data[0];
      }

      if (isset($loc_data[1])) {
        $data['State'] = $loc_data[1];
      }
    // unset($data['City / State']);
    }

    // dd($data);
    return $data;
  }

  private function justdial($mail)
  {

    $text = $mail['body'];
    // echo $text; exit;
    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);


    $tables = $finder->query("//table//table");


    $data = [];
    foreach ($tables as $key => $node) {



      $rows = $node->getElementsByTagName('tr');
      foreach ($rows as $row) {

        if (!preg_match('/Congratulations/', $this->cleanFieldName($row->getElementsByTagName('td')[0]->nodeValue))) {
          $key_name = "";
          $key_val = "";
          if ($row->getElementsByTagName('td')->length == 1) {
            $key_val = str_replace("\n", ' ', $row->getElementsByTagName('td')[0]->nodeValue);
            $key_val = $this->cleanFieldName($key_val);
            if (preg_match("/inquired for your business/", $key_val)) {
              $key_val = explode(" inquired for your business", $key_val);
              $key_val = $key_val[0];
              $key_name = "Name";
            }

          }
          if ($row->getElementsByTagName('td')->length == 2) {

            $key_name = str_replace("\n", ' ', $row->getElementsByTagName('td')[0]->nodeValue);

            $key_name = $this->cleanFieldName($key_name);
            if ($key_name != "Search Date & Time") {
              $value = str_replace("\n", ' ', $row->getElementsByTagName('td')[1]->nodeValue);
              $key_val = $this->cleanFieldValue($value);

            }

          }

          if ($key_name != "" && $key_val) {
            $data[$key_name] = $key_val;
          }



        }
      }


    }

    return $data;
  }

  private function nexttechmart($mail)
  {

    $text = $mail['body'];
    // echo $text; exit;
    $dom = new DomDocument();
    libxml_use_internal_errors(true);
    $dom->formatOutput = true;
    $dom->preserveWhiteSpace = false;
    $dom->loadHTML($text);
    libxml_use_internal_errors(false);
    $finder = new DomXPath($dom);


    $tables = $finder->query("//table//table");


    $data = [];
    foreach ($tables as $key => $node) {



      $rows = $node->getElementsByTagName('tr');
      foreach ($rows as $row) {

        if (!preg_match('/Congratulations/', $this->cleanFieldName($row->getElementsByTagName('td')[0]->nodeValue))) {
          $key_name = "";
          $key_val = "";
          if ($row->getElementsByTagName('td')->length == 1) {
            $key_val = str_replace("\n", ' ', $row->getElementsByTagName('td')[0]->nodeValue);
            $key_val = $this->cleanFieldName($key_val);
            if (preg_match("/inquired for your business/", $key_val)) {
              $key_val = explode(" inquired for your business", $key_val);
              $key_val = $key_val[0];
              $key_name = "Name";
            }

          }
          if ($row->getElementsByTagName('td')->length == 2) {

            $key_name = str_replace("\n", ' ', $row->getElementsByTagName('td')[0]->nodeValue);

            $key_name = $this->cleanFieldName($key_name);
            if ($key_name != "Search Date & Time") {
              $value = str_replace("\n", ' ', $row->getElementsByTagName('td')[1]->nodeValue);
              $key_val = $this->cleanFieldValue($value);

            }

          }

          if ($key_name != "" && $key_val) {
            $data[$key_name] = $key_val;
          }



        }
      }


    }

    return $data;
  }

  private function revertChanges()
  {


    $logs = DB::table('audits')->where('user_id', '105')->where('auditable_type', 'App\\Lead')
      ->where('created_at', '>=', '2022-02-02 15:01:35')->where('url', 'console')
      ->orderBy('id', 'asc')->get();
    // dd($logs);

    foreach ($logs as $log) {
      dump($log);
      $old_values = json_decode($log->old_values);
      dump($old_values);
      $lead = Lead::find($log->auditable_id);
      if ($lead) {

        foreach ($old_values as $key => $old_value) {
          // dump(["Old",$key,$lead->$key,$old_value]);
          if ($lead->$key == null) {
            $lead->$key = $old_value;

          }

        // dump(["New",$key,$lead->$key,$old_value]);

        }

        $lead->save();
      // exit;
      }


    }
    dump("Done");

  }
}
