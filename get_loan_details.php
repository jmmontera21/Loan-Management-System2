<?php
include 'db_connect.php';

if (isset($_GET['apply_loan_id'])) {
    $apply_loan_id = $_GET['apply_loan_id'];

    $query = "SELECT * FROM apply_loan WHERE apply_loan_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $apply_loan_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $loan_data = $result->fetch_assoc();
        echo json_encode($loan_data);
    } else {
        echo json_encode(["error" => "Loan not found."]);
    }
} else {
    echo json_encode(["error" => "No loan ID provided."]);
}
?>
