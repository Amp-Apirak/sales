<?php

// โหลด environment variables
require_once __DIR__ . '/env_loader.php';

//path
define('BASE_URL', getEnvVar('BASE_URL', '/sales/'));

// ข้อมูลการเชื่อมต่อฐานข้อมูล
$host = getEnvVar('DB_HOST', 'localhost');
$dbname = getEnvVar('DB_NAME', 'sales_db');
$username = getEnvVar('DB_USERNAME', 'root');
$password = getEnvVar('DB_PASSWORD', '1234');

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

// ฟังก์ชันสำหรับเข้ารหัส ID
function encryptUserId($user_id) {
    $secret_key = getEnvVar('SECRET_KEY', 'your_secret_key');
    $iv = getEnvVar('ENCRYPTION_IV', '1234567890123456');
    return base64_encode(openssl_encrypt($user_id, "aes-256-cbc", $secret_key, 0, $iv));
}

// ฟังก์ชันสำหรับถอดรหัส ID
function decryptUserId($encrypted_user_id) {
    $secret_key = getEnvVar('SECRET_KEY', 'your_secret_key');
    $iv = getEnvVar('ENCRYPTION_IV', '1234567890123456');
    return openssl_decrypt(base64_decode($encrypted_user_id), "aes-256-cbc", $secret_key, 0, $iv);
}
