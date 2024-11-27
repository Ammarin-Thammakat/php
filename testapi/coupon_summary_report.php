<?php
require 'db.php';

header('Content-Type: application/json');

try {
    // กำหนดค่าคูปองที่ต้องการตรวจสอบ
    $coupon_values = [30, 20, 10, 5, 1];
    $report = [];

    foreach ($coupon_values as $value) {
        // จำนวนคูปองทั้งหมด
        $sql_total = "SELECT COUNT(*) AS total FROM coupons WHERE value = ?";
        $stmt_total = $pdo->prepare($sql_total);
        $stmt_total->execute([$value]);
        $total = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];

        // จำนวนคูปองที่ใช้งานแล้ว
        $sql_used = "SELECT COUNT(*) AS used FROM coupon_logs WHERE value = ? AND transaction_type = 'use'";
        $stmt_used = $pdo->prepare($sql_used);
        $stmt_used->execute([$value]);
        $used = $stmt_used->fetch(PDO::FETCH_ASSOC)['used'];

        // จำนวนคูปองที่ยังไม่ได้ใช้งาน
        $unused = $total - $used;

        // เก็บข้อมูลในรายงาน
        $report[] = [
            'value' => $value,
            'total' => $total,
            'used' => $used,
            'unused' => $unused
        ];
    }

    echo json_encode([
        'status' => 'success',
        'report' => $report
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
