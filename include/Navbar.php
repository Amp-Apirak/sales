<?php

$role = $_SESSION['role'];  // ดึง role ของผู้ใช้จาก session
$team_id = $_SESSION['team_id'];  // ดึง team_id ของผู้ใช้จาก session
$user_id = $_SESSION['user_id'];  // ดึง user_id ของผู้ใช้จาก session
$first_name = $_SESSION['first_name']; // ดึง first_name ของผู้ใช้จาก session
$lastname = $_SESSION['last_name']; // ดึง last_name ของผู้ใช้จาก session
$profile_image = $_SESSION['profile_image']; // ดึง profile_image ของผู้ใช้จาก session

?>



<!-- Preloader -->
<div class="preloader">
    <div class="preloader-content">
        <img class="preloader-logo" src="<?php echo BASE_URL; ?>assets/img/pitt.png" alt="Account Management">
        <div class="preloader-spinner"></div>
        <div class="preloader-text">กำลังโหลด...</div>
    </div>
</div>


<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block ">
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

<!-- LOGO -->
<style>
    /* แก้ไขส่วน Brand Logo */
    .brand-link {
        /* เพิ่มพื้นหลังสีดำเข้มตลอดเวลา */
        background-color: #1a1a1a !important;
        /* สีดำเข้ม */
        border-bottom: 1px solid #4b4b4b !important;
        /* เส้นคั่นด้านล่าง */
        transition: all 0.3s ease;
        /* เพิ่มการเปลี่ยนแปลงแบบนุ่มนวล */
    }

    /* เมื่อ hover ที่ brand-link */
    .brand-link:hover {
        background-color: #000000 !important;
        /* สีดำสนิทเมื่อ hover */
    }

    /* ปรับ brand-text ให้เข้ากับพื้นหลังสีดำ */
    .brand-text {
        /* ขนาดตัวอักษร */
        font-size: 1rem;

        /* เอฟเฟกต์สีและการไล่ระดับ - ปรับให้สว่างขึ้น */
        background: linear-gradient(to right, #FFD700, #FFF8DC, #DAA520, #FFFACD, #B8860B);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;

        /* เอฟเฟกต์เพิ่มเติม */
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        letter-spacing: 1px;
        font-weight: bold;

        /* เพิ่มแอนิเมชั่น */
        animation: goldShine 3s infinite;
        transition: all 0.3s ease;
    }

    /* ปรับแอนิเมชั่นให้มีความสว่างมากขึ้น */
    @keyframes goldShine {
        0% {
            filter: brightness(100%);
        }

        50% {
            filter: brightness(130%);
        }

        100% {
            filter: brightness(100%);
        }
    }

    /* เอฟเฟกต์ hover ให้สว่างขึ้น */
    .brand-text:hover {
        transform: scale(1.05);
        text-shadow: 3px 3px 6px rgba(255, 215, 0, 0.3);
        filter: brightness(120%);
    }

    .brand-image {
        box-shadow: 0 0 10px rgba(255, 215, 0, 0.5) !important;
        border: 2px solid #FFD700 !important;
        padding: 2px !important;
        background: rgba(255, 215, 0, 0.1) !important;
        transition: all 0.3s ease !important;
    }

    /* แอนิเมชั่นความสว่าง */
    @keyframes goldShine {
        0% {
            filter: brightness(100%);
        }

        50% {
            filter: brightness(130%);
        }

        100% {
            filter: brightness(100%);
        }
    }
</style>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo BASE_URL; ?>index.php" class="brand-link bg-dark">
        <img src="<?php echo BASE_URL; ?>assets/img/pit3.png" alt="POINT IT INNOVATION" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text"><b>POINT IT INNOVATION</b></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <style>
            .user-panel {
                display: flex;
                /* กำหนดการแสดงผลแบบยืดหยุ่น (flexbox) */
                flex-direction: column;
                /* จัดเรียงองค์ประกอบในแนวตั้ง */
                align-items: center;
                /* จัดตำแหน่งองค์ประกอบให้อยู่ตรงกลาง */
                padding: 15px;
                /* กำหนดระยะห่างภายในขององค์ประกอบ */
                text-align: center;
                /* จัดข้อความให้อยู่กึ่งกลาง */
            }

            .user-panel .image {
                margin-bottom: 10px;
                /* เพิ่มระยะห่างด้านล่างของรูป */
            }

            .user-panel .image img {
                max-width: 80px;
                /* กำหนดความกว้างสูงสุดของรูปที่ 80px */
                width: 100%;
                /* กำหนดความกว้างเต็มที่ของรูป */
                height: auto;
                /* กำหนดความสูงตามสัดส่วนของรูป */
                min-width: 40px;
                /* กำหนดความกว้างขั้นต่ำที่ 40px เพื่อป้องกันรูปไม่เล็กเกินไป */
                border-radius: 50%;
                /* ทำให้รูปเป็นวงกลม */
                border: 2px solid #fff;
                /* เพิ่มขอบสีขาวที่ความหนา 2px */
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                /* เพิ่มเงาให้กับรูป */
                object-fit: cover;
                /* ทำให้รูปครอบคลุมพื้นที่โดยไม่ถูกยืด */
            }

            .user-panel .info {
                display: flex;
                /* ใช้ flexbox ในการจัดวางข้อมูล */
                flex-direction: column;
                /* จัดเรียงข้อมูลในแนวตั้ง */
                align-items: center;
                /* จัดตำแหน่งข้อมูลให้อยู่กึ่งกลาง */
            }

            .user-panel .info .d-block {
                color: #333;
                /* กำหนดสีตัวอักษรเป็นสีเทาเข้ม */
                text-decoration: none;
                /* ไม่ต้องมีขีดเส้นใต้ */
                margin: 2px 0;
                /* เพิ่มระยะห่างบน-ล่าง 2px */
            }

            .user-panel .info .user-name {
                font-weight: bold;
                /* ทำให้ชื่อผู้ใช้หนาขึ้น */
                font-size: 1em;
                /* กำหนดขนาดตัวอักษรที่ 1em */
            }

            .user-panel .info .user-role {
                font-size: 0.9em;
                /* กำหนดขนาดตัวอักษรที่ 0.9em */
                color: #666;
                /* กำหนดสีตัวอักษรเป็นสีเทาอ่อน */
            }

            .logout-btn {
                width: 50vw;
                margin-top: 10px;
                /* เพิ่มระยะห่างด้านบน 10px */
                padding: 5px 10px;
                /* เพิ่มระยะห่างภายใน (padding) ของปุ่ม */
                background-color: #f8f9fa;
                /* กำหนดสีพื้นหลังเป็นสีเทาอ่อน */
                color: #343a40;
                /* กำหนดสีตัวอักษรเป็นสีเทาเข้ม */
                border-radius: 5px;
                /* ทำให้มุมของปุ่มโค้งมน */
                text-decoration: none;
                /* ไม่ต้องมีขีดเส้นใต้ */
                transition: all 0.3s ease;
                /* เพิ่มการเปลี่ยนแปลงของปุ่มอย่างนุ่มนวล */
                font-size: 0.8em;
                /* กำหนดขนาดตัวอักษรของปุ่ม */
                border: 1px solid #dee2e6;
                /* เพิ่มขอบบาง ๆ ให้กับปุ่ม */
            }

            .logout-btn:hover {
                background-color: #e9ecef;
                /* เปลี่ยนสีพื้นหลังเมื่อเอาเมาส์ชี้ */
                color: #dc3545;
                /* เปลี่ยนสีตัวอักษรเป็นสีแดงเมื่อเอาเมาส์ชี้ */
            }

            .logout-btn i {
                margin-right: 5px;
                /* เพิ่มระยะห่างทางขวาของไอคอนในปุ่ม */
            }

            /* การปรับแต่งสำหรับหน้าจอที่มีความกว้างน้อยกว่า 768px */
            @media (max-width: 768px) {
                .user-panel .image img {
                    max-width: 70px;
                    /* ปรับขนาดรูปให้เล็กลงเมื่อหน้าจอแคบลง */
                }

                .user-panel .info .user-name,
                .user-panel .info .user-role {
                    font-size: 0.9em;
                    /* ปรับขนาดตัวอักษรให้เล็กลง */
                }

                .logout-btn {
                    font-size: 0.75em;
                    /* ปรับขนาดตัวอักษรของปุ่มให้เล็กลง */
                    padding: 4px 8px;
                    /* ปรับระยะห่างภายในของปุ่มให้เล็กลง */
                }
            }

            /* การปรับแต่งสำหรับหน้าจอที่มีความกว้างน้อยกว่า 576px */
            @media (max-width: 576px) {
                .user-panel .image img {
                    max-width: 60px;
                    /* ปรับขนาดรูปให้เล็กลงอีกเมื่อหน้าจอเล็กลงมาก */
                }

                .user-panel .info .user-name,
                .user-panel .info .user-role {
                    font-size: 0.8em;
                    /* ปรับขนาดตัวอักษรให้เล็กลงอีกเมื่อหน้าจอเล็กมาก */
                }

                .logout-btn {
                    font-size: 0.7em;
                    /* ปรับขนาดตัวอักษรของปุ่มให้เล็กลงที่สุด */
                    padding: 3px 6px;
                    /* ปรับระยะห่างภายในปุ่มให้เล็กลงที่สุด */
                }
            }
        </style>

        <div class="user-panel">
            <div class="image">
                <?php
                // ตรวจสอบว่ามี profile image หรือไม่ หากไม่มีให้ใช้ภาพเริ่มต้น
                $profile_image = !empty($_SESSION['profile_image']) ? BASE_URL . 'uploads/profile_images/' . htmlspecialchars($_SESSION['profile_image']) : BASE_URL . 'assets/img/add.jpg';
                ?>
                <img src="<?php echo $profile_image; ?>" alt="User Image">
            </div>
            <div class="info">
                <?php
                // ตรวจสอบว่ามี team_name ใน session หรือไม่
                $team_display = isset($_SESSION['team_name']) ? $_SESSION['team_name'] : 'No Team';
                ?>
                <a href="<?php echo BASE_URL; ?>pages/profile/profile.php" class="d-block user-name">
                    <?php echo htmlspecialchars($team_display); ?> team
                </a>
                <a href="<?php echo BASE_URL; ?>pages/profile/profile.php" class="d-block user-role"><b>Name :</b> <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></a>
                <a href="<?php echo BASE_URL; ?>pages/profile/profile.php" class="d-block user-role"><b>Role :</b> <?php echo htmlspecialchars($_SESSION['role']); ?></a>
            </div>
            <a href="<?php echo BASE_URL; ?>logout.php" class="logout-btn info">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
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
        <nav class="mt-3">
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
                    <a href="<?php echo BASE_URL; ?>pages/customer/customer.php" class="nav-link <?php if ($menu == "customer") {
                                                                                                        echo "active";
                                                                                                    } ?> ">
                        <i class="nav-icon fa fa-book"></i>
                        <p>
                            Customer
                        </p>
                    </a>
                </li>
                <!-- <li class="nav-header text-primary">Service</li> -->

                <!-- <?php if ($role === 'Engineer'): ?>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>pages/category/category.php" class="nav-link <?php if ($menu == "category") {
                                                                                                            echo "active";
                                                                                                        } ?> ">
                            <i class="nav-icon far fa-copy"></i>
                            <p>
                                Service Category
                                <span class="right badge badge-danger">New</span>
                            </p>
                        </a>
                    </li>
                <?php endif; ?> -->

                <!-- <li class="nav-item">
                    <a href="#" class="nav-link <?php if ($menu == "service") {
                                                    echo "active";
                                                } ?> ">
                        <i class="nav-icon fas fa-receipt"></i>
                        <p>
                            IT Service
                        </p>
                    </a>
                </li> -->

                <li class="nav-header text-primary">Setting</li>
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>pages/setting/suppliers/supplier.php" class="nav-link <?php if ($menu == "supplier") {
                                                                                                                echo "active";
                                                                                                            } ?> ">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>
                            Supplier
                        </p>
                    </a>
                </li>
                <?php if ($role != 'Engineer'): ?>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>pages/setting/product/product.php" class="nav-link <?php if ($menu == "product") {
                                                                                                                echo "active";
                                                                                                            } ?> ">
                            <i class="nav-icon fas fa-box-open"></i>
                            <p>
                                Product Point
                            </p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role != 'AA'): ?>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>pages/setting/employees/employees.php" class="nav-link <?php if ($menu == "employees") {
                                                                                                                    echo "active";
                                                                                                                } ?>">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Employees</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role === 'Executive'): ?>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>pages/setting/team/team.php" class="nav-link <?php if ($menu == "team") {
                                                                                                            echo "active";
                                                                                                        } ?> ">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Team
                            </p>
                        </a>
                    </li>
                <?php endif; ?>



                <!-- <li class="nav-item">
                    <a href="https://adminlte.io/docs/3.1/" class="nav-link">
                        <i class="nav-icon fas fa-file"></i>
                        <p>Documentation</p>
                    </a>
                </li> -->
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>pages/profile/profile.php" class="nav-link <?php if ($menu == "profile") {
                                                                                                    echo "active";
                                                                                                } ?> ">
                        <i class="nav-icon fas fa-address-card"></i>
                        <p>
                            Profile
                        </p>
                    </a>
                </li>
            </ul>

            <style>

            </style>

            <!-- /.sidebar-menu -->
            <!-- <div class="copyright-bar text-center" style="position: absolute; bottom: 0; width: 100%; padding: 8px;"> รูปติดขอบล่าง -->
            <div class="copyright-bar text-center" style="display: flex; justify-content: center; align-items: center; padding: 20px; margin-top: 50px;">
                <a href="<?php echo BASE_URL; ?>index.php" class="footer-logo">
                    <img src="<?php echo BASE_URL; ?>assets/img/pit.png"
                        alt="POINT IT INNOVATION"
                        class="footer-brand-image"
                        id="footerLogo"
                        style="height: 60px; width: auto; transition: all 0.3s ease;">
                </a>
            </div>

            <style>
                /* ขนาดปกติ */
                #footerLogo {
                    height: 60px;
                    width: auto;
                    transition: all 0.3s ease;
                    /* เพิ่ม animation การเปลี่ยนขนาด */
                }

                /* เมื่อ Navbar ถูกย่อ */
                body.sidebar-collapse #footerLogo {
                    height: 35px;
                    /* ขนาดเมื่อ Navbar ย่อ */
                }

                /* สำหรับหน้าจอขนาดเล็ก */
                @media (max-width: 768px) {
                    #footerLogo {
                        height: 45px;
                    }

                    body.sidebar-collapse #footerLogo {
                        height: 30px;
                    }
                }

                /* สำหรับหน้าจอขนาดเล็กมาก */
                @media (max-width: 576px) {
                    #footerLogo {
                        height: 40px;
                    }

                    body.sidebar-collapse #footerLogo {
                        height: 25px;
                    }
                }

                .copyright-bar {
                    transition: all 0.3s ease;
                    /* ทำให้การเปลี่ยนแปลงทั้งหมดมี animation */
                }

                body.sidebar-collapse .copyright-bar {
                    padding: 5px;
                    /* ลด padding เมื่อ Navbar ย่อ */
                }
            </style>
        </nav>
    </div>
    <!-- /.sidebar -->
