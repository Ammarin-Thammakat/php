<?php
$host = 'localhost';  // ชื่อเซิร์ฟเวอร์
$db = 'coupon_system';  // ชื่อฐานข้อมูล
$user = 'root';  // ชื่อผู้ใช้ MySQL
$pass = '';  // รหัสผ่าน (สำหรับ XAMPP ส่วนใหญ่เว้นว่าง)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    echo "Database connection successful!";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>
