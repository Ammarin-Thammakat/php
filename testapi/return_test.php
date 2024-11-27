<?php
require 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $coupon_id = $data['coupon_id']; // coupon_id ที่จะคืน
    $store_id = $data['store_id'];   // store_id ที่ร้านค้าสแกน QR Code
    $return_date = date('Y-m-d H:i:s'); // วันที่คืนคูปอง

    // ตรวจสอบว่าคูปองมีสถานะเป็น 'unused' หรือ 'used' และยังไม่หมดอายุ
    $sql = "SELECT * FROM Coupons WHERE coupon_id = ? AND (status = 'unused' OR status = 'used') AND expiry_date >= CURDATE()";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$coupon_id]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($coupon) {
        // ถ้าคูปองยังไม่ได้ถูกใช้, เปลี่ยนสถานะเป็น 'used' และบันทึก store_id ที่คืนคูปอง
        if ($coupon['status'] == 'unused') {
            $sql_update = "UPDATE Coupons SET status = 'used', store_id = ? WHERE coupon_id = ?";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([$store_id,  $coupon_id]);

            // บันทึกการใช้คูปองในตาราง coupon_transactions
            $transaction_sql = "INSERT INTO coupon_transactions (coupon_id, store_id, transaction_type, value) 
                                VALUES (?, ?, 'use',?)";
            $stmt_transaction = $pdo->prepare($transaction_sql);
            $stmt_transaction->execute([$coupon_id, $store_id, $coupon['value']]);

            echo json_encode(["message" => "Coupon used successfully!"]);
        } elseif ($coupon['status'] == 'used') {
            // ถ้าคูปองถูกใช้แล้วและต้องการคืนคูปองให้ใช้ซ้ำได้
            $sql_reset = "UPDATE Coupons SET status = 'unused', store_id = NULL WHERE coupon_id = ? AND status = 'used'";
            $stmt_reset = $pdo->prepare($sql_reset);
            $stmt_reset->execute([$coupon_id]);

            if ($stmt_reset->rowCount() > 0) {
                // บันทึกการคืนคูปองในตาราง coupon_transactions
                $transaction_sql = "INSERT INTO transactions (coupon_id, store_id, transaction_type, value) 
                                    VALUES (?, ?, 'return', ?)";
                $stmt_transaction = $pdo->prepare($transaction_sql);
                $stmt_transaction->execute([$coupon_id, $store_id, $coupon['value']]);

                echo json_encode(["message" => "Coupon returned successfully, now it's available to use again!"]);
            } else {
                echo json_encode(["message" => "Coupon not found or cannot be reset."]);
            }
        }
    } else {
        echo json_encode(["message" => "Coupon not found or expired."]);
    }
}
?>
