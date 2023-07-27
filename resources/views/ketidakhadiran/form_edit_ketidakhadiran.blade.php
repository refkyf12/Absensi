@extends('layouts.master')
 
@section('content')
<style>
  .custom-input-group {
    font-size: 16px;
  }
</style>
<div class="row">
    <div class="col-md-12">
        <h4>Edit Absen</h4>
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
                    action="/ketidakhadiran/update/{{$data->id}}"
                >
                    @csrf
                    <input type="hidden" name="_method" value="POST" />
                    <div class="form-group" id="user">
                    <label for="name">Nama</label>
                        <div class="input-group custom-input-group">
                        <input
                            type="text"
                            name="nama"
                            class="form-control"
                            value="{{ isset($data)?$data->users->nama:'' }}"
                            readonly
                        />
                        </div>
                    </div>

                    <div class="form-group" id="tanggal">
                    <label for="tanggal">tanggal</label>
                        <div class="input-group custom-input-group">
                        <input
                            type="text"
                            name="tanggal"
                            class="form-control"
                            value="{{ isset($data)?$data->tanggal:'' }}"
                            readonly
                        />
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi</label>
                        <input
                            type="text"
                            name="deskripsi"
                            class="form-control"
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
