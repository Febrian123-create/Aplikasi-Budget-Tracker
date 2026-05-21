<!-- Sidebar -->
<div class="sidebar" data-background-color="dark">
  <div class="sidebar-logo">
    <!-- Logo Header -->
    <div class="logo-header" data-background-color="dark">
      <a href="index.html" class="logo">
        <img
          src="{{ asset('assets/img/kaiadmin/logo_light.svg') }}"
          alt="navbar brand"
          class="navbar-brand"
          height="20"
        />
      </a>
      <div class="nav-toggle">
        <button class="btn btn-toggle toggle-sidebar">
          <i class="gg-menu-right"></i>
        </button>
        <button class="btn btn-toggle sidenav-toggler">
          <i class="gg-menu-left"></i>
        </button>
      </div>
      <button class="topbar-toggler more">
        <i class="gg-more-vertical-alt"></i>
      </button>
    </div>
    <!-- End Logo Header -->
  </div>
  <div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">
      <ul class="nav nav-secondary">
        <li class="nav-item active">
          <a
            data-bs-toggle="collapse"
            href="#dashboard"
            class="collapsed"
            aria-expanded="false"
          >
            <i class="fas fa-home"></i>
            <p>Dashboard</p>
            <span class=""></span>
          </a>
        </li>
        
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#base">
            <i class="fas fa-wallet"></i>
            <p>Keuangan</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="base">
            <ul class="nav nav-collapse">
              <li>
                <a href="{{ route('transactions.index') }}">
                  <span class="sub-item">Transaction</span>
                </a>
              </li>
              <li>
                <a href="{{ route('transactions.history') }}">
                  <span class="sub-item">History</span>
                </a>
              </li>
              <li>
                <a href="{{ route('charts.index') }}">
                  <span class="sub-item">
                    Visualisasi 
                    @if(!app(\App\Features\MembershipFeatureInterface::class)->canViewChart())
                      <i class="fas fa-lock text-warning ml-2"></i>
                    @endif
                  </span>
                </a>
              </li>
              <li>
                <a href="{{ route('recurring.index') }}">
                  <span class="sub-item">Transaksi Rutin</span>
                </a>
              </li>
              <li>
                <a href="{{ route('membership.index') }}">
                  <span class="sub-item"><b>Membership</b></span>
                </a>
              </li>
            </ul>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>
<!-- End Sidebar -->
