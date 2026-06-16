@if(!empty($trend['hasData']))
  @php
    $trendClass = $trend['direction'] === 'flat'
        ? 'trend-flat'
        : ($trend['favorable'] ? 'trend-good' : 'trend-bad');
    $trendIcon = $trend['direction'] === 'up'
        ? 'bi-arrow-up-short'
        : ($trend['direction'] === 'down' ? 'bi-arrow-down-short' : 'bi-dash');
  @endphp
  <div class="metric-trend {{ $trendClass }}" title="vs bulan lalu">
    <i class="bi {{ $trendIcon }}"></i>{{ $trend['percent'] }}
  </div>
@endif
