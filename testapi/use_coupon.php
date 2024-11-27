<?php
require 'db.php';  // การเชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจาก API
    $data = json_decode(file_get_contents('php://input'), true);
    $coupon_id = $data['coupon_id']; // coupon_id ที่จะคืน
    $store_id = $data['store_id'];   // store_id ที่ร้านค้าสแกน QR Code
    $return_at = date('Y-m-d H:i:s'); // วันที่คืนคูปอง

    // ตรวจสอบว่าคูปองมีสถานะเป็น 'unused' และยังไม่หมดอายุ
    $sql = "SELECT * FROM Coupons WHERE coupon_id = ? AND (status = 'unused' OR status = 'used') AND expiry_date >= CURDATE()";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$coupon_id]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($coupon) {
        // ถ้าคูปองยังไม่ได้ถูกใช้, เปลี่ยนสถานะเป็น 'used' และบันทึก store_id ที่คืนคูปอง
        if ($coupon['status'] == 'unused') {
            // เปลี่ยนสถานะเป็น 'used' และบันทึก store_id และ return_at
            $sql_update = "UPDATE Coupons SET status = 'used', store_id = ? WHERE coupon_id = ?";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([$store_id, $coupon_id]);

            // บันทึกข้อมูลการคืนคูปองใน coupon_logs
            $transaction_sql = "INSERT INTO coupon_logs (coupon_id, store_id, transaction_type, value)
                                VALUES (?, ?, 'use', ?)";
            $stmt_transaction = $pdo->prepare($transaction_sql);
            $stmt_transaction->execute([$coupon_id, $store_id, $coupon['value']]);

            echo json_encode(["message" => "Coupon returned successfully!"]);
        } elseif ($coupon['status'] == 'used') {
            echo json_encode(["message" => "Coupon already used."]);
        }
    } 
}
?>
