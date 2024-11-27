<?php
require 'db.php'; // เรียกใช้การเชื่อมต่อฐานข้อมูล

$sql = "SELECT * FROM Coupons"; // คำสั่ง SQL ดึงข้อมูลทั้งหมด
$stmt = $pdo->prepare($sql);
$stmt->execute();
$coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($coupons);
?>
