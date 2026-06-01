<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <title>@yield('title', 'PAK NAMAK & MASALA JAAT')</title>
    <base href="{{ asset("assets") }}/" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&amp;display=fallback">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min2167.css?v=3.2.0">
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* ── PAK NAMAK GREEN THEME OVERRIDE ── */
        :root {
            --pn-dark:   #0a2e18;
            --pn-mid:    #1a5c35;
            --pn-light:  #2d7a4f;
            --pn-pale:   #e8f5ee;
        }

        /* Sidebar */
        .main-sidebar, .sidebar-dark-primary { background: var(--pn-dark) !important; }
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active,
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link:focus,
        .sidebar-dark-primary .nav-sidebar > .nav-item.menu-open > .nav-link {
            background: var(--pn-light) !important;
            color: #fff !important;
        }
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link:hover {
            background: var(--pn-mid) !important;
            color: #fff !important;
        }
        .sidebar-dark-primary .nav-treeview > .nav-item > .nav-link.active,
        .sidebar-dark-primary .nav-treeview > .nav-item > .nav-link:hover {
            background: rgba(255,255,255,.1) !important;
            color: #fff !important;
        }
        .sidebar-dark-primary .nav-treeview > .nav-item > .nav-link.active .nav-icon,
        .sidebar-dark-primary .nav-treeview > .nav-item > .nav-link:hover .nav-icon {
            color: #fff !important;
        }
        .brand-link { border-bottom-color: rgba(255,255,255,.1) !important; }

        /* Top navbar */
        .main-header.navbar { border-bottom: 2px solid var(--pn-pale) !important; }

        /* Buttons */
        .btn-primary { background: var(--pn-mid) !important; border-color: var(--pn-mid) !important; }
        .btn-primary:hover { background: var(--pn-light) !important; border-color: var(--pn-light) !important; }
        .btn-outline-primary { color: var(--pn-mid) !important; border-color: var(--pn-mid) !important; }
        .btn-outline-primary:hover { background: var(--pn-mid) !important; color: #fff !important; }

        /* Badges & accents */
        .badge-primary, .bg-primary { background: var(--pn-mid) !important; }
        .text-primary { color: var(--pn-mid) !important; }
        .border-left-primary { border-left-color: var(--pn-mid) !important; }

        /* Progress bars */
        .progress-bar.bg-primary { background: var(--pn-mid) !important; }

        /* Links */
        a.text-primary, a[style*="4e73df"], a[style*="224abe"] { color: var(--pn-mid) !important; }

        /* Content wrapper background */
        .content-wrapper { background: #f2f5f3 !important; }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="{{ asset('assets/images/logo.png') }}" alt="AdminLTELogo" height="60" width="60">
        </div>
        @include('admin.layout.header')
        @include('admin.layout.sidebar')
        <div class="content-wrapper">
            @yield('content')
        </div>
        @include('admin.layout.footer')
    </div>
    <script type="text/javascript">
        var APP_URL = {!! json_encode(url('/')) !!}
        var ASSET_URL = {!! json_encode(asset('/')) !!}
    </script>
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/jquery-ui/jquery-ui.min.js"></script>
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="plugins/jszip/jszip.min.js"></script>
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <script src="dist/js/adminlte2167.js?v=3.2.0"></script>
    <script src="dist/js/demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };
    </script>
    @yield('scripts')
</body>
</html>
