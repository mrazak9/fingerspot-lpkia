<?php

use App\Employee;
use App\ScanLog;
use App\UserAttlog;
use GuzzleHttp\Client;

function attLogsUser($data)
{
    // return $data;
    $apitoken = env('FINGERSPOT_API_TOKEN');
    $cloudid = env('FINGERSPOT_CLOUD_ID');
    $url = env('FINGERSPOT_URL');
    $dataBody = array(
        'trans_id' => 1,
        'cloud_id' => $cloudid,
        'start_date' => $data['start_date'],
        'end_date' => $data['end_date'],
    );
    $client = new Client(['verify' => false]);
    $r = $client->request('POST', $url, [
        'headers' => [
            'Authorization' => 'Bearer ' . $apitoken,
        ],
        'body' => json_encode($dataBody),
    ]);
    if ($r->getStatusCode() == 200) {
        $response = $r->getBody()->getContents();
        $scans = json_decode($response, true);
        // $scanlogs = ScanLog::whereBetween('scan', [$data['start_date'], $data['end_date']])->get();
        if ($data['end_date'] === $data['end_date']) {
            foreach ($scans['data'] as $key => $value) {
                $scan = strtotime($value['scan_date']);
                $date = date('Y-m-d', $scan);
                $time = date('H:i:s', $scan);

                $scans = ScanLog::where('pin', $value['pin'])->where('scan', $value['scan_date'])->get();

                $pin = 'lpkia-' . $value['pin'];
                $myPin = explode('-', $pin);
                if ($myPin[1] === "050") {
                    $realPin = "1" . $myPin[1];
                } else {
                    $realPin = $value['pin'];
                }

                if ($scans->count() === 0) {
                    // $a +=1;
                    $dataEmploye = Employee::where('pin', '=', $realPin)->get();
                    if (count($dataEmploye) !== 0) {
                        $attlogEmploye = [
                            'pin' => $realPin,
                            'name' => $dataEmploye[0]['name'],
                            'scan_date' => $date,
                            'scan_time' => $time,
                        ];
                        UserAttlog::create($attlogEmploye);
                    } else {
                        $attlogEmploye = [
                            'pin' => $value['pin'],
                            'name' =>  $value['pin'],
                            'scan_date' => $date,
                            'scan_time' => $time,
                        ];
                        UserAttlog::create($attlogEmploye);
                    }
                    $attlogData = [
                        'pin' => $value['pin'],
                        'scan' => $value['scan_date'],
                        'verify' => $value['verify'],
                        'status_scan' => $value['status_scan'],
                    ];
                    ScanLog::create($attlogData);
                }
            }
        }        
    } 
}
