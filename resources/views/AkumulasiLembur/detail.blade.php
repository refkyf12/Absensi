@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h4>Detail Lembur</h4>
        <div class="box box-warning">
            <div class="box-header">
                <div class="box-body">
                </div>
                <!-- </div> -->
                <div class="table-responsive">
                    <table class="table table-hover myTable">
                        <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Nama</th>
                                <th>Tanggal</th>
                                <th>Jam Awal</th>
                                <th>Jam Akhir</th>
                                <th>Jumlah Jam (1 Hari)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataDetail as $e=>$dt)
                            <tr>
                                <td>{{$dt->users_id}}</td>
                                <td>
                                    {{$dt->nama}}
                                </td>
                                <td>{{$dt->tanggal}}</td>
                                <td>{{$dt->jam_awal}}</td>
                                <td>{{$dt->jam_akhir}}</td>
                                <td>{{$dt->jumlah_jam}}</td>
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
