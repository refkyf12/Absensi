@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h4>Akumulasi Absen</h4>
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
                    <form method="GET" action="/akumulasi/filter">
                        <div class="form-group">
                            <label for="tanggal-filter-start">Tanggal Awal:</label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="tanggal-filter-end">Tanggal Akhir:</label>
                            <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control">
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
                                <th>Tanggal Awal</th>
                                <th>Tanggal Akhir</th>
                                <th>Total Jam Kerja</th>
                                <!-- <th>Keterlambatan</th> -->
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $e=>$dt)
                            <tr>
                                <td>{{$dt->users_id}}</td>
                                <td>
                                    {{$dt->nama}}
                                </td>
                                <td>{{request('tanggal_mulai')}}</td>
                                <td>{{request('tanggal_akhir')}}</td>
                                <td>{{$dt->total_kerja}}</td>
                                <!-- <td>{{$dt->total_keterlambatan}}</td> -->
                                <td>
                                <!-- <td> -->
                                    <form class="border"
                                    method="GET"
                                    action="/akumulasi/detail/{{$dt->users_id}}">
                                        <button type="submit">Lihat Detail</button>
                                    </form>
                                <!-- </td> -->
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
