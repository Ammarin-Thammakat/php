<?php
require 'db.php';  // นำเข้าไฟล์การเชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $coupon_id = $_GET['coupon_id'];

    $sql = "DELETE FROM Coupons WHERE coupon_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$coupon_id]);

    echo json_encode(["message" => "Coupon deleted successfully"]);
}
?>
