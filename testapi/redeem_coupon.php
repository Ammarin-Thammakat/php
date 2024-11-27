<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = json_decode(file_get_contents('php://input'), true);

    $coupon_id =  $data['coupon_id'];
    $store_id =   $data['store_id'];

    // ตรวจสอบว่าคูปองใช้ได้
    $checkSql = "SELECT * FROM coupons WHERE coupon_id = ? AND status = 'unused' AND expiry_date >= CURDATE()";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$coupon_id]);
    $coupon = $checkStmt->fetch();

    if ($coupon) {
        // เพิ่มใน Transactions
        $redeemSql = "INSERT INTO transactions (coupon_id, store_id, used_at, status) VALUES (?, ?, NOW(), 'completed')";
        $redeemStmt = $pdo->prepare($redeemSql);
        $redeemStmt->execute([$coupon_id, $store_id]);

        // อัปเดตสถานะใน Coupons
        $updateSql = "UPDATE coupons SET status = 'used', updated_at = NOW() WHERE coupon_id = ?";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([$coupon_id]);

        echo json_encode([
            "message" => "Coupon redeem successfully.!"
        ]);
    } else {
        echo json_encode([
            "message" => "Coupon is invalid or already used.!"
        ]);
    }
}
?>
