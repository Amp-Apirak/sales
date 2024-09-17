<?php

//path
define('BASE_URL', '/sales/');

// ข้อมูลการเชื่อมต่อฐานข้อมูล
$host = 'localhost';
$dbname = 'sales_db';
$username = 'root';
$password = '1234';

try {
    // สร้างการเชื่อมต่อด้วย PDO
    $condb = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // ตั้งค่า PDO ให้โยน Exception ในกรณีที่เกิดข้อผิดพลาด
    $condb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $condb->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // ถ้ามีข้อผิดพลาด จะโยนข้อความแสดงข้อผิดพลาดออกมา
    echo "การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage();
    exit;
}

// ฟังก์ชันสำหรับเข้ารหัส user_id
function encryptUserId($user_id) {
    $secret_key = "your_secret_key";  // เปลี่ยนคีย์นี้ตามที่คุณต้องการ
    return base64_encode(openssl_encrypt($user_id, "aes-256-cbc", $secret_key, 0, "1234567890123456"));
}

// ฟังก์ชันสำหรับถอดรหัส user_id
function decryptUserId($encrypted_user_id) {
    $secret_key = "your_secret_key";  // คีย์เดียวกับที่ใช้เข้ารหัส
    return openssl_decrypt(base64_decode($encrypted_user_id), "aes-256-cbc", $secret_key, 0, "1234567890123456");
}
