@extends('layouts.master')
 
@section('content')
<style>
  .custom-input-group {
    font-size: 16px;
  }
</style>
<div class="row">
    <div class="col-md-12">
        <h4>Approval Cuti</h4>
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
                    action="/lembur/status/update/{{$data->id}}"
                >
                    @csrf
                    <input type="hidden" name="_method" value="POST" />
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
                        <input
                            type="text"
                            name="nama"
                            class="form-control"
                            value="{{ isset($data)?$data->User->nama:'' }}"
                            readonly
                        />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Jam Awal</label>
                        <input
                            type="text"
                            name="jam_awal"
                            class="form-control"
                            value="{{ isset($data)?$data->jam_awal:'' }}"
                            readonly
                        />
                    </div>
                    <div class="form-group">
                        <label>Jam Akhir</label>
                        <input
                            type="text"
                            name="jam_akhir"
                            class="form-control"
                            value="{{ isset($data)?$data->jam_akhir:'' }}"
                            readonly
                        />
                    </div>
                    <div class="form-group">
                        <label>Approval</label>
                        <br>
                        <select required name="status">
                            <option value="">--pilih--</option>
                            <option value=1>Disetujui</option>
                            <option value=2>Ditolak</option>
                        </select>
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
