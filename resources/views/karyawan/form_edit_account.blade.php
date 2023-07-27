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
                    action="/karyawan/update/{{ $data->id }}"
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
                        />
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input
                            type="string"
                            name="nama"
                            class="form-control"
                            value="{{ $data->nama}}"
                        />
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input
                            type="string"
                            name="email"
                            class="form-control"
                            value="{{ $data->email }}"
                        />
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            value="{{ $data->password }}"
                        />
                    <div class="form-group">
                        <label>Role</label>
                        <br>
                        <select required name="role_id">
                        <option value="">--pilih--</option>
                        <option value=0>Karyawan</option>
                        <option value=1>Admin</option>
                        <option value=2>Project Manager</option>
                        <option value=3>HR</option>
                        <option value=4>Administrasi</option>
                        </select>
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
