<?php
require 'db.php';
header('Content-Type: application/json');

// รับข้อมูลจาก API
$data = json_decode(file_get_contents('php://input'), true);
$coupon_ids = $data['coupon_ids'] ?? [];
$return_date = date('Y-m-d H:i:s');

if (empty($coupon_ids)) {
    echo json_encode(['error' => 'No coupons provided for return.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // คำนวณยอดรวมและอัปเดตสถานะคูปอง
    $total_refund = 0;
    $update_sql = "UPDATE Coupons1 SET status = 'returned', return_date = :return_date WHERE coupon_id = :coupon_id AND status IN ('unused', 'used')";
    $stmt_update = $pdo->prepare($update_sql);

    foreach ($coupon_ids as $coupon_id) {
        // ดึงข้อมูลคูปองเพื่อตรวจสอบมูลค่า
        $select_sql = "SELECT value FROM Coupons1 WHERE coupon_id = :coupon_id AND status IN ('unused', 'used')";
        $stmt_select = $pdo->prepare($select_sql);
        $stmt_select->execute(['coupon_id' => $coupon_id]);
        $coupon = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if ($coupon) {
            $total_refund += $coupon['value'];
            // อัปเดตสถานะคูปองเป็น returned
            $stmt_update->execute([
                'coupon_id' => $coupon_id,
                'return_date' => $return_date
            ]);
        }
    }

    // บันทึกธุรกรรมการคืนเงิน
    $transaction_sql = "INSERT INTO transactions (transaction_type, amount, transaction_date) VALUES ('refund', :amount, :transaction_date)";
    $stmt_transaction = $pdo->prepare($transaction_sql);
    $stmt_transaction->execute([
        'amount' => $total_refund,
        'transaction_date' => $return_date
    ]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'refund_amount' => $total_refund,
        'message' => 'Coupons returned successfully and refund processed.'
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['error' => $e->getMessage()]);
}
?>
