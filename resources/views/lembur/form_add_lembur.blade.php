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
                    action="{{url('lembur/create')}}"
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
                        <label>Tanggal</label>
                        <input
                            type="date"
                            name="tanggal"
                            class="form-control"
                            value="{{ isset($data)?$data->tanggal:'' }}"
                        />
                    </div>
                    <div class="form-group">
                        <label>Jam Awal</label>
                        <input
                            type="string"
                            name="jam_awal"
                            class="form-control"
                            value="{{ isset($data)?$data->jam_awal:'' }}"
                        />
                    </div>
                    <div class="form-group">
                        <label>Jam Akhir</label>
                        <input
                            type="string"
                            name="jam_akhir"
                            class="form-control"
                            value="{{ isset($data)?$data->jam_akhir:'' }}"
                        />
                    </div>
                    <div class="form-group">
                        <label>Status kerja</label>
                        <br>
                        <label>
                            <input type="radio" name="status_kerja" value="2"> Di rumah
                        </label>
                        <br>
                        <label>
                            <input type="radio" name="status_kerja" value="1"> Di kantor
                        </label>
                        <br>
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
 
@section('scripts')
 
<script type="text/javascript">
    $(document).ready(function(){
 
        // btn refresh
        $('.btn-refresh').click(function(e){
            e.preventDefault();
            $('.preloader').fadeIn();
            location.reload();
        })
 
    })
</script>
