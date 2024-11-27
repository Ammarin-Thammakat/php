<?php
require 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    // คำนวณยอดรวมคูปองที่สร้าง
    $sql_created = "SELECT value, COUNT(*) AS quantity FROM Coupons WHERE transaction_type = 'create' GROUP BY value";
    $stmt_created = $pdo->prepare($sql_created);
    $stmt_created->execute();
    $created_coupons = $stmt_created->fetchAll(PDO::FETCH_ASSOC);

    $total_created = 0;
    foreach ($created_coupons as $coupon) {
        $total_created += $coupon['value'] * $coupon['quantity'];
    }

    // คำนวณยอดรวมคูปองที่ใช้แล้ว
    $sql_used = "SELECT value, COUNT(*) AS quantity FROM coupon_logs WHERE transaction_type = 'use' GROUP BY value";
    $stmt_used = $pdo->prepare($sql_used);
    $stmt_used->execute();
    $used_coupons = $stmt_used->fetchAll(PDO::FETCH_ASSOC);

    $total_used = 0;
    foreach ($used_coupons as $coupon) {
        $total_used += $coupon['value'] * $coupon['quantity'];
    }

    // คำนวณยอดรวมคูปองที่คืนแล้ว
    $sql_returned = "SELECT value, COUNT(*) AS quantity FROM transactions WHERE transaction_type = 'return' GROUP BY value";
    $stmt_returned = $pdo->prepare($sql_returned);
    $stmt_returned->execute();
    $returned_coupons = $stmt_returned->fetchAll(PDO::FETCH_ASSOC);

    $total_returned = 0;
    foreach ($returned_coupons as $coupon) {
        $total_returned += $coupon['value'] * $coupon['quantity'];
    }

    // ตรวจสอบผลลัพธ์
    $target_amount = $total_used - $total_returned; // จำนวนเงินที่ได้รับ
    $created_match = $total_created == $target_amount ? 'ตรงกับยอดเงินที่ได้รับ!' : 'ไม่ตรงกับยอดเงินที่ได้รับ!';

    // ส่งผลลัพธ์กลับในรูปแบบ JSON
    echo json_encode([
        'created_total' => $total_created,
        'used_total' => $total_used,
        'returned_total' => $total_returned,
        'created_match' => $created_match
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
