<section class="sidebar">
      <!-- Sidebar user panel -->
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">

        @if(\Auth::user()->role_id == 1)
        <li class="menu-sidebar"><a href="{{ url('/karyawan') }}"><span class="fa fa-firefox"></span>Karyawan</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/log_absen') }}"><span class="fa fa-firefox"></span>Absen</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/akumulasi') }}"><span class="fa fa-firefox"></span>Akumulasi Absen</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/lebihKerja') }}"><span class="fa fa-firefox"></span>Jam Kerja Lebih</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/kurangKerja') }}"><span class="fa fa-firefox"></span>Jam Kerja Kurang</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/lembur') }}"><span class="fa fa-firefox"></span>Lembur</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/rules') }}"><span class="fa fa-firefox"></span>Rules</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/cuti') }}"><span class="fa fa-firefox"></span>Cuti</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/role') }}"><span class="fa fa-firefox"></span>Role(Jabatan Kerja)</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/libur') }}"><span class="fa fa-firefox"></span>Libur Nasional</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/akumulasi_tahunan') }}"><span class="fa fa-firefox"></span>Akumulasi Tahunan</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/log_activity') }}"><span class="fa fa-firefox"></span>Log Aktivitas</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/log_kegiatan') }}"><span class="fa fa-firefox"></span>Log Kegiatan</span></a></li>
        @endif

        @if(\Auth::user()->role_id == 2)
        <li class="menu-sidebar"><a href="{{ url('/karyawan') }}"><span class="fa fa-firefox"></span>Karyawan</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/lembur') }}"><span class="fa fa-firefox"></span>Lembur</span></a></li>
        @endif

        @if(\Auth::user()->role_id == 3)
        <li class="menu-sidebar"><a href="{{ url('/karyawan') }}"><span class="fa fa-firefox"></span>Karyawan</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/log_absen') }}"><span class="fa fa-firefox"></span>Absen</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/lebihKerja') }}"><span class="fa fa-firefox"></span>Lebih Kerja</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/lembur') }}"><span class="fa fa-firefox"></span>Lembur</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/rules') }}"><span class="fa fa-firefox"></span>Rules</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/cuti') }}"><span class="fa fa-firefox"></span>Cuti</span></a></li>
        @endif


  </ul>
    </section>