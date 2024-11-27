// update_coupon.php
<?php
require 'db.php';  // นำเข้าไฟล์การเชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $coupon_id = $_GET['coupon_id'];
    $data = json_decode(file_get_contents('php://input'), true);

    $value = $data['value'];
    $issue_date = $data['issue_date'];
    $expiry_date = $data['expiry_date'];
    $status = $data['status'];

    $sql = "UPDATE Coupons SET value = ?, issue_date = ?, expiry_date = ?, status = ? WHERE coupon_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$value, $issue_date, $expiry_date, $status, $coupon_id]);

    echo json_encode(["message" => "Coupon updated successfully"]);
}
?>
