@php
  $user = Auth::user();
  $initials = '';
  if ($user) {
      $names = explode(' ', $user->name);
      $initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
  }
  $membershipName = $user && $user->membership ? $user->membership->membership_name : 'Free';
  $isPremium = $user && $user->membership_id == 2;
  $membershipFeature = app(\App\Features\MembershipFeatureInterface::class);
  $canViewChart = $membershipFeature->canViewChart();
  $canUseRecurring = $membershipFeature->canUseRecurring();
@endphp


<aside class="bunrek-sidebar">
  <div class="sidebar-brand">
    <div class="sidebar-brand-icon">
      <i class="bi bi-wallet2"></i>
    </div>
    <span class="sidebar-brand-text">BUNREK</span>
  </div>

  <nav class="sidebar-nav">
    <ul class="sidebar-nav-main">
      <li>
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li>
        <a href="{{ route('transactions.index') }}" class="sidebar-link {{ request()->routeIs('transactions.index') ? 'active' : '' }}">
          <i class="bi bi-credit-card"></i>
          <span>Transaksi</span>
        </a>
      </li>
      <li>
        <a href="{{ route('transactions.history') }}" class="sidebar-link {{ request()->routeIs('transactions.history') ? 'active' : '' }}">
          <i class="bi bi-clock-history"></i>
          <span>Riwayat</span>
        </a>
      </li>
      @if($canViewChart)
        <li>
          <a href="{{ route('charts.index') }}" class="sidebar-link {{ request()->routeIs('charts.index') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i>
            <span>Visualisasi</span>
          </a>
        </li>
      @endif
      @if($canUseRecurring)
        <li>
          <a href="{{ route('recurring.index') }}" class="sidebar-link {{ request()->routeIs('recurring.index') ? 'active' : '' }}">
            <i class="bi bi-arrow-repeat"></i>
            <span>Transaksi Rutin</span>
          </a>
        </li>
      @endif
      <li>
        <a href="{{ route('membership.index') }}" class="sidebar-link {{ request()->routeIs('membership.index') ? 'active' : '' }}">
          <i class="bi bi-gem"></i>
          <span>Membership</span>
        </a>
      </li>
    </ul>

    <ul class="sidebar-nav-bottom">
      <li>
        <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
          <i class="bi bi-gear"></i>
          <span>Pengaturan</span>
        </a>
      </li>
      <li>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="sidebar-link">
          <i class="bi bi-box-arrow-right"></i>
          <span>Keluar</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          @csrf
        </form>
      </li>
    </ul>
  </nav>

  @if($user)
    <div class="sidebar-user">
      <div class="sidebar-avatar">
        {{ $initials }}
      </div>
      <div class="sidebar-user-info">
        <div class="sidebar-user-name">{{ $user->name }}</div>
        <span class="sidebar-user-plan {{ $isPremium ? 'plan-premium' : 'plan-free' }}">
          {{ $membershipName }}
        </span>
      </div>
    </div>
  @endif
</aside>
