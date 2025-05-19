<!--**********************************
            Sidebar start
        ***********************************-->
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
            <a href="index.html" class="app-brand-link">
              <span class="app-brand-logo demo">
                <img src="<?= base_url('assets/themes/admin/images/logonu.png') ?>" alt="Logo" width="160">
              </span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
              <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1 fw-bold">
            <!-- Dashboard -->
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Dashboard</span></li>
            <li class="menu-item ">
              <a href="/dashboard" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
              </a>
            </li>
            <!-- Forms & Tables -->
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Data</span></li>
            <!-- Layouts -->
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user-pin"></i>
                <div data-i18n="Layouts">Komunitas</div>
              </a>

              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="/citizen" class="menu-link">
                    <div data-i18n="Without menu">Warga</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="/officers" class="menu-link">
                    <div data-i18n="Without menu">Petugas Lapangan</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="/beneficaries" class="menu-link">
                    <div data-i18n="Without menu">Penerima Manfaat</div>
                  </a>
                </li>
              </ul>
            </li>

            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">Kegiatan Sosial</div>
              </a>

              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="/programs" class="menu-link">
                    <div data-i18n="Without navbar">Program</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="/donations" class="menu-link">
                    <div data-i18n="Without navbar">Donasi Warga</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="/fund-distribution" class="menu-link">
                    <div data-i18n="Without navbar">Distribusi Dana</div>
                  </a>
                </li>
              </ul>
            </li>

            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-collection"></i>
                <div data-i18n="Layouts">Laporan</div>
              </a>

              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="/officers-commision" class="menu-link">
                    <div data-i18n="Without navbar">Komisi Petugas</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="/transaction" class="menu-link">
                    <div data-i18n="Container">Keuangan</div>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </aside>
        <!-- / Menu -->

          <!-- Content wrapper -->
          <div class="content-wrapper">		
		

<!--**********************************
            Sidebar end
        ***********************************-->