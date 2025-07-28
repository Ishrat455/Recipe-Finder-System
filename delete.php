<?php
include "db_conn.php";

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php?msg=Access denied. Admins only.");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Start a transaction to ensure data consistency
    mysqli_begin_transaction($conn);

    try {
        // Delete from password_resets
        $stmt = mysqli_prepare($conn, "DELETE FROM password_resets WHERE email = (SELECT email FROM users WHERE id = ?)");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete from recipes
        $stmt = mysqli_prepare($conn, "DELETE FROM recipes WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete from saved_recipes
        $stmt = mysqli_prepare($conn, "DELETE FROM saved_recipes WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete from search_history
        $stmt = mysqli_prepare($conn, "DELETE FROM search_history WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete the user
        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Commit the transaction
        mysqli_commit($conn);
        header("Location: admin.php?msg=User deleted successfully");
    } catch (mysqli_sql_exception $e) {
        // Roll back the transaction on error
        mysqli_rollback($conn);
        header("Location: admin.php?msg=Failed to delete user: " . mysqli_error($conn));
    }
} else {
    header("Location: admin.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <title>Recipe Finder System - Delete User</title>
</head>
<body>
</body>
</html>
