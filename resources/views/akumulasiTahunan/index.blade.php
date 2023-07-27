@extends('layouts.master')
 
@section('content')
 
<div class="row">
    <div class="col-md-12">
        <h4>Akumulasi Tahunan</h4>
        @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif
        <div class="box box-warning">
            <div class="box-header">
                <p>
                    <button class="btn btn-sm btn-flat btn-warning btn-refresh"><i class="fa fa-refresh"></i> Refresh</button>
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
                                <th>Tanggal</th>
                                <th class="not-export-col">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $e=>$dt)
                                <tr>
                                <td>{{ $e+1 }}</td>
                                <td>{{ $dt->users_id }}</td>
                                <td>{{ $dt->nama }}</td>
                                <td>{{ $dt->email }}</td>
                                @if($dt->role_id == 0)
                                <td>Karyawan</td>
                                @endif
                                @if($dt->role_id == 1)
                                <td>Admin</td>
                                @endif
                                @if($dt->role_id == 2)
                                <td>Project Manager</td>
                                @endif
                                @if($dt->role_id == 3)
                                <td>HR</td>
                                @endif
                                <td>
                                        @if ($dt->id)
                                        {{$dt->role->sisa_cuti}}
                                        @endif
                                </td>

                                @if($dt->jam_lebih == null || $dt->jam_lebih == 0)
                                <td>Tidak pernah lebih</td>
                                @endif
                                @if($dt->jam_lebih != null && $dt->jam_lebih >= 0)
                                <td>{{ $dt->jam_lebih }}</td>
                                @endif

                                @if($dt->jam_kurang == null || $dt->jam_kurang == 0)
                                <td>Tidak pernah kurang</td>
                                @endif
                                @if($dt->jam_kurang != null && $dt->jam_kurang >= 0)
                                <td>{{ $dt->jam_kurang }}</td>
                                @endif

                                @if($dt->jam_lembur == null || $dt->jam_lembur == 0)
                                <td>Tidak pernah lembur</td>
                                @endif
                                @if($dt->jam_lembur != null && $dt->jam_lembur >= 0)
                                <td>{{ $dt->jam_lembur }}</td>
                                @endif                
                                <td>{{ $dt->created_at }}</td>
                                @if(\Auth::user()->role_id == 1 || \Auth::user()->role_id == 2 || \Auth::user()->role_id == 3)
                                <td>
                                    <div style="width:60px">
                                        <a href="/karyawan/{{$dt->id}}" class="btn btn-warning btn-xs btn-edit" id="edit"><i class="fa fa-pencil-square-o"></i></a>
                                        @if($dt->jam_lembur != null && $dt->jam_lembur*60 <= $dt->jam_lebih)
                                        <a href="/kurang/{{$dt->id}} "class="btn btn-danger btn-xs btn-hapus" id="delete"><i class="fa fa-trash-o"></i></a> 
                                        @endif                                   
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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