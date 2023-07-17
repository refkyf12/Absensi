@extends('layouts.master')
 
@section('content')
 
<div class="row">
    <div class="col-md-12">
        <h4>Edit Karyawan</h4>
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
                    action="/role/update/{{$data->id}}"
                >
                    @csrf
                    <input type="hidden"/>
                    <div class="form-group">
                        <label>ID</label>
                        <input
                            type="string"
                            name="id"
                            class="form-control"
                            value="{{ $data->id }}"
                            readonly
                        />
                    </div>
                    <div class="form-group">
                        <label>Nama Role</label>
                        <input
                            type="string"
                            name="nama_role"
                            class="form-control"
                            value="{{ $data->nama_role}}"
                        />
                    </div>
                    <div class="form-group">
                        <label>Cuti</label>
                        <input
                            type="number"
                            name="sisa_cuti"
                            class="form-control"
                            value="{{ $data->sisa_cuti }}"
                        />
                    </div>
                    @if($errors->any())
                    <b style="color:red" >{{$errors->first()}}</b>
                    @endif
                    <br>
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
