@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h4>Log Absen</h4>
        {{-- notifikasi form validasi --}}
        @if ($errors->has('file'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first('file') }}</strong>
        </span>
        @endif

        {{-- notifikasi sukses --}}
        @if ($sukses = Session::get('sukses'))
        <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $sukses }}</strong>
        </div>
        @endif
        <div class="box box-warning">
            <div class="box-header">
                <div class="modal-dialog" role="document">
                    <form method="post" action="/log_absen/import_excel" enctype="multipart/form-data">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Import Excel</h5>
                            </div>
                            <div class="modal-body">

                                {{ csrf_field() }}

                                <label>Pilih file excel</label>
                                <div class="form-group">
                                    <input type="file" name="file" required="required">
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Import</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="box-body">
                    <form method="GET" action="/filter">
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
                    </form>
                </div>
                <!-- </div> -->



                <div class="table-responsive">
                    <table class="table table-hover myTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>User ID</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Total Jam Kerja</th>
                                <th>Keterlambatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $e=>$dt)
                            <tr>
                                <td>{{ $e+1 }}</td>
                                <td>{{$dt->users_id}}</td>
                                <td>
                                    @if ($dt->id)
                                    {{$dt->users->nama}}
                                    @endif
                                </td>
                                <td>{{$dt->tanggal}}</td>
                                <td>{{$dt->jam_masuk}}</td>
                                <td>{{$dt->jam_keluar}}</td>
                                <td>{{$dt->total_jam}}</td>
                                @if($dt->keterlambatan == true)
                                <td>Terlambat</td>
                                @endif
                                @if($dt->keterlambatan == false)
                                <td>Tepat Waktu</td>
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
