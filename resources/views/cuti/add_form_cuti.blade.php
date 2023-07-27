@extends('layouts.master')
 
@section('content')
<style>
  .custom-input-group {
    font-size: 16px;
  }
</style>
<div class="row">
    <div class="col-md-12">
        <h4>{{ $title }}</h4>
        <div class="box box-warning">
            <div class="box-header">
                <p>
                    <button class="btn btn-sm btn-flat btn-warning btn-refresh"><i class="fa fa-refresh"></i> Refresh</button>
                </p>
            </div>
            <div class="box-body">
               
            <form
                    class="border"
                    style="padding: 20px"
                    method="POST"
                    action="{{ url('/cuti/create/store') }}"
                >
                    @csrf
                    <input type="hidden" name="_method" value="{{ $method }}" />
                    <!-- <div class="form-group">
                        <label>Judul</label>
                        <input
                            type="string"
                            name="judul"
                            class="form-control"
                            value="{{ isset($data)?$data->judul:'' }}"
                        />
                    </div> -->
                    <div class="form-group" id="user">
                    <label for="name">Nama</label>
                        <div class="input-group custom-input-group">
                            <select class="from control" name="nama" id="user">
                            <option selected disabled">--- pilih karyawan ---</option>
                                @foreach ($users as $user)
                                <option value="{{$user->id}}">{{$user->nama}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Awal</label>
                        <input
                            type="date"
                            name="tanggal_awal"
                            class="datepicker"
                            value="{{ isset($data)?$data->tanggal_awal:'' }}"
                        />
                    </div>
                    <div class="form-group">
                        <label>Tanggal Akhir</label>
                        <input
                            type="date"
                            name="tanggal_akhir"
                            class="datepicker"
                            value="{{ isset($data)?$data->tanggal_akhir:'' }}"
                        />
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <input
                            type="text"
                            name="deskripsi"
                            class="text"
                            value="{{ isset($data)?$data->deskripsi:'' }}"
                        />
                    </div>
                    <div style="text-align: center">
                        <button class="btn btn-success">Simpan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
 
@endsection

@push('scripts')
    <script>
        var nationalHolidays = @json($libur_nasional);
        var disabledDates = [];
        @foreach ($libur_nasional as $date)
            disabledDates.push("{{ $date }}");
        @endforeach

        $('.datepicker').datepicker({
            beforeShowDay: function(date) {
                var stringDate = $.datepicker.formatDate('yy-mm-dd', date);
                if ($.inArray(stringDate, disabledDates) != -1) {
                    return [false];
                }
                return [true];
            }
        });
    </script>
@endpush
