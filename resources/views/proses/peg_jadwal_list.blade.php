@extends('layouts.app')
@push('css')
<link href="{{ asset('datatables.net-dt/css/jquery.dataTables.css') }}" rel="stylesheet">
<link href="{{ asset('datatables.net-buttons-dt/buttons.dataTables.css') }}" rel="stylesheet">
<link href="{{ asset('datatables.net-select-dt/select.dataTables.css') }}" rel="stylesheet">
<link href="{{ asset('css/rowGroup.dataTables.min.css') }}" rel="stylesheet">
@endpush
@section('content')
@if(Auth::check())
   @include('partial.subnavbar')
@endif
<div class="main">
  <div class="main-inner">
      <div class="container">
          {!! Breadcrumbs::render('peg_jadwal.list') !!}
          <div class="row">
            <div class="span12">
              <div class="widget">
                <div class="widget-header">
                  <i class="icon-list"></i>
                  <h3>Daftar Jadwal Pegawai</h3>
                </div>

                <div class="widget-content">
                  <table class="table table-striped table-bordered display nowrap" id="pegjadwal-table" width="100%" >
                      <thead>
                          <tr>
                              <th></th>
                              <th></th>
                              <th></th>
                              <th>Nip</th>
                              <th>OPD</th>
                              <th>Nama</th>
                              <th>Pangkat / Gol</th>
                              <th>Jabatan</th>
                              <th>Detail</th>
                          </tr>
                      </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
      </div>
  </div>
</div>
@endsection

@push('script')
<script src="{{ asset('jquery/jquery.min.js') }}"></script>
<script src="{{ asset('datatables.net/js/jquery.dataTables.js') }}"></script>
<script src="{{ asset('js/twitter.datatables.js') }}"></script>
<script src="{{ asset('datatables.net-buttons/dataTables.buttons.js') }}"></script>
<script src="{{ asset('datatables.net-select/dataTables.select.js') }}"></script>
<script src="{{ asset('js/dataTables.rowGroup.min.js') }}"></script>
<script>
$(function() {
    var table = $('#pegjadwal-table').DataTable({
        dom: 'Bfrtip',
        scrollX: true,
        select: {
            style:    'multi',
            selector: 'td:first-child'
        },
        buttons: [
          'selectAll',
          'selectNone',
            {
                text: '<i class="icon-plus"> Tambah Data</i>',
                titleAttr: 'Tambah Data',
                action: function ( e, dt, node, config ) {
                  window.location.href = "{{ route('peg_jadwal.create') }}";
                }
            },
            {
                text: '<i class="icon-remove"> Hapus</i>',
                action: function ( e, dt, node, config ) {
                  toastr.info("Apakah anda yakin ingin menghapus data ini?<br/><button type='button' id='confirmYes' class='btn btn-danger'>Ya</button> <button type='button' id='confirmNo' class='btn'>Tidak</button>",'Konfirmasi?',
                  {
                      positionClass: "toast-top-center",
                      timeOut: 0,
                      tapToDismiss: false,
                      closeButton: false,
                      allowHtml: true,
                      onShown: function (toast) {
                          $("#confirmYes").click(function(){
                            var data = dt.rows( { selected: true } ).data().pluck('id');

                            var arrData = [];
                            $.each(data, function(key, value) {
                              arrData[key] = value;
                            });

                            $.ajax({
                                url: '{{ url("api/peg_jadwal_delete_all?api_token=") }}{{ Auth::user()->api_token }}',
                                type: 'post',
                                dataType: "json",
                                data: { data: arrData },
                                success: function(response){
                                  table.ajax.reload();
                                },
                                error: function(error){
                                  console.log(error);
                                }
                            });
                            toastr.remove();
                          });

                          $("#confirmNo").click(function(){
                            toastr.remove();
                          });
                        }
                  });
                },
                enabled: false
            },
        ],
        processing: true,
        serverSide: true,
        ajax: '{{ url("api/peg_jadwal_list?api_token=") }}{{ Auth::user()->api_token }}',
        columns: [
            { orderable: false, className: 'select-checkbox', data: null, defaultContent:'', searchable: false },
            { className: 'details-control', orderable: false, data: null, defaultContent: '', searchable: false },
            { data: 'id', name: 'id', visible: false },
            { data: 'nip', name: 'nip' },
            { data: 'nama_unker', name: 'nama_unker', width:'250px', visible: false },
            { data: 'nama', name: 'nama', width: '150px' },
            { data: 'pangkat', name: 'pangkat', width: '140px' },
            { data: 'nama_jabatan', name: 'nama_jabatan', width: '300px' },
            { data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        drawCallback: function( settings ){
          var api = this.api();
          var rows = api.rows( {page:'current'} ).nodes();
          var last=null;

          api.column(4, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        '<tr class="group"><td colspan="9">'+group+'</td></tr>'
                    );

                    last = group;
                }
            } );
        }
    });

    table.on( 'select', function (e, dt, type, indexes) {
        var selectedRows = table.rows( { selected: true } ).count();

        table.button( 1 ).enable( selectedRows >= 1 );
        table.button( 3 ).enable( selectedRows >= 1 );
    } );

    table.on( 'deselect', function ( e, dt, type, indexes ) {
        var selectedRows = table.rows( { selected: true } ).count();

        table.button( 1 ).enable( selectedRows >= 1 );
        table.button( 3 ).enable( selectedRows >= 1 );
    } );

    $('#pegjadwal-table').on('click', 'td.details-control', function () {
       var tr = $(this).closest('tr');
       var row = table.row( tr );

       if ( row.child.isShown() ) {
           // This row is already open - close it
           row.child.hide();
           tr.removeClass('shown');
       }
       else {
           // Open this row
           row.child( format(row.data()) ).show();
           tr.addClass('shown');
       }
   } );

    function format ( d ) {
      var head = '<thead>'+
                  '<tr>'+
                    '<th>Id</th>'+
                    '<th>Nama Jadwal</th>'+
                    '<th>Mulai</th>'+
                    '<th>Berakhir</th>'+
                    '<th>Action</th>'+
                  '</tr>'+
                '</thead>';

        var content = '';
        var urlEdit = '{{ url("peg_jadwal_edit") }}';
        var urlDelete = '{{ url("peg_jadwal_delete_jadwal") }}';

        $.ajax({
          type: 'post',
          url: '{{ url("api/jadwal_detail?api_token=") }}{{ Auth::user()->api_token }}',
          dataType: 'json',
          data:{
            id: d.id
          },
          async: false,
          success: function(response){
            $.each(response, function(key, value) {
              content += '<tr>'+
                          '<td>'+value.id+'</td>'+
                          '<td>'+value.name+'</td>'+
                          '<td>'+value.start+'</td>'+
                          '<td>'+value.end+'</td>'+
                          '<td>'+
                            '<a href='+urlEdit+'/'+d.id+'/'+value.id+' class="btn btn-mini btn-success"><i class="icon-edit"> Edit</i></a> '+
                            '<a href='+urlDelete+'/'+value.id+' class="btn btn-mini btn-danger"><i class="icon-remove"> Hapus</i></a> '+
                          '</td>'+
                         '</tr>';
            });
          }
        });

        return '<table class="table table-bordered">'+
                head+
                content+
               '</table>';
    }
});
</script>
@endpush
