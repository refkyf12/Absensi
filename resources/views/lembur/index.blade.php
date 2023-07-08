@extends('layouts.master')
 
@section('content')
 
<div class="row">
    <div class="col-md-12">
        <h4>Lembur</h4>
        <div class="box box-warning">
            <div class="box-header">
                <p>
                    <a href="/lembur/create" class="btn btn-sm btn-flat btn-primary"><i class="fa fa-plus"></i> Tambah Data</a>
                </p>
            </div>
            <div class="box-body">
               
                <div class="table-responsive">
                    <table class="table table-hover myTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Tanggal Lembur</th>
                                <th>Lama Lembur (jam) </th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $e=>$dt)
                            <tr>
                                <td>{{ $e+1 }}</td>

                                <td>
                                @if ($dt->id)
                                    {{$dt->User->nama}}
                                @endif
                                </td>
                                <td>{{ $dt->tanggal }}</td>
                                <td>{{ $dt->jumlah_jam }}</td>
                                <td>
                                    <div style="width:90px">
                                        <a href="/delete_lembur/{{$dt->id}}" class="btn btn-danger btn-xs btn-edit" id="edit"><i class="fa fa-trash-o"></i></a>
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
    $(document).ready(function(){
 
        // btn refresh
        $('.btn-refresh').click(function(e){
            e.preventDefault();
            $('.preloader').fadeIn();
            location.reload();
        })
 
    })
</script>
