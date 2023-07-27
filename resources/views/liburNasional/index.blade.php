@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h4>Libur Nasional</h4>
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
                    <a href="/libur/create" class="btn btn-sm btn-flat btn-primary"><i class="fa fa-plus"></i> Tambah
                        Data</a>
                </p>
                @endif
                <div class="box-body">
                    <!-- <form method="GET" action="/lembur/filter">
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
                </div> -->
                <div class="box-body">


                    <div class="table-responsive">
                        <table class="table table-hover myTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Deskripsi</th>
                                    @if(\Auth::user()->role_id == 1 || \Auth::user()->role_id == 2 || \Auth::user()->role_id == 3)
                                    <th>Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $e=>$dt)
                                <tr>
                                    <td>{{ $e+1 }}</td>
                                    <td>{{ $dt->tanggal }}</td>
                                    <td>{{ $dt->deskripsi }}</td>
                                    @if(\Auth::user()->role_id == 1 || \Auth::user()->role_id == 2 || \Auth::user()->role_id == 3)
                                    <td>
                                        <div style="width:90px">
                                            <a href="/libur/{{$dt->id}}" class="btn btn-warning btn-xs btn-edit"
                                                id="edit"><i class="fa fa-check"></i></a>
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