</aside>


<!-- LOGO POINT ด้านล่าง ตรวจจับการขยายจอ-->
<script>
    // ตรวจจับการย่อ/ขยาย Navbar
    document.addEventListener('DOMContentLoaded', function() {
        // ตรวจสอบสถานะเริ่มต้น
        if (document.body.classList.contains('sidebar-collapse')) {
            document.getElementById('footerLogo').style.height = '35px';
        }

        // ติดตามการเปลี่ยนแปลง class ของ body
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    const isCollapsed = document.body.classList.contains('sidebar-collapse');
                    const logo = document.getElementById('footerLogo');
                    if (isCollapsed) {
                        logo.style.height = '35px';
                    } else {
                        logo.style.height = '60px';
                    }
                }
            });
        });

        observer.observe(document.body, {
            attributes: true
        });
    });
</script>

<!-- /.Preloader -->
<style>
    .preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #f4f6f9;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .preloader-content {
        text-align: center;
    }

    .preloader-logo {
        width: 80px;
        height: 80px;
        animation: pulse 2s infinite;
    }

    .preloader-spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #3498db;
        border-top: 5px solid #f4f6f9;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 20px auto;
    }

    .preloader-text {
        color: #3498db;
        font-size: 18px;
        font-weight: bold;
        margin-top: 10px;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<script>
    window.addEventListener('load', function() {
        const preloader = document.querySelector('.preloader');
        preloader.style.opacity = '0';
        setTimeout(function() {
            preloader.style.display = 'none';
        }, 500);
    });
</script>
<!-- /.Preloader -->