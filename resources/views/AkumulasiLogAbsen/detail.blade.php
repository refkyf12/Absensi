@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h4>Detail Log Absen</h4>
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
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Total Kerja (1 Hari)</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataDetail as $e=>$dt)
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
                                <td>{{$dt->total_jam}}</td>
                                @if($dt->keterlambatan == true)
                                <td>Jam Kerja Tidak Terpenuhi</td>
                                @endif
                                @if($dt->keterlambatan == false)
                                <td>Jam Kerja Terpenuhi</td>
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
