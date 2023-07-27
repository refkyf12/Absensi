@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h4>Lembur</h4>
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
            @if(\Auth::user()->role_id == 1 || \Auth::user()->role_id == 2 || \Auth::user()->role_id == 3)
                <p>
                    <a href="/lembur/create" class="btn btn-sm btn-flat btn-primary"><i class="fa fa-plus"></i> Tambah
                        Data</a>
                </p>
                @endif
                <div class="box-body">
                    <form method="GET" action="/lembur/filter">
                        <div class="form-group">
                            <label for="tanggal-filter-start">Tanggal Awal:</label>
                            <input type="date" name="start_date" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="tanggal-filter-end">Tanggal Akhir:</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                </div>


                <div class="box-body">


                    <div class="table-responsive">
                        <table class="table table-hover myTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Tanggal Lembur</th>
                                    <th>Lama Lembur</th>
                                    <th>Jam Awal</th>
                                    <th>Jam Akhir</th>
                                    <th>Jam Keluar</th>
                                    <th>Status Kerja</th>
                                    <th>Status Approval</th>
                                    @if(\Auth::user()->role_id == 1 || \Auth::user()->role_id == 2 || \Auth::user()->role_id == 3)
                                    <th>Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $e=>$dt)
                                <tr>
                                    <td>{{ $e+1 }}</td>

                                    <td>
                                        @if ($dt->id)
                                        {{$dt->user_nama}}
                                        @endif
                                    </td>
                                    <td>{{ $dt->tanggal }}</td>
                                    <td>
                                        {{ sprintf("%02d:%02d", intdiv($dt->jumlah_jam, 60), $dt->jumlah_jam % 60) }}
                                    </td>
                                    <td>{{ $dt->jam_awal }}</td>
                                    <td>{{ $dt->jam_akhir }}</td>
                                    <td>{{ $dt->jam_keluar }}</td>

                                    @if($dt->status_kerja == 1)
                                        <td>Di Kantor</td>
                                    @endif
                                    @if($dt->status_kerja == 2)
                                        <td>Di Rumah</td>
                                    @endif

                                    @if($dt->status == 1)
                                        <td>DISETUJUI</td>
                                    @endif
                                    @if($dt->status == 2)
                                        <td>DITOLAK</td>
                                    @endif
                                    @if($dt->status == null)
                                        <td>BELUM DI PROSES</td>
                                    @endif
                                    @if(\Auth::user()->role_id == 1 || \Auth::user()->role_id == 2 || \Auth::user()->role_id == 3)
                                    <td>
                                        
                                        <div style="width:90px">
                                            @if($dt->status == null)
                                                <a href="/lembur/status/{{$dt->id}}" class="btn btn-success btn-xs btn-edit"
                                                id="edit"><i class="fa fa-check"></i></a>
                                            @endif
                                                <a href="/lembur/status/viewedit/{{$dt->id}}" class="btn btn-warning btn-xs btn-edit"
                                                id="edit"><i class="fa fa-edit"></i></a>
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
        $(document).ready(function () {

            // btn refresh
            $('.btn-refresh').click(function (e) {
                e.preventDefault();
                $('.preloader').fadeIn();
                location.reload();
            })

        })

    </script>
