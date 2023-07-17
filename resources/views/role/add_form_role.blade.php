@extends('layouts.master')
 
@section('content')
 
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
                    action="{{ url('/role/create/store') }}"
                >
                    @csrf
                    <input type="hidden" name="_method" value="{{ $method }}" />
                    <div class="form-group">
                        <label>ID</label>
                        <input
                            type="string"
                            name="id"
                            class="form-control"
                            value="{{ isset($data)?$data->id:'' }}"
                        />
                    </div>
                    <div class="form-group">
                        <label>Nama Role</label>
                        <input
                            type="string"
                            name="nama_role"
                            class="form-control"
                            value="{{ isset($data)?$data->nama_role:'' }}"
                        />
                    </div>
                    <div class="form-group">
                        <label>Cuti</label>
                        <input
                            type="number"
                            name="sisa_cuti"
                            class="form-control"
                            value="{{ isset($data)?$data->sisa_cuti:'' }}"
                        />
                    </div>
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
