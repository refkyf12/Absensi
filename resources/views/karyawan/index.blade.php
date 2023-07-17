@extends('layouts.master')
 
@section('content')
 
<div class="row">
    <div class="col-md-12">
        <h4>Karyawan</h4>
        <div class="box box-warning">
            <div class="box-header">
                <p>
                    <button class="btn btn-sm btn-flat btn-warning btn-refresh"><i class="fa fa-refresh"></i> Refresh</button>
                    <a href="/karyawan/create" class="btn btn-sm btn-flat btn-primary"><i class="fa fa-plus"></i> Tambah Data</a>
                </p>
            </div>
            <div class="box-body">
               
                <div class="table-responsive">
                    <table class="table table-hover myTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>User ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Sisa Cuti</th>
                                <th>Jam Lebih (Menit) </th>
                                <th>Jam Kurang (Menit) </th>
                                <th>Jam Lembur (Menit) </th>
                                <th class="not-export-col">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $e=>$dt)
                                <tr>
                                <td>{{ $e+1 }}</td>
                                <td>{{ $dt->id }}</td>
                                <td>{{ $dt->nama }}</td>
                                <td>{{ $dt->email }}</td>
                                @if($dt->role == 0)
                                <td>Karyawan</td>
                                @endif
                                @if($dt->role == 1)
                                <td>Admin</td>
                                @endif
                                @if($dt->role == 2)
                                <td>Project Manager</td>
                                @endif
                                <td>{{ $dt->sisa_cuti }}</td>

                                @if($dt->jam_lebih == null || $dt->jam_lebih == 0)
                                <td>Tidak pernah lebih</td>
                                @endif
                                @if($dt->jam_lebih != null && $dt->jam_lebih >= 0)
                                <td>{{ sprintf("%02d Jam %0 2d Menit", intdiv($dt->jam_lebih, 60), $dt->jam_lebih%60 )}}</td>

                                @endif

                                @if($dt->jam_kurang == null || $dt->jam_kurang == 0)
                                <td>Tidak pernah kurang</td>
                                @endif
                                @if($dt->jam_kurang != null && $dt->jam_kurang >= 0)
                                <td>{{ sprintf("%02d Jam %02d Menit", intdiv($dt->jam_kurang, 60), $dt->jam_kurang%60 )}}</td>
                                @endif

                                @if($dt->jam_lembur == null || $dt->jam_lembur == 0)
                                <td>Tidak pernah lembur</td>
                                @endif
                                @if($dt->jam_lembur != null && $dt->jam_lembur >= 0)
                                <td>{{ sprintf("%02d Jam %02d Menit", intdiv($dt->jam_lembur, 60), $dt->jam_lembur%60 )}}</td>
                                @endif                
                                
                                <td>
                                    <div style="width:60px">
                                        <a href="/karyawan/{{$dt->id}}" class="btn btn-warning btn-xs btn-edit" id="edit"><i class="fa fa-pencil-square-o"></i></a>
                                        @if($dt->jam_lembur != null && $dt->jam_lembur*60 <= $dt->jam_lebih)
                                        <a href="/kurang/{{$dt->id}} "class="btn btn-danger btn-xs btn-hapus" id="delete"><i class="fa fa-trash-o"></i></a> 
                                        @endif                                   
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <a href="/reset" class="btn btn-sm btn-flat btn-Danger"><i class="fa fa-trash-o"></i> RESET</a>

            </div>
        </div>
    </div>
</div>
 
@endsection
 
@section('scripts')
 
<script type="text/javascript">
    $(document).ready(function(){
 
        // btn refresh
        $('.btn-refresh').click(function(e){
            e.preventDefault();
            $('.preloader').fadeIn();
            location.reload();
        })
 
    })
</script>