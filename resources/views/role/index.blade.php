@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h4>Role</h4>
        <div class="box box-warning">
            <div class="box-header">
                <p>
                    <a href="/role/create" class="btn btn-sm btn-flat btn-primary"><i class="fa fa-plus"></i> Tambah
                        Data</a>
                </p>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover myTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>Nama Role</th>
                                    <th>Sisa Cuti</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $e=>$dt)
                                <tr>
                                    <td>{{ $e+1 }}</td>
                                    <td>{{ $dt->id }}</td>
                                    <td>{{ $dt->nama_role }}</td>
                                    <td>{{ $dt->sisa_cuti }}</td>
                                    <td>
                                        <div style="width:90px">
                                            <a href="/role/{{ $dt->id }}" class="btn btn-warning btn-xs btn-edit"
                                                id="edit"><i class="fa fa-check"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
