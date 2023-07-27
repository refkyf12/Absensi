<?php

namespace App\Http\Controllers;

use App\Models\absenNonKerja;
use Illuminate\Http\Request;
use App\Models\liburNasional;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

use Exception;
use App\lib\TAD;
use App\Http\Controllers\_DIR_;
use App\Models\User;
use App\Models\Rules;
use App\Traits\jamKeInt;
use App\Http\Controllers\TADFactory;

class AbsenNonKerjaController extends Controller
{
    use jamKeInt;
    /**
     * Display a listing of the resource.
     */
    public function getBatasWaktu(){
        $rules = Rules::where('key', "batas_waktu")->first();
        $batas = $rules["value"];
        return $batas;
    }

    public function logAbsenNonKerja(Request $request)
    {
        try{
            $logger = new Logger('soap-service');
        $tad = (new TADFactory((['ip'=> '10.50.0.60', 'com_key'=>0])))->get_instance();
        echo 'starting read data in machine finger print ..'. getenv('IP_MESIN_ABSEN') . "<br/>";
        $logs = $tad->get_att_log();
        $data = $logs->to_json();

        $conv = json_decode($data, true);

        $yesterday = date('Y-m-d', strtotime("-1 days"));
        $absensi = [];

        // Ambil data dari tabel libur_nasional
        $tanggalLibur = liburNasional::pluck('tanggal')->toArray();

        foreach ($conv['Row'] as $data) {
            $id = $data['PIN'];
            $datetime = $data['DateTime'];
            $tanggalLog = urldecode(date("Y-m-d", strtotime($data['DateTime'])));

            // Periksa apakah tanggal log merupakan hari Sabtu, Minggu, atau tanggal libur
            if (in_array($tanggalLog, $tanggalLibur) || date('N', strtotime($tanggalLog)) >= 6) {
                if ($tanggalLog == $yesterday) {
                    if (isset($absensi[$id])) {
                        if ($datetime > $absensi[$id]['jam_terakhir']) {
                            $absensi[$id]['jam_terakhir'] = $datetime;
                        }
                    } else {
                        $absensi[$id] = [
                            'id_karyawan' => $id,
                            'jam_pertama' => $datetime,
                            'jam_terakhir' => $datetime
                        ];
                    }
                }
            }
        }

        foreach ($absensi as $item) {
            $logAbsen = new absenNonKerja;
            $jamAwal = urldecode(date("H:i", strtotime($item['jam_pertama'])));
            $jamAkhir = urldecode(date("H:i", strtotime($item['jam_terakhir'])));
            if ($logAbsen->users_id != $item['id_karyawan']) {
                $logAbsen->users_id = $item['id_karyawan'];
                $logAbsen->tanggal = $yesterday;
                $logAbsen->jam_masuk = $jamAwal;
                $logAbsen->jam_keluar = $jamAkhir;
                $total = ($this->timeToInteger($jamAkhir) - $this->timeToInteger($jamAwal));

                $totaljam = $total/3600;
                $totaljam = (int)$totaljam;
                $totalmenit = ($total%3600)/60;

                

                if ($totaljam / 10 < 1){
                    $totaljam = "0".$totaljam;
                }
        
                if ($totalmenit / 10 < 1){
                    $totalmenit = "0".$totalmenit;
                }
        
                $totalWaktu = $totaljam.":".$totalmenit;

                $batas = $this->getBatasWaktu();
                if ($this->timeToInteger($jamAwal) > $this->timeToInteger($batas))  {
                    $statusTerlambat = true;
                }else{
                    $statusTerlambat = false;
                }

                $logAbsen->total_jam = $totalWaktu;
                $logAbsen->keterlambatan = $statusTerlambat;
                $logAbsen->save();
            }
        }
        return redirect('/absen_non_kerja')->with('success', 'Berhasil mengambil data');
        }catch(Exception $e){
            $errorMessage = $e->getMessage();
            return redirect('/absen_non_kerja')->with('error', 'Gagal mengambil data. Error : ' . $errorMessage );
        }
        
        
    }
    public function index()
    {
        $log_absen = absenNonKerja::all();
        if(request()-> segment(1) =='api') return response()->json([
            "error"=> false,
            "list" => $log_absen,
        ]);
		return view('logAbsenNonKerja.index',['data'=>$log_absen]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(absenNonKerja $absenNonKerja)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(absenNonKerja $absenNonKerja)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, absenNonKerja $absenNonKerja)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(absenNonKerja $absenNonKerja)
    {
        //
    }
}
