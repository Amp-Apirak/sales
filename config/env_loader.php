<?php
/**
 * Simple environment variables loader
 * อ่านไฟล์ .env และโหลดค่าต่างๆ เข้าสู่ $_ENV และ $_SERVER
 */

function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        return false;
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // ข้ามบรรทัดที่เป็น comment
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // แยก key=value
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // ลบ quotes ถ้ามี
            $value = trim($value, '"\'');

            // เก็บค่าใน $_ENV และ $_SERVER
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;

            // ตั้งค่าเป็น environment variable ของระบบ
            putenv("$key=$value");
        }
    }

    return true;
}

function getEnvVar($key, $default = null) {
    // ลองหาค่าจาก $_ENV, $_SERVER, และ getenv() ตามลำดับ
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }

    if (isset($_SERVER[$key])) {
        return $_SERVER[$key];
    }

    $value = getenv($key);
    if ($value !== false) {
        return $value;
    }

    return $default;
}

// โหลด .env file โดยอัตโนมัติ
$envFile = __DIR__ . '/../.env';
loadEnv($envFile);