<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ดึงข้อมูล JSON จากคำขอ
    $data = json_decode(file_get_contents('php://input'), true);

    // รับค่าจาก JSON
    $username = $data['username'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT); // เข้ารหัสรหัสผ่าน
    $email = $data['email'];
    $created_at = date('Y-m-d H:i:s');

    // เพิ่มข้อมูลลงฐานข้อมูล
    $sql = "INSERT INTO admins (username, password, email, created_at) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $password, $email, $created_at]);

    echo json_encode([
        "message" => "Admin created successfully!",
        "admin_id" => $pdo->lastInsertId()
    ]);
    echo "Admin created successfully";
}
?>


