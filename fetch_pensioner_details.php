<?php
include 'db_connect.php';

// Get selected pensioner ID
$pensioner_id = isset($_POST['apply_loan_id']) ? intval($_POST['apply_loan_id']) : 0;

// Fetch loan details for the pensioner
$sql = "SELECT * FROM apply_loan WHERE apply_loan_id = ? AND status = 'Released'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pensioner_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $loan = $result->fetch_assoc();
    echo json_encode($loan);
} else {
    echo json_encode(['error' => 'Loan not released']);
}

$conn->close();
?>
