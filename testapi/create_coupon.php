<?php
require 'db.php'; // นำเข้าไฟล์ฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $value = $data['value'];
    $issue_date = $data['issue_date'] ?? date('Y-m-d');
    $expiry_date = $data['expiry_date'] . ' 23:59:59';  // เพิ่มเวลา 23:59:59 ให้กับวันหมดอายุ
    $quantity = $data['quantity'] ?? 1; // จำนวนคูปองที่ต้องการสร้าง
    $status = 'unused';

    $sql = "INSERT INTO Coupons (value, issue_date, expiry_date, status, qr_code,transaction_type) VALUES (?, ?, ?, ?, ?,'create')";
    $stmt = $pdo->prepare($sql);

    try {
        for ($i = 0; $i < $quantity; $i++) {
            $qr_code = uniqid('coupon_', true); // สร้าง QR Code แบบสุ่ม
            $stmt->execute([$value, $issue_date, $expiry_date, $status, $qr_code]);
        }

        echo json_encode(['message' => "$quantity coupons created successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error creating coupons: ' . $e->getMessage()]);
    }
}
?>


