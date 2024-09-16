<?php

$role = $_SESSION['role'];  // ดึง role ของผู้ใช้จาก session
$team_id = $_SESSION['team_id'];  // ดึง team_id ของผู้ใช้จาก session
$user_id = $_SESSION['user_id'];  // ดึง user_id ของผู้ใช้จาก session
$first_name = $_SESSION['first_name']; // ดึง first_name ของผู้ใช้จาก session
$lastname = $_SESSION['last_name']; // ดึง last_name ของผู้ใช้จาก session

?>

<!-- Preloader -->
<div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="<?php echo BASE_URL; ?>assets/img/pitt.png" alt="Account Magement" height="60" width="60">
</div>


<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?php echo BASE_URL; ?>index.php" class="nav-link">Home</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
                <form class="form-inline">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">15</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">15 Notifications</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> 4 new messages
                    <span class="float-right text-muted text-sm">3 mins</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-users mr-2"></i> 8 friend requests
                    <span class="float-right text-muted text-sm">12 hours</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-file mr-2"></i> 3 new reports
                    <span class="float-right text-muted text-sm">2 days</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
    </ul>
</nav>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo BASE_URL; ?>index.php" class="brand-link bg-dark bg-primary bg-danger ">
        <img src="<?php echo BASE_URL; ?>assets/img/inoo.png" alt="INO Management" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">INO Management</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?php echo BASE_URL; ?>assets/img/ad.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo ($_SESSION['first_name']); ?>&nbsp;&nbsp;<?php echo ($_SESSION['last_name']); ?></a>
                <a href="#" class="d-block"><?php echo ($_SESSION['role']); ?></a>
                <a href="<?php echo BASE_URL; ?>logout.php" class=""><i class="nav-icon fa fa-sign-in"> Logout</i></a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <!-- <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div> -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent nav-collapse-hide-child" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <!-- <li class="nav-header">Menu</li> -->
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>index.php" class="nav-link <?php if ($menu == "index") {
                                                                                    echo "active";
                                                                                } ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <?php if ($_SESSION["role"] == "Executive" || $_SESSION["role"] == "Sale Supervisor") { ?>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>pages/account/account.php" class="nav-link <?php if ($menu == "account") {
                                                                                                        echo "active";
                                                                                                    } ?> ">
                            <i class="nav-icon fas fa-user"></i>
                            <p>
                                Account
                            </p>
                        </a>
                    </li>
                <?php } ?>
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>pages/project/project.php" class="nav-link <?php if ($menu == "project") {
                                                                                                    echo "active";
                                                                                                } ?> ">
                        <i class="nav-icon fas fa-folder-open"></i>
                        <p>
                            Project
                            <span class="right badge badge-danger">New</span>
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>pages/account/customer.php" class="nav-link <?php if ($menu == "customer") {
                                                                                                    echo "active";
                                                                                                } ?> ">
                        <i class="nav-icon fa fa-book"></i>
                        <p>
                            Customer
                        </p>
                    </a>
                </li>
                <li class="nav-header text-primary">Service</li>
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>pages/service/category.php" class="nav-link <?php if ($menu == "category") {
                                                                                                    echo "active";
                                                                                                } ?> ">
                        <i class="nav-icon far fa-copy"></i>
                        <p>
                            Service Category
                            <span class="right badge badge-danger">New</span>
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>pages/service/service.php" class="nav-link <?php if ($menu == "service") {
                                                                                                    echo "active";
                                                                                                } ?> ">
                        <i class="nav-icon fas fa-receipt"></i>
                        <p>
                            IT Service
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>pages/service/inventory.php" class="nav-link <?php if ($menu == "inventory") {
                                                                                                    echo "active";
                                                                                                } ?> ">
                        <i class="nav-icon fas fa-desktop"></i>
                        <p>
                            Inventory
                        </p>
                    </a>
                </li>
                <li class="nav-header text-primary">Setting</li>
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>pages/setting/profile.php" class="nav-link <?php if ($menu == "profile") {
                                                                                                    echo "active";
                                                                                                } ?> ">
                        <i class="nav-icon fas fa-address-card"></i>
                        <p>
                            Profile
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="https://adminlte.io/docs/3.1/" class="nav-link">
                        <i class="nav-icon fas fa-file"></i>
                        <p>Documentation</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->

    </div>
    <!-- /.sidebar -->
</aside>