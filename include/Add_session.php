<?php
// เริ่มต้น session
session_start();

// Set UTF-8 encoding for all pages
header('Content-Type: text/html; charset=utf-8');

// เชื่อมต่อฐานข้อมูล
require_once __DIR__ . '/../config/condb.php';

// ตรวจสอบการตั้งค่า Session เพื่อป้องกันกรณีที่ไม่ได้ล็อกอิน
if (!isset($_SESSION['role']) || !isset($_SESSION['team_id']) || !isset($_SESSION['user_id'])) {
    // กรณีไม่มีการตั้งค่า Session หรือล็อกอิน
    header("Location: " . BASE_URL . "login.php");  // Redirect ไปยังหน้า login.php
    exit; // หยุดการทำงานของสคริปต์ปัจจุบันหลังจาก redirect
}

?>