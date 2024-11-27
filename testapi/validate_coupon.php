<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = json_decode(file_get_contents('php://input'), true);
    
    $coupon_id = $data['coupon_id'];

    $sql = "SELECT * FROM coupons WHERE coupon_id = ? AND expiry_date >= CURDATE() AND status = 'unused'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$coupon_id]);
    $coupon = $stmt->fetch();

    if ($coupon) {
        echo json_encode(['message' => 'Valid coupon', 'coupon' => $coupon]);
    } else {
        echo json_encode(['message' => 'Invalid or expired coupon']);
    }
}
?>

