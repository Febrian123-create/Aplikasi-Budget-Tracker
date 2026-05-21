<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BUNREK - Aplikasi Budget Tracker</title>
    
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    
    <link rel="stylesheet" href="{{ asset('css/bunrek-tokens.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/bunrek-app.css') }}" />

    @stack('styles')
  </head>
  <body>
    <div class="bunrek-wrapper">
      
      <div class="sidebar-backdrop"></div>

      
      @include('layouts.sidebar')

      
      <div class="bunrek-main">
        
        @include('layouts.header')

        
        <main class="bunrek-content">
          @yield('content')
        </main>

        
        @include('layouts.footer')
      </div>
    </div>

    
    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>

    
    <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>

    
    <script>
      $(document).ready(function() {
        $('.header-toggle, .sidebar-backdrop').on('click', function() {
          $('.bunrek-sidebar').toggleClass('open');
          $('.sidebar-backdrop').toggleClass('active');
        });
      });
    </script>
    @stack('scripts')
  </body>
</html>
