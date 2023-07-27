<?php

namespace App\Imports;

use App\Models\JamKurang;
use App\Models\Lembur;
use App\Models\logAbsen;
use App\Models\lebihKerja;
use App\Models\User;
use App\Models\Rules;
use App\Models\logKegiatan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Traits\jamKeInt;

class LogAbsenImport implements ToCollection
{
    use jamKeInt;

    public function getLamaKerja(){
        $rules = Rules::where('key', "lama_kerja")->first();
        $lamaKerja = $rules["value"];
        return $lamaKerja;
    }

    public function getBatasKerja(){
        $rules = Rules::where('key', "batas_waktu")->first();
        $batasKerja = $rules["value"];
        return $batasKerja;
    }

    public function setBatasWaktu($batas){
		$this->batas = $batas;
	}
    public function getBatasWaktu() {
		return $this->batas; 
    }
    public function setBatasKerja($batasKerja){
		$this->batasKerja = $batasKerja;
	}
    // public function getBatasKerja() {
	// 	return $this->batasKerja; 
    // }
    public function setLog($kegiatan){
		$this->kegiatan = $kegiatan;
	}
    public function getLog() {
		return $this->kegiatan; 
	}
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        //dd($rows);
        foreach ($rows as $row) {
            $time = strtotime($row[3]);
            $newformat = date('Y-m-d',$time);
            $keluar = $this->timeToInteger($row[5]);
            if($keluar == null){
                dd($row);
            }
            $masuk = $row[4];
            
            $total = ($keluar - $this->timeToInteger($masuk));
            //coba
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
            //---
            $batas = $this->getBatasWaktu();
            $lamaBekerja = $this->getLamaKerja();
            $lamaBekerja = $lamaBekerja * 60;
            if ($total < $lamaBekerja)  {
                $statusTerlambat = true;
            }else{
                $statusTerlambat = false;
            }
            
            logAbsen::create([
                'id' => $row[0],
                'users_id' => $row[1],
                'tanggal' => $newformat,
                'jam_masuk' => $masuk,
                'jam_keluar' => $row[5],
                'total_jam' => $totalWaktu,
                'keterlambatan'=> $statusTerlambat,
            ]);

            if ($total >= ((int)$this->getLamaKerja())*3600){ //28800 = 8 jam
                $lembur = Lembur::where('users_id', $row[1])->where('tanggal', $newformat)->first();                

                $jamMasuk = $this->timeToInteger($row[4])/60;
                $jamKeluar = $this->timeToInteger($row[5])/60;
                if($lembur != null){
                    $jamAwalLembur = $this->timeToInteger($lembur->jam_awal)/60;
                    $jamAkhirLembur = $this->timeToInteger($lembur->jam_akhir)/60;
                }


                //UBAH KE YANG BARU---------------------------------------------------------
                $totalLebih = ($jamKeluar-$jamMasuk)-(((int)$this->getLamaKerja())*60);
                $jamKerjaLebih = $totalLebih;
                $lebihForLembur = $totalLebih;
                // --------------------------------------------------------------------------------------------------
                if($lembur != null){
                    $jamMasukKantor = $this->getBatasKerja();
                    $jamMasukKantor = $this->timeToInteger($jamMasukKantor)/60;
                    if($lembur->status == 1 && $lembur->status_kerja == 1){
                        if($jamAwalLembur > ($jamMasuk+(((int)$this->getLamaKerja())*60))){
                            $masukLebih1 = ($jamAwalLembur - ($jamMasuk+(((int)$this->getLamaKerja())*60)));//dari selesai jam kerja hingga jam awal lembur
                            $lebihForLembur = $lebihForLembur - ($masukLebih1+$lembur->jumlah_jam);
                            if($lebihForLembur <= 0){
                                $lebihForLembur = 0;
                            }

                            $totalJamForLebih = $masukLebih1 + $lebihForLembur;
                        }

                        if($jamAwalLembur <= $jamMasukKantor){
                            $lebih1 = ($jamAwalLembur - $jamMasuk);

                            if($lebih1 < 0){
                                $lebih1 = 0;
                            }

                            if($jamAkhirLembur > $jamMasuk){
                                $lebih2 = $jamMasukKantor - $jamAkhirLembur;
                            }else{
                                $lebih2 = $jamAkhirLembur - $jamMasukKantor;
                            }
                            
        
                            if($lebih2 < 0){
                                $lebih2 = 0;
                            }

                            $lebihForLembur = $lebihForLembur - ($lebih1 + $lebih2 + $lembur->jumlah_jam);

                            if($lebihForLembur < 0){
                                $lebihForLembur = 0;
                            }

                            $totalJamForLebih = $lebih1 + $lebih2 + $lebihForLembur;
                        }
                        

                    }else{
                        $totalJamForLebih = $totalLebih;  
                    }
                }else{
                    $totalJamForLebih = $totalLebih;
                }
                //----------------------------------------------------------------------------------------------------------------
                // lebih untuk user
                //$totalJamForLebih = $totalLebih;
            
                //----------------------------------------------------------------------------------------------------------------
                //kodingan dibawah untuk tambah data ke lebihKerja
                $totalJamLebih = $jamKerjaLebih/60;
                $totalJamLebih = (int)$totalJamLebih;
            
                $totalMenitLebih = ($jamKerjaLebih%60);
            
                if ($totalJamLebih / 10 < 1){
                    $totalJamLebih = "0".$totalJamLebih;
                }
            
                if ($totalMenitLebih / 10 < 1){
                    $totalMenitLebih = "0".$totalMenitLebih;
                }
            
                $jamKerjaLebih = $totalJamLebih.":".$totalMenitLebih;

                lebihKerja::create([
                    'users_id' => $row[1],
                    'absen_id' => $row[0],
                    'total_jam' => $jamKerjaLebih,
                ]);
                //----------------------------------------------------------------------------------------------------------------

                $newValue = $totalJamForLebih;
                

                // User::where('id', $row[1])->update(['jam_lebih' => $newValue]);

                $user = User::find($row[1]);
                $user->jam_lebih = $user->jam_lebih + $newValue;
                $user->save();
                //UBAH KE YANG BARU---------------------------------------------------------

            }
            
            if ($total < ((int)$this->getLamaKerja())*3600){
                $totalKurang = ((int)$this->getLamaKerja())*3600 - $total;
                $newValue = $totalKurang/60;

                $totalJamForKurang = $totalKurang;
            
                $totalJamForKurang = $totalKurang/3600;
                $totalJamForKurang = (int)$totalJamForKurang;
            
                $totalMenitKurang = ($totalKurang%3600)/60;
            
                if ($totalJamForKurang / 10 < 1){
                    $totalJamForKurang = "0".$totalJamForKurang;
                }
            
                if ($totalMenitKurang / 10 < 1){
                    $totalMenitKurang = "0".$totalMenitKurang;
                }
            
                $totalKurang = $totalJamForKurang.":".$totalMenitKurang;

                JamKurang::create([
                    'users_id' => $row[1],
                    'absen_id' => $row[0],
                    'total_jam_kurang' => $totalKurang,
                ]);

                $user = User::find($row[1]);
                $user->jam_kurang = $user->jam_kurang + $newValue;
                $user->save();
            }
            
        }
        

        if (Auth::check())
                {
                    date_default_timezone_set("Asia/Jakarta");
                    $id = Auth::id();
                    $date = date("Y-m-d h:i:sa");
                    $text = 'Melakukan Import Excel';
                    $logKegiatan = new logKegiatan;
                    $logKegiatan->users_id = $id;
                    $logKegiatan->kegiatan = $text;
                    $logKegiatan->created_at = $date;
                    $logKegiatan->save();
                }
        
    }

}
