<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">

  <title>Online Approval for GCash Transactions</title>

  <meta name="description" content="Gcash-Portal for EverFirst">
  <meta name="author" content="Astra">
  <meta name="robots" content="index, follow">

  <!-- Open Graph Meta -->
  <meta property="og:title" content="Online Approval for GCash Transaction">
  <meta property="og:site_name" content="Gcash Transaction">
  <meta property="og:description" content="Gcash transactions for Everfirst">
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://phpstack-1258925-4698409.cloudwaysapps.com/">
  <meta property="og:image" content="">

  <!-- Icons -->
  <link rel="shortcut icon" href="{{ asset('public/media/favicons/favicon.png') }}">
  <link rel="icon" sizes="192x192" type="image/png" href="{{ asset('media/favicons/ef_favicon.png') }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('public/media/favicons/apple-touch-icon-180x180.png') }}">


  <link rel="stylesheet" id="css-main" href="{{asset('js/codebase.min.css')}}">
  <!-- Modules -->

  @vite(['resources/sass/main.scss', 'resources/js/codebase/app.js'])

  <!-- Alternatively, you can also include a specific color theme after the main stylesheet to alter the default color theme of the template -->
  {{-- @vite(['resources/sass/main.scss', 'resources/sass/codebase/themes/corporate.scss', 'resources/js/codebase/app.js']) --}}
  @yield('js')
</head>

<body>
  <!-- Page Container -->
  <!--
    Available classes for #page-container:

    SIDEBAR & SIDE OVERLAY

      'sidebar-r'                                 Right Sidebar and left Side Overlay (default is left Sidebar and right Side Overlay)
      'sidebar-mini'                              Mini hoverable Sidebar (screen width > 991px)
      'sidebar-o'                                 Visible Sidebar by default (screen width > 991px)
      'sidebar-o-xs'                              Visible Sidebar by default (screen width < 992px)
      'sidebar-dark'                              Dark themed sidebar

      'side-overlay-hover'                        Hoverable Side Overlay (screen width > 991px)
      'side-overlay-o'                            Visible Side Overlay by default

      'enable-page-overlay'                       Enables a visible clickable Page Overlay (closes Side Overlay on click) when Side Overlay opens

      'side-scroll'                               Enables custom scrolling on Sidebar and Side Overlay instead of native scrolling (screen width > 991px)

    HEADER

      ''                                          Static Header if no class is added
      'page-header-fixed'                         Fixed Header

    HEADER STYLE

      ''                                          Classic Header style if no class is added
      'page-header-modern'                        Modern Header style
      'page-header-dark'                          Dark themed Header (works only with classic Header style)
      'page-header-glass'                         Light themed Header with transparency by default
                                                  (absolute position, perfect for light images underneath - solid light background on scroll if the Header is also set as fixed)
      'page-header-glass page-header-dark'        Dark themed Header with transparency by default
                                                  (absolute position, perfect for dark images underneath - solid dark background on scroll if the Header is also set as fixed)

    MAIN CONTENT LAYOUT

      ''                                          Full width Main Content if no class is added
      'main-content-boxed'                        Full width Main Content with a specific maximum width (screen width > 1200px)
      'main-content-narrow'                       Full width Main Content with a percentage width (screen width > 1200px)

    DARK MODE

      'sidebar-dark page-header-dark dark-mode'   Enable dark mode (light sidebar/header is not supported with dark mode)
  -->
  <div id="page-container" class="main-content-boxed">
    <!-- Main Container -->
    <main id="main-container">
      @yield('content')
    </main>
    <!-- END Main Container -->
  </div>
  <!-- END Page Container -->

  <script src="{{asset('js/codebase.app.min.js')}}"></script>

    <!-- jQuery (required for BS Notify plugin) -->
    <script src="{{asset('js/lib/jquery.min.js')}}"></script>

    {{-- <!-- Page JS Plugins -->
    <script src="{{asset('public/js/plugins/bootstrap-notify/bootstrap-notify.min.js')}}"></script>

    <!-- Page JS Helpers (BS Notify Plugin) -->
    <script>Codebase.helpersOnLoad(['jq-notify']);</script> --}}
</body>

</html>
