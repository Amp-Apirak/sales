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
    // echo "เชื่อมต่อฐานข้อมูลเรียบร้อยแล้ว";
} catch (PDOException $e) {
    // ถ้ามีข้อผิดพลาด จะโยนข้อความแสดงข้อผิดพลาดออกมา
    echo "การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage();
    exit;
}
?>
