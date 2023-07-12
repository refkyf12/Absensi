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
                    action="/rules/update/{{ $data->id }}"
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
                        <label>Nama</label>
                        <input
                            type="string"
                            name="key"
                            class="form-control"
                            value="{{ $data->key}}"
                            readonly
                        />
                    </div>
                    <div class="form-group">
                        <label>Value</label>
                        <input
                            type="string"
                            name="value"
                            class="form-control"
                            value="{{ $data->value }}"
                        />
                    </div>
                    @if($errors->any())
                    <b style="color:red" >{{$errors->first()}}</b>
                    @endif
                    <br>
                    <!-- <div class="form-group">
                        <label>Role</label>
                        <br>
                        <select required name="role">
                        <option value="">--pilih--</option>
                        <option value=1>Admin</option>
                        <option value=2>HRD</option>
                        <option value=3>Karyawan</option>
                        </select>
                    </div> -->
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
