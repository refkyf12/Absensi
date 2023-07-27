@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h4>Ketidakhadiran</h4>
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
            @if(\Auth::user()->role_id == 1 || \Auth::user()->role_id == 2 || \Auth::user()->role_id == 3)
                <form
                    class="border"
                    style="padding: 20px"
                    method="POST"
                    action="{{ url('/ketidakhadiran/simpan') }}"
                >
                @csrf
                <div style="text-align: center">
                        <button class="btn btn-success">Tambah ketidakhadiran</button>
                    </div>
                </form>
                @endif
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
                                <th>User ID</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
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
                                @if($dt->deskripsi == null)
                                <td>Tidak ada keterangan</td>
                                @endif
                                @if($dt->deskripsi != null)
                                <td>{{$dt->deskripsi}}</td>
                                @endif
                                @if(\Auth::user()->role_id == 1 || \Auth::user()->role_id == 2 || \Auth::user()->role_id == 3)
                                <td>
                                        
                                        <div style="width:90px">
                                                <a href="/ketidakhadiran/show/{{$dt->id}}" class="btn btn-warning btn-xs btn-edit"
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
