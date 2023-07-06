@extends('layouts.master')
 
@section('content')
 
<div class="row">
    <div class="col-md-12">
        <h4>Lembur</h4>
        <div class="box box-warning">
            <div class="box-header">
                <p>
                    <button class="btn btn-sm btn-flat btn-warning btn-refresh"><i class="fa fa-refresh"></i> Refresh</button>
                </p>
            </div>
            <div class="box-body">
               
                <div class="table-responsive">
                    <table class="table table-hover myTable" id="absentable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Total Lembur</th>
                                <th>Approval</th>
                                <th class="not-export-col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $e=>$dt)
                                <tr>
                                <td>{{ $e+1 }}</td>
                                <td>
                                    @if ($dt->id)
                                    {{$dt->users->nama}}
                                    @endif
                                </td>
                                <td>{{ $dt->total_jam }}</td>
                                
                                @if ($dt->disetujui == 1)
                                    <td>DISETUJUI</td>
                                @endif

                                @if ($dt->disetujui == 2)
                                    <td>DITOLAK</td>
                                @endif

                                @if ($dt->disetujui == NULL)
                                    <td>BELUM DIPROSES</td>
                                @endif
                                
                                <td>
                                    <div style="width:60px">
                                        <a href="/lembur/setuju/{{$dt->id}}" class="btn btn-success btn-xs btn-edit" id="edit"><i class="fa fa-check"></i></a>
                                        <a href="/lembur/tolak/{{$dt->id}}" class="btn btn-danger btn-xs btn-edit" id="tolak"><i class="fa fa-pencil-square-o"></i></a>
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
        // btn refresh
        $('.btn-refresh').click(function(e){
            e.preventDefault();
            $('.preloader').fadeIn();
            location.reload();
        });
    });
</script>
