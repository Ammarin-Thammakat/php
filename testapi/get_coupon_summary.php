<?php
require 'db.php'; // นำเข้าไฟล์ฐานข้อมูล

// ตรวจสอบว่าเป็นคำขอ GET
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // คำสั่ง SQL สำหรับดึงข้อมูลสรุปจำนวนคูปองตามค่า
    $sql = "SELECT value, COUNT(*) AS quantity, SUM(value) AS total_value 
            FROM Coupons WHERE status = 'unused' GROUP BY value";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $summary = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ส่งข้อมูลเป็น JSON
        echo json_encode($summary);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error fetching coupon summary: ' . $e->getMessage()]);
    }
}
?>
