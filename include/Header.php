    <!-- Google Fonts: Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
    <!-- Font Awesome (CDN Backup) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Font Awesome (Local) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/plugins/summernote/summernote-bs4.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- sweetalert -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/adminlte.min.css">

    <!-- Toastr -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/plugins/toastr/toastr.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

    <!-- Custom Fonts CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/fonts.css">

    <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>assets/img/favicon.ico">

    <!-- Fix Tempusdominus DateTimePicker Icons for Font Awesome 6 -->
    <style>
        /* Force Font Awesome 6 for all picker icons */
        .bootstrap-datetimepicker-widget .btn[data-action] span {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900 !important;
            font-style: normal !important;
            display: inline-block;
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
        }

        /* Hide old icon content and show new ones */
        .bootstrap-datetimepicker-widget .btn[data-action] span:before {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900 !important;
            display: inline-block !important;
        }

        /* Up arrow for increment time */
        .bootstrap-datetimepicker-widget .btn[data-action="incrementHours"] span,
        .bootstrap-datetimepicker-widget .btn[data-action="incrementMinutes"] span {
            font-size: 0 !important;
        }
        .bootstrap-datetimepicker-widget .btn[data-action="incrementHours"] span:before,
        .bootstrap-datetimepicker-widget .btn[data-action="incrementMinutes"] span:before {
            content: "\f062" !important;
            font-size: 1rem !important;
        }

        /* Down arrow for decrement time */
        .bootstrap-datetimepicker-widget .btn[data-action="decrementHours"] span,
        .bootstrap-datetimepicker-widget .btn[data-action="decrementMinutes"] span {
            font-size: 0 !important;
        }
        .bootstrap-datetimepicker-widget .btn[data-action="decrementHours"] span:before,
        .bootstrap-datetimepicker-widget .btn[data-action="decrementMinutes"] span:before {
            content: "\f063" !important;
            font-size: 1rem !important;
        }

        /* Previous month chevron */
        .bootstrap-datetimepicker-widget .btn[data-action="previous"] span {
            font-size: 0 !important;
        }
        .bootstrap-datetimepicker-widget .btn[data-action="previous"] span:before {
            content: "\f053" !important;
            font-size: 1rem !important;
        }

        /* Next month chevron */
        .bootstrap-datetimepicker-widget .btn[data-action="next"] span {
            font-size: 0 !important;
        }
        .bootstrap-datetimepicker-widget .btn[data-action="next"] span:before {
            content: "\f054" !important;
            font-size: 1rem !important;
        }

        /* Today button */
        .bootstrap-datetimepicker-widget .btn[data-action="today"] span {
            font-size: 0 !important;
        }
        .bootstrap-datetimepicker-widget .btn[data-action="today"] span:before {
            content: "\f073" !important;
            font-size: 1rem !important;
        }

        /* Clear button */
        .bootstrap-datetimepicker-widget .btn[data-action="clear"] span {
            font-size: 0 !important;
        }
        .bootstrap-datetimepicker-widget .btn[data-action="clear"] span:before {
            content: "\f2ed" !important;
            font-size: 1rem !important;
        }

        /* Close button */
        .bootstrap-datetimepicker-widget .btn[data-action="close"] span {
            font-size: 0 !important;
        }
        .bootstrap-datetimepicker-widget .btn[data-action="close"] span:before {
            content: "\f00d" !important;
            font-size: 1rem !important;
        }

        /* Toggle picker button */
        .bootstrap-datetimepicker-widget .btn[data-action="togglePicker"] span {
            font-size: 0 !important;
        }
        .bootstrap-datetimepicker-widget .btn[data-action="togglePicker"] span:before {
            content: "\f017" !important;
            font-size: 1rem !important;
        }
    </style>