@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h4>Log Absen</h4>
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
                <!-- <form
                    class="border"
                    style="padding: 20px"
                    method="POST"
                    action="{{ url('/soap_data') }}"
                >
                @csrf
                <div style="text-align: center">
                        <button class="btn btn-success">Tambah Data Log absen</button>
                    </div>
                </form> -->
                <div class="box-body">
                @if(\Auth::user()->role_id == 1 || \Auth::user()->role_id == 2 || \Auth::user()->role_id == 3)
                <form method="post" action="/log_absen/import_excel" enctype="multipart/form-data">
                        <button id="showModalBtn" class="btn btn-primary">Import Data</button>
                    <div class="modal-dialog" role="document">
                        

                        <div class="modal-content" id="modalContent" hidden>
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Import Excel</h5>
                            </div>
                            <div class="modal-body">
                                <form action="your_form_action_url_here" method="post" enctype="multipart/form-data">
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
                            </form>
                        </div>
                        @endif

                        <script>
                            document.getElementById("showModalBtn").addEventListener("click", function() {
                                document.getElementById("modalContent").removeAttribute("hidden");
                            });
                        </script>

                        </form>
                    </div>
                <!-- </div>

                <div class="box-body"> -->
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
                                <th>User ID</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Total Jam Kerja</th>
                                <th>Keterangan</th>
                                <th>Deskripsi</th>
                                @if(\Auth::user()->role_id == 1 || \Auth::user()->role_id == 2 || \Auth::user()->role_id == 3)
                                <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $e=>$dt)
                            <tr>
                                <td>{{$dt->users_id}}</td>
                                <td>
                                    @if ($dt->id)
                                    {{$dt->users->nama}}
                                    @endif
                                </td>
                                <td>{{$dt->tanggal}}</td>
                                <td>{{$dt->jam_masuk}}</td>
                                <td>{{$dt->jam_keluar}}</td>
                                <td>
                                    {{$dt->total_jam}}
                                </td>
                                @if($dt->keterlambatan == true)
                                <td>Jam Kerja Tidak Terpenuhi</td>
                                @endif
                                @if($dt->keterlambatan == false)
                                <td>Jam Kerja Terpenuhi</td>
                                @endif
                                <td>{{$dt->deskripsi}}</td>
                                @if(\Auth::user()->role_id == 1 || \Auth::user()->role_id == 2 || \Auth::user()->role_id == 3)
                                <td>
                                        
                                        <div style="width:90px">
                                                <a href="/log_absen/edit/{{$dt->id}}" class="btn btn-warning btn-xs btn-edit"
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
