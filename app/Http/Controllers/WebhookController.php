<?php

namespace App\Http\Controllers;

use App\Employee;
use App\ScanLog;
use App\UserAttlog;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function fingerspotHandler(Request $request)
    {
        $data = $request->all();
        // return $data;

        $type = $data['type'];
        $cloudId = $data['cloud_id'];

        $myCloudId = 'C260503403280925';

        if ($type === 'attlog') {
            $scan = strtotime($data['data']['scan']);
            $date = date('Y-m-d', $scan);
            $time = date('H:i:s', $scan);

            $pin = 'lpkia-' . $data['data']['pin'];
            $myPin = explode('-', $pin);
            if ($myPin[1] === "050") {
                $realPin = "1" . $myPin[1];
            } else {
                $realPin = $data['data']['pin'];
            }
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
                    'pin' => $data['data']['pin'],
                    'name' =>  $data['data']['pin'],
                    'scan_date' => $date,
                    'scan_time' => $time,
                ];
                UserAttlog::create($attlogEmploye);
            }

            if ($cloudId !== $myCloudId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'invalid cloud id'
                ], 400);
            }

            $attlogData = [
                'pin' => $data['data']['pin'],
                'scan' => $data['data']['scan'],
                'verify' => $data['data']['verify'],
                'status_scan' => $data['data']['status_scan'],
            ];
            ScanLog::create($attlogData);

            return response()->json([
                'status' => 'Success',
                'data' => $attlogEmploye
            ]);
        } elseif ($type === 'get_userinfo') {
            $userInfoData = [
                'pin' => $data['data']['pin'],
                'name' => $data['data']['name'],
            ];
            Employee::create($userInfoData);

            return response()->json([
                'status' => 'Success',
                'data' => $userInfoData
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'attlog not found'
            ], 404);
        }
    }
}
