<section class="sidebar">
      <!-- Sidebar user panel -->
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">

        @if(\Auth::user()->role_id == 1)
        
        <li class="menu-sidebar"><a href="{{ url('/karyawan') }}"><span class="fa fa-firefox"></span>Karyawan</span></a></li>

        <li class="menu-sidebar" onclick="toggleDropdown('dropdown-kehadiran')">
            <a href="javascript:void(0)"><span class="fa fa-firefox"></span>Kehadiran ▼</span></a>
            <ul id="dropdown-kehadiran" style="display: none;" data-widget="tree">
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/log_absen') }}"><span class="fa fa-firefox"></span>Hari Kerja</span></a></li>
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/absen_non_kerja') }}"><span class="fa fa-firefox"></span>Non Hari Kerja</span></a></li>
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/akumulasi') }}"><span class="fa fa-firefox"></span>Akumulasi Kehadiran</span></a></li>
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/lebihKerja') }}"><span class="fa fa-firefox"></span>Jam Kerja Lebih</span></a></li>
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/kurangKerja') }}"><span class="fa fa-firefox"></span>Jam Kerja Kurang</span></a></li>
            </ul>
        </li>
        <li class="menu-sidebar"><a href="{{ url('/ketidakhadiran') }}"><span class="fa fa-firefox"></span>Ketidakhadiran</span></a></li>

        <li class="menu-sidebar" onclick="toggleDropdown('dropdown-lembur')">
            <a href="javascript:void(0)"><span class="fa fa-firefox"></span>Lembur ▼</span></a>
            <ul id="dropdown-lembur" style="display: none;">
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/lembur') }}"><span class="fa fa-firefox"></span>Lembur</span></a></li>
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/akumulasiLembur') }}"><span class="fa fa-firefox"></span>Akumulasi Lembur</span></a></li>
            </ul>
        </li>

        <li class="menu-sidebar"><a href="{{ url('/cuti') }}"><span class="fa fa-firefox"></span>Cuti</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/rules') }}"><span class="fa fa-firefox"></span>Peraturan</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/role') }}"><span class="fa fa-firefox"></span>Role</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/libur') }}"><span class="fa fa-firefox"></span>Libur Nasional</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/akumulasi_tahunan') }}"><span class="fa fa-firefox"></span>Akumulasi Tahunan</span></a></li>

        <li class="menu-sidebar" onclick="toggleDropdown('dropdown-log')">
            <a href="javascript:void(0)"><span class="fa fa-firefox"></span>Log ▼</span></a>
            <ul id="dropdown-log" style="display: none;">
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/log_activity') }}"><span class="fa fa-firefox"></span>Log Aktivitas</span></a></li>
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/log_kegiatan') }}"><span class="fa fa-firefox"></span>Log Kegiatan</span></a></li>
            </ul>
        </li>

        
        @endif

        @if(\Auth::user()->role_id == 2)
        <li class="menu-sidebar" onclick="toggleDropdown('dropdown-lembur')">
            <a href="javascript:void(0)"><span class="fa fa-firefox"></span>Lembur ▼</span></a>
            <ul id="dropdown-lembur" style="display: none;">
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/lembur') }}"><span class="fa fa-firefox"></span>Lembur</span></a></li>
            </ul>
        </li>
        <li class="menu-sidebar"><a href="{{ url('/cuti') }}"><span class="fa fa-firefox"></span>Cuti</span></a></li>
        @endif

        @if(\Auth::user()->role_id == 3)
        <li class="menu-sidebar"><a href="{{ url('/karyawan') }}"><span class="fa fa-firefox"></span>Karyawan</span></a></li>

        <li class="menu-sidebar" onclick="toggleDropdown('dropdown-kehadiran')">
            <a href="javascript:void(0)"><span class="fa fa-firefox"></span>Kehadiran ▼</span></a>
            <ul id="dropdown-kehadiran" style="display: none;" data-widget="tree">
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/log_absen') }}"><span class="fa fa-firefox"></span>Hari Kerja</span></a></li>
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/absen_non_kerja') }}"><span class="fa fa-firefox"></span>Non Hari Kerja</span></a></li>
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/akumulasi') }}"><span class="fa fa-firefox"></span>Akumulasi Kehadiran</span></a></li>
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/lebihKerja') }}"><span class="fa fa-firefox"></span>Jam Kerja Lebih</span></a></li>
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/kurangKerja') }}"><span class="fa fa-firefox"></span>Jam Kerja Kurang</span></a></li>
            </ul>
        </li>
        <li class="menu-sidebar"><a href="{{ url('/ketidakhadiran') }}"><span class="fa fa-firefox"></span>Ketidakhadiran</span></a></li>

        <li class="menu-sidebar" onclick="toggleDropdown('dropdown-lembur')">
            <a href="javascript:void(0)"><span class="fa fa-firefox"></span>Lembur ▼</span></a>
            <ul id="dropdown-lembur" style="display: none;">
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/lembur') }}"><span class="fa fa-firefox"></span>Lembur</span></a></li>
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/akumulasiLembur') }}"><span class="fa fa-firefox"></span>Akumulasi Lembur</span></a></li>
            </ul>
        </li>

        <li class="menu-sidebar"><a href="{{ url('/cuti') }}"><span class="fa fa-firefox"></span>Cuti</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/rules') }}"><span class="fa fa-firefox"></span>Peraturan</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/role') }}"><span class="fa fa-firefox"></span>Role</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/libur') }}"><span class="fa fa-firefox"></span>Libur Nasional</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/akumulasi_tahunan') }}"><span class="fa fa-firefox"></span>Akumulasi Tahunan</span></a></li>

        <li class="menu-sidebar" onclick="toggleDropdown('dropdown-log')">
            <a href="javascript:void(0)"><span class="fa fa-firefox"></span>Log ▼</span></a>
            <ul id="dropdown-log" style="display: none;">
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/log_activity') }}"><span class="fa fa-firefox"></span>Log Aktivitas</span></a></li>
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/log_kegiatan') }}"><span class="fa fa-firefox"></span>Log Kegiatan</span></a></li>
            </ul>
        </li>
        @endif

        @if(\Auth::user()->role_id == 4)
        <li class="menu-sidebar"><a href="{{ url('/karyawan') }}"><span class="fa fa-firefox"></span>Karyawan</span></a></li>

        <li class="menu-sidebar" onclick="toggleDropdown('dropdown-kehadiran')">
            <a href="javascript:void(0)"><span class="fa fa-firefox"></span>Kehadiran ▼</span></a>
            <ul id="dropdown-kehadiran" style="display: none;" data-widget="tree">
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/log_absen') }}"><span class="fa fa-firefox"></span>Hari Kerja</span></a></li>
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/absen_non_kerja') }}"><span class="fa fa-firefox"></span>Non Hari Kerja</span></a></li>
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/akumulasi') }}"><span class="fa fa-firefox"></span>Akumulasi Kehadiran</span></a></li>
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/lebihKerja') }}"><span class="fa fa-firefox"></span>Jam Kerja Lebih</span></a></li>
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/kurangKerja') }}"><span class="fa fa-firefox"></span>Jam Kerja Kurang</span></a></li>
            </ul>
        </li>
        <li class="menu-sidebar"><a href="{{ url('/ketidakhadiran') }}"><span class="fa fa-firefox"></span>Ketidakhadiran</span></a></li>

        <li class="menu-sidebar" onclick="toggleDropdown('dropdown-lembur')">
            <a href="javascript:void(0)"><span class="fa fa-firefox"></span>Lembur ▼</span></a>
            <ul id="dropdown-lembur" style="display: none;">
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/lembur') }}"><span class="fa fa-firefox"></span>Lembur</span></a></li>
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/akumulasiLembur') }}"><span class="fa fa-firefox"></span>Akumulasi Lembur</span></a></li>
            </ul>
        </li>

        <li class="menu-sidebar"><a href="{{ url('/cuti') }}"><span class="fa fa-firefox"></span>Cuti</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/rules') }}"><span class="fa fa-firefox"></span>Peraturan</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/role') }}"><span class="fa fa-firefox"></span>Role</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/libur') }}"><span class="fa fa-firefox"></span>Libur Nasional</span></a></li>
        <li class="menu-sidebar"><a href="{{ url('/akumulasi_tahunan') }}"><span class="fa fa-firefox"></span>Akumulasi Tahunan</span></a></li>

        <li class="menu-sidebar" onclick="toggleDropdown('dropdown-log')">
            <a href="javascript:void(0)"><span class="fa fa-firefox"></span>Log ▼</span></a>
            <ul id="dropdown-log" style="display: none;">
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/log_activity') }}"><span class="fa fa-firefox"></span>Log Aktivitas</span></a></li>
              <li class="menu-sidebar" style="margin-bottom: 10px;"><a href="{{ url('/log_kegiatan') }}"><span class="fa fa-firefox"></span>Log Kegiatan</span></a></li>
            </ul>
        </li>
        @endif


  </ul>
  <script>
    function toggleDropdown(id) {
        var dropdown = document.getElementById(id);
        dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
    }
</script>
    </section>