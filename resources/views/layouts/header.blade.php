@php
  $user = Auth::user();
  $initials = '';
  if ($user) {
      $names = explode(' ', $user->name);
      $initials = strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
  }
@endphp


<header class="bunrek-header">
  <div class="header-left">
    <button class="header-toggle" aria-label="Toggle Sidebar">
      <i class="bi bi-list"></i>
    </button>
    <span class="header-title">@yield('page_title', 'Aplikasi Budget Tracker')</span>
  </div>

  @if($user)
    <div class="header-user">
      <span class="header-user-name d-none d-sm-inline">{{ $user->name }}</span>
      <div class="header-avatar">
        {{ $initials }}
      </div>
    </div>
  @endif
</header>
