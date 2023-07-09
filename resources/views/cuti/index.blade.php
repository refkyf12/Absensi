@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h4>Cuti</h4>
        <div class="box box-warning">
            <div class="box-header">
                <!-- <p>
                    <a href="/lembur/create" class="btn btn-sm btn-flat btn-primary"><i class="fa fa-plus"></i> Tambah Data</a>
                </p> -->
            </div>
            <div class="box-body">
                <form method="GET" action="/cuti/filter">
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
                                <th>Tanggal Cuti</th>
                                <th>Lama Cuti (hari) </th>
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
                                <td>{{ $dt->tanggal }}</td>
                                <td>{{ $dt->jumlah_hari }}</td>

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
