<?php

namespace App\Http\Controllers;

use App\Employee;
use App\ScanLog;
use App\UserAttlog;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserAttlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if (!empty($request->from_date)) {

                //Jika tanggal awal(from_date) hingga tanggal akhir(to_date) adalah sama maka
                if ($request->from_date === $request->to_date) {
                    //kita filter tanggalnya sesuai dengan request from_date
                    $scan_logs = UserAttlog::whereDate('scan_date', '=', $request->from_date)->get();
                } else {
                    //kita filter dari tanggal awal ke akhir
                    $scan_logs = UserAttlog::whereBetween('scan_date', array($request->from_date, $request->to_date))->get();
                }
            }
            //load data default
            else {
                $scan_logs = UserAttlog::all();
            }
            return datatables()->of($scan_logs)->make(true);
        }
        return view('scanlog');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $end_date = date('Y-m-d');
        $start_date = date('Y-m-d');

        $apitoken = env('FINGERSPOT_API_TOKEN');
        $cloudid = env('FINGERSPOT_CLOUD_ID');
        $url = env('FINGERSPOT_URL');
        $dataBody = array(
            'trans_id' => 1,
            'cloud_id' => $cloudid,
            'start_date' => "2022-09-03",
            'end_date' => "2022-09-03",
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
            $scanlogs = ScanLog::whereBetween('scan_date', [$start_date, $end_date])->get();
            if ($start_date === $end_date) {
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
            // return $a;
            return view('syncAll', [
                'scanlogs' => $scanlogs['data'],
            ]);
        } else {
            return "ahhh";
        }
    }

    // public function create()
    // {
    //     attLogsUser([
    //         'start_date' => '2022-08-31',
    //         'end_date' => '2022-08-31',
    //     ]);
    //     $scanlogs = UserAttlog::all();
    //     return view('syncAll', [
    //         'scanlogs' => $scanlogs,
    //     ]);
    // }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function synchronize(Request $request)
    {
        // return 'masuk';
        $rules = [
            'date' => 'required|string',
        ];

        $data = $request->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $end_date = $request->date;
        $start_date = $request->date;

        $apitoken = env('FINGERSPOT_API_TOKEN');
        $cloudid = env('FINGERSPOT_CLOUD_ID');
        $url = env('FINGERSPOT_URL');
        $dataBody = array(
            'trans_id' => 1,
            'cloud_id' => $cloudid,
            'start_date' => $end_date,
            'end_date' =>  $start_date,
        );

        $client = new Client(['verify' => false]);

        $r = $client->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $apitoken,
            ],
            'body' => json_encode($dataBody),
        ]);
        $a = 0;
        if ($r->getStatusCode() == 200) {
            
            $response = $r->getBody()->getContents();
            $scans = json_decode($response, true);

            if ($start_date === $end_date) {
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
                        $a += 1;
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

        return response()->json(['status' => 'success', 'data' => 'data berhasil di simpan : ' . $a]);
    }
}
