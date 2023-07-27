@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h4>Log Kegiatan</h4>
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
                                    <th>Role</th>
                                    <th>Kegiatan</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $e=>$dt)
                                <tr>
                                    <td>{{ $e+1 }}</td>
                                    <td>
                                        @if ($dt->id)
                                        {{$dt->User->nama}}
                                        @endif
                                    </td>
                                        @if($dt->User->role_id == 0)
                                            <td>Karyawan</td>
                                        @endif
                                        @if($dt->User->role_id == 1)
                                            <td>Admin</td>
                                        @endif
                                        @if($dt->User->role_id == 2)
                                            <td>Project Manager</td>
                                        @endif
                                        @if($dt->User->role_id == 3)
                                            <td>HR</td>
                                        @endif
                                    <td>{{ $dt->kegiatan }}</td>
                                    <td>{{ $dt->created_at }}</td>
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
