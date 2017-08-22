@php
  $start = Carbon\Carbon::parse($start);
  $end = Carbon\Carbon::parse($end);
  $interval = $end->diffInDays($start);

@endphp
@extends('layouts.report')
@push('css')
  <style>
    @page { size: F4 landscape }
    .sub-title { background:#eee; }
  </style>
@endpush
@section('content')
    <table>
      <thead>
        <tr>
          <td align="center" >
            <h4>LAPORAN KEHADIRAN</h4>
            <h3>{{ $opd->nama_unker }}</h3>
            <h5>Tanggal: {{ $start->format('d-m-Y')  }}&nbsp;s/d&nbsp;{{ $end->format('d-m-Y') }}</h5>
            <br>
          </td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td align="center" >
            <table class="border thick data">
              <thead>
                <tr class="thick">
                  <th rowspan="2" width="5">NO</th>
                  <th rowspan="2">NAMA</th>
                  <th rowspan="2" width="10">NIP</th>
                  <th colspan="2">JAM KERJA</th>
                  <th colspan="2">ABSENSI</th>
                  <th rowspan="2">TERLAMBAT</th>
                  <th rowspan="2">PULANG AWAL</th>
                  <th rowspan="2">KETERANGAN</th>
                </tr>
                <tr class="thick">
                  <th>MASUK</th>
                  <th>PULANG</th>
                  <th>MASUK</th>
                  <th>PULANG</th>
                </tr>
                </thead>
                <tbody>
                  @for($i=1;$i<=$interval;$i++)
                    <tr>
                      <td colspan='10' class="sub-title"><h5>Tanggal: {{ $start->format('d-m-Y') }}</h5></td>
                    </tr>
                    @php $no = 1 @endphp
                    @foreach($data[$start->day] as $value)
                    <tr>
                      <td align="center">{{ $no }}</td>
                      <td>{{ $value->nama }}</td>
                      <td align="center">{{ $value->nip }}</td>
                      <td align="center">{{ $value->jam_masuk }}</td>
                      <td align="center">{{ $value->jam_pulang }}</td>
                    </tr>
                    @php $no++ @endphp
                    @endforeach
                    @php $start->addDay() @endphp
                  @endfor
                </tbody>
                <tfoot>
                  <tr>
                    <td>Test</td>
                  </tr>
                </tfoot>
            </table>
          </td>
        </tr>
      </tbody>
    </table>

    <blockquote>
      <strong>Keterangan:</strong>
      <small>H = Hadir&nbsp;&nbsp;&nbsp;HT = Hadir Terlambat&nbsp;&nbsp;&nbsp;HP = Hadir Pulang Awal&nbsp;&nbsp;&nbsp;A = Absent</small>
      <small>I = Ijin&nbsp;&nbsp;&nbsp;S = Sakit&nbsp;&nbsp;&nbsp;C = Cuti&nbsp;&nbsp;&nbsp;DL = Dinas Luar&nbsp;&nbsp;&nbsp;TB = Tugas Belajar</small>
    </blockquote>
@endsection
