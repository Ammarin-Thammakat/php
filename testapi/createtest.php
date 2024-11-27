<?php
require 'db.php'; // นำเข้าไฟล์ฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $amount = $data['amount']; // จำนวนเงินที่ต้องการแลก
    $issue_date = $data['issue_date'] ?? date('Y-m-d');
    $expiry_date = $data['expiry_date'] . ' 23:59:59'; // เพิ่มเวลา 23:59:59 ให้กับวันหมดอายุ
    $status = 'unused';

    // มูลค่าของคูปองที่มี (เรียงจากมากไปน้อย)
    $coupon_values = [30, 20, 10, 5, 1];
    $coupons_to_create = []; // เก็บข้อมูลจำนวนคูปองที่จะแจกแจง

    // คำนวณจำนวนคูปองที่ต้องการสร้าง
    foreach ($coupon_values as $value) {
        $count = intdiv($amount, $value); // หาจำนวนคูปองชนิดนี้ที่ต้องการ
        if ($count > 0) {
            $coupons_to_create[$value] = $count;
        }
        $amount %= $value; // อัปเดตจำนวนเงินที่เหลือ
    }

    $sql = "INSERT INTO Coupons (value, issue_date, expiry_date, status, qr_code, transaction_type) VALUES (?, ?, ?, ?, ?, 'create')";
    $stmt = $pdo->prepare($sql);

    try {
        foreach ($coupons_to_create as $value => $quantity) {
            for ($i = 0; $i < $quantity; $i++) {
                $qr_code = uniqid('coupon_', true); // สร้าง QR Code แบบสุ่ม
                $stmt->execute([$value, $issue_date, $expiry_date, $status, $qr_code]);
            }
        }

        echo json_encode(['message' => 'Coupons created successfully!', 'details' => $coupons_to_create]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error creating coupons: ' . $e->getMessage()]);
    }
}
?>

