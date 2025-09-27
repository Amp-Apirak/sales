<?php
/**
 * ไฟล์ Helper สำหรับ Input Validation และ Security Functions
 * ใช้สำหรับตรวจสอบและทำความสะอาดข้อมูลจากผู้ใช้
 */

// ป้องกันการเข้าถึงไฟล์โดยตรง
if (!defined('BASE_URL')) {
    die('Direct access not allowed');
}

/**
 * ฟังก์ชันทำความสะอาดข้อมูล input ทั่วไป
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }

    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

/**
 * ฟังก์ชันป้องกัน XSS และทำความสะอาดข้อมูลสำหรับการแสดงผล
 */
function escapeOutput($data) {
    if (is_array($data)) {
        return array_map('escapeOutput', $data);
    }

    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * ฟังก์ชันตรวจสอบอีเมล
 */
function validateEmail($email) {
    $email = sanitizeInput($email);

    // ตรวจสอบรูปแบบอีเมล
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'message' => 'รูปแบบอีเมลไม่ถูกต้อง'];
    }

    // ตรวจสอบความยาว
    if (strlen($email) > 255) {
        return ['valid' => false, 'message' => 'อีเมลยาวเกินไป (สูงสุด 255 ตัวอักษร)'];
    }

    return ['valid' => true, 'value' => $email];
}

/**
 * ฟังก์ชันตรวจสอบเบอร์โทรศัพท์
 */
function validatePhone($phone) {
    $phone = sanitizeInput($phone);
    $phone = preg_replace('/[^0-9]/', '', $phone); // เก็บเฉพาะตัวเลข

    // ตรวจสอบความยาว (9-15 หลัก)
    if (strlen($phone) < 9 || strlen($phone) > 15) {
        return ['valid' => false, 'message' => 'เบอร์โทรศัพท์ต้องมี 9-15 หลัก'];
    }

    return ['valid' => true, 'value' => $phone];
}

/**
 * ฟังก์ชันตรวจสอบรหัสผ่าน
 */
function validatePassword($password) {
    // ตรวจสอบความยาว
    if (strlen($password) < 6) {
        return ['valid' => false, 'message' => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร'];
    }

    if (strlen($password) > 255) {
        return ['valid' => false, 'message' => 'รหัสผ่านยาวเกินไป (สูงสุด 255 ตัวอักษร)'];
    }

    return ['valid' => true, 'value' => $password];
}

/**
 * ฟังก์ชันตรวจสอบข้อความทั่วไป
 */
function validateText($text, $minLength = 1, $maxLength = 255, $fieldName = 'ข้อมูล') {
    $text = sanitizeInput($text);

    // ตรวจสอบความยาวขั้นต่ำ
    if (strlen($text) < $minLength) {
        return ['valid' => false, 'message' => $fieldName . 'ต้องมีอย่างน้อย ' . $minLength . ' ตัวอักษร'];
    }

    // ตรวจสอบความยาวสูงสุด
    if (strlen($text) > $maxLength) {
        return ['valid' => false, 'message' => $fieldName . 'ยาวเกินไป (สูงสุด ' . $maxLength . ' ตัวอักษร)'];
    }

    return ['valid' => true, 'value' => $text];
}

/**
 * ฟังก์ชันตรวจสอบตัวเลข
 */
function validateNumber($number, $min = null, $max = null, $fieldName = 'ตัวเลข') {
    $number = sanitizeInput($number);

    // ตรวจสอบว่าเป็นตัวเลข
    if (!is_numeric($number)) {
        return ['valid' => false, 'message' => $fieldName . 'ต้องเป็นตัวเลขเท่านั้น'];
    }

    $number = floatval($number);

    // ตรวจสอบค่าขั้นต่ำ
    if ($min !== null && $number < $min) {
        return ['valid' => false, 'message' => $fieldName . 'ต้องมีค่าอย่างน้อย ' . $min];
    }

    // ตรวจสอบค่าสูงสุด
    if ($max !== null && $number > $max) {
        return ['valid' => false, 'message' => $fieldName . 'ต้องมีค่าไม่เกิน ' . $max];
    }

    return ['valid' => true, 'value' => $number];
}

/**
 * ฟังก์ชันตรวจสอบ Username
 */
function validateUsername($username) {
    $username = sanitizeInput($username);

    // ตรวจสอบความยาว
    if (strlen($username) < 3) {
        return ['valid' => false, 'message' => 'Username ต้องมีอย่างน้อย 3 ตัวอักษร'];
    }

    if (strlen($username) > 50) {
        return ['valid' => false, 'message' => 'Username ยาวเกินไป (สูงสุด 50 ตัวอักษร)'];
    }

    // ตรวจสอบรูปแบบ (อนุญาตเฉพาะ a-z, A-Z, 0-9, _, -)
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
        return ['valid' => false, 'message' => 'Username ประกอบด้วย a-z, A-Z, 0-9, _, - เท่านั้น'];
    }

    return ['valid' => true, 'value' => $username];
}

