<?php
// เริ่มต้น session
session_start();

// ===== ป้องกัน Browser Cache หลัง Logout =====
// บังคับให้ browser ไม่ cache หน้าเว็บ (ป้องกันกด Back แล้วเห็นข้อมูลเดิม)
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');

// เชื่อมต่อฐานข้อมูล
require_once __DIR__ . '/../config/condb.php';

// ตรวจสอบการตั้งค่า Session เพื่อป้องกันกรณีที่ไม่ได้ล็อกอิน
if (!isset($_SESSION['role']) || !isset($_SESSION['team_id']) || !isset($_SESSION['user_id'])) {
    // กรณีไม่มีการตั้งค่า Session หรือล็อกอิน
    header("Location: " . BASE_URL . "login.php");  // Redirect ไปยังหน้า login.php
    exit; // หยุดการทำงานของสคริปต์ปัจจุบันหลังจาก redirect
}

// ===== Session Security =====
// ตรวจสอบว่า session ยังมีชีวิตอยู่ (ป้องกัน session hijacking)
if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
} elseif (time() - $_SESSION['last_activity'] > 3600) {
    // Session timeout หลัง 1 ชั่วโมงไม่มี activity
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL . "login.php?timeout=1");
    exit;
}
$_SESSION['last_activity'] = time();

// Regenerate session ID ทุก 30 นาที เพื่อป้องกัน session fixation
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

?>