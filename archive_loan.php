<?php
include 'db_connect.php';

if (isset($_GET['apply_loan_id'])) {
    $apply_loan_id = $_GET['apply_loan_id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert the record into the archive table
        $archiveQuery = "INSERT INTO apply_loan_archive SELECT * FROM apply_loan WHERE apply_loan_id = ?";
        $archiveStmt = $conn->prepare($archiveQuery);
        $archiveStmt->bind_param("i", $apply_loan_id);
        $archiveStmt->execute();

        // Delete the record from the main table
        $deleteQuery = "DELETE FROM apply_loan WHERE apply_loan_id = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $apply_loan_id);
        $deleteStmt->execute();

        // Commit the transaction
        $conn->commit();
        echo "Loan archived successfully.";
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        echo "Failed to archive loan: " . $e->getMessage();
    }

    $archiveStmt->close();
    $deleteStmt->close();
    $conn->close();
} else {
    echo "No loan ID specified.";
}
?>
