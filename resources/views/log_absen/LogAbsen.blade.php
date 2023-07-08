@extends('layouts.master')
<head>
    <title>Absen</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
</head>
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
            </div>
        </div>
        <div style="margin: 20px 0px;">
        <strong>Date Filter:</strong>
        <input type="text" name="daterange" value="" />
        <button class="btn btn-success filter">Filter</button>
        </div>
        <div class="box-body">

            <div class="table-responsive">
                <table class="table table-hover absenTable">
                    <thead>
                        <tr>
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
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
</div>

@endsection

<script type="text/javascript">
    $(function(){
        $('.absenTable').DataTable({
            processing : true,
            serverSide : true,
            ajax : {
                url : "{{ route('log_absen.index') }}"
            },
            column : [
                {data : 0, name : 'users_id'},
                {data : 1, name : 'nama'},
                {data : 2, name : 'tanggal'},
                {data : 3, name : 'jam_masuk'},
                {data : 4, name : 'jam_keluar'},
                {data : 5, name : 'total_jam'},
                {data : 6, name : 'keterlambatan'},
            ],
        });
    });

</script>