/**
 * ฟังก์ชันตรวจสอบไฟล์อัพโหลด
 */
function validateUploadedFile($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'], $maxSize = 5242880) { // 5MB
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['valid' => false, 'message' => 'กรุณาเลือกไฟล์'];
    }

    // ตรวจสอบข้อผิดพลาดในการอัพโหลด
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['valid' => false, 'message' => 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์'];
    }

    // ตรวจสอบขนาดไฟล์
    if ($file['size'] > $maxSize) {
        $maxSizeMB = $maxSize / 1024 / 1024;
        return ['valid' => false, 'message' => 'ขนาดไฟล์เกิน ' . $maxSizeMB . ' MB'];
    }

    // ตรวจสอบนามสกุลไฟล์
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedTypes)) {
        return ['valid' => false, 'message' => 'ไฟล์ต้องเป็นประเภท: ' . implode(', ', $allowedTypes)];
    }

    // ตรวจสอบ MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMimes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'pdf' => 'application/pdf'
    ];

    if (isset($allowedMimes[$extension]) && $mimeType !== $allowedMimes[$extension]) {
        return ['valid' => false, 'message' => 'ไฟล์ไม่ตรงกับประเภทที่ระบุ'];
    }

    return ['valid' => true, 'file' => $file];
}

/**
 * ฟังก์ชันสำหรับ Rate Limiting
 */
function checkRateLimit($identifier, $maxAttempts = 5, $timeWindow = 600) {
    if (!isset($_SESSION['rate_limit'])) {
        $_SESSION['rate_limit'] = [];
    }

    $now = time();
    $key = 'rate_' . $identifier;

    if (!isset($_SESSION['rate_limit'][$key]) || !is_array($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = [];
    }

    $entry =& $_SESSION['rate_limit'][$key];
    $entry['attempts'] = $entry['attempts'] ?? [];
    $entry['block_level'] = $entry['block_level'] ?? 0;
    $entry['block_expires'] = $entry['block_expires'] ?? 0;

    if (!empty($entry['block_expires']) && $entry['block_expires'] > $now) {
        $retryAfter = $entry['block_expires'] - $now;
        $minutes = ceil($retryAfter / 60);
        $message = 'พยายามเกินจำนวนที่กำหนด กรุณาลองใหม่อีกครั้งใน ' . $minutes . ' นาที';
        return [
            'allowed' => false,
            'message' => $message,
            'retry_after' => $retryAfter,
            'details' => sprintf(
                'อยู่ในช่วงรอปลดล็อก (ระดับการบล็อก %d) เหลือ %d วินาที',
                $entry['block_level'],
                $retryAfter
            ),
            'block_level' => $entry['block_level']
        ];
    }

    if (!empty($entry['block_expires']) && $entry['block_expires'] <= $now) {
        $entry['block_expires'] = 0;
        $entry['attempts'] = [];
    }

    $entry['attempts'] = array_filter(
        $entry['attempts'],
        function ($timestamp) use ($now, $timeWindow) {
            return ($now - $timestamp) < $timeWindow;
        }
    );
    $attemptCount = count($entry['attempts']);

    if ($attemptCount >= $maxAttempts) {
        $entry['block_level'] = min($entry['block_level'] + 1, 6);
        $penalties = [60, 180, 300, 420, 540, 900];
        $penaltyIndex = max(0, min($entry['block_level'] - 1, count($penalties) - 1));
        $penaltyDuration = $penalties[$penaltyIndex];
        $entry['block_expires'] = $now + $penaltyDuration;
        $entry['attempts'] = [];

        $minutes = ceil($penaltyDuration / 60);
        $message = 'พยายามเกินจำนวนที่กำหนด กรุณาลองใหม่อีกครั้งใน ' . $minutes . ' นาที';

        return [
            'allowed' => false,
            'message' => $message,
            'retry_after' => $penaltyDuration,
            'details' => sprintf(
                'ระบบพบบทความแก้ไข %d ครั้งภายใน %d นาที (จำกัด %d ครั้ง)',
                $attemptCount,
                ceil($timeWindow / 60),
                $maxAttempts
            ),
            'block_level' => $entry['block_level']
        ];
    }

    $entry['attempts'][] = $now;

    return [
        'allowed' => true,
        'retry_after' => 0,
        'attempts' => $attemptCount + 1,
        'block_level' => $entry['block_level']
    ];
}

/**
 * ฟังก์ชันสำหรับ CSRF Protection
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * ฟังก์ชันทำความสะอาดชื่อไฟล์
 */
function sanitizeFilename($filename) {
    // เก็บเฉพาะตัวอักษร ตัวเลข จุด ขีดกลาง และขีดล่าง
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);

    // ป้องกันชื่อไฟล์เริ่มต้นด้วยจุด
    $filename = ltrim($filename, '.');

    // จำกัดความยาว
    if (strlen($filename) > 100) {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $filename = substr($name, 0, 100 - strlen($extension) - 1) . '.' . $extension;
    }

    return $filename;
}
