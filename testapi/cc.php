<?php
require 'db.php'; // นำเข้าไฟล์ฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $value = $data['value'];
    $issue_date = $data['issue_date'] ?? date('Y-m-d');
    $expiry_date = $data['expiry_date'];
    $quantity = $data['quantity'] ?? 1; // จำนวนคูปองที่ต้องการสร้าง
    $status = 'unused';

    $sql = "INSERT INTO Coupons1 (value, issue_date, expiry_date, status, qr_code) VALUES (?, ?, ?, ?, ?)";
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
<!-- <?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $value = $data['value'];
    $issue_date = $data['issue_date'];
    $expiry_date = $data['expiry_date'];
    $status = 'unused';
    $qr_code = uniqid('coupon_', true);

    $sql = "INSERT INTO coupons (value, issue_date, expiry_date, status, qr_code) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$value, $issue_date, $expiry_date, $status, $qr_code]);

    echo json_encode([
        'message' => 'Coupon created successfully!',
        'qr_code' => $qr_code
    ]);
}
?> -->
