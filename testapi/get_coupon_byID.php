<?php
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $coupon_id=$_GET['coupon_id'];

    $sql = "select * from coupons where coupon_id=?";
    $stmt = $pdo->prepare($sql);
        $stmt->execute([$coupon_id]);
    $coupons = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($coupons) {
        echo json_encode($coupons);
    } else {
        echo json_encode(["message" => "Coupon not found"]);
    }

}
?>