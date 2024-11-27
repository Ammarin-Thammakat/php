<?php
require 'db.php'; // ปรับ path การเชื่อมต่อฐานข้อมูลให้ถูกต้อง

header('Content-Type: application/json');

try {
    $sql = "SELECT status, COUNT(*) AS total_coupons, SUM(value) AS total_value 
            FROM Coupons 
            GROUP BY status";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'data' => $result
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
