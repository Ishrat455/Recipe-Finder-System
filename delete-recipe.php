<?php
include "db_conn.php";

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php?msg=Admins only");
    exit;
}

if (isset($_GET['id'])) {
    $recipe_id = $_GET['id'];

    mysqli_begin_transaction($conn);
    try {
        // Delete from saved_recipes
        $sql = "DELETE FROM saved_recipes WHERE recipe_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $recipe_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete from recipe_steps
        $sql = "DELETE FROM recipe_steps WHERE recipe_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $recipe_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete from recipe_ingredients (optional, since ON DELETE CASCADE exists)
        $sql = "DELETE FROM recipe_ingredients WHERE recipe_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $recipe_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Delete from recipes
        $sql = "DELETE FROM recipes WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $recipe_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Reset AUTO_INCREMENT
        $sql = "SELECT MAX(id) + 1 AS next_id FROM recipes";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $next_id = $row['next_id'] ?: 1;
        mysqli_query($conn, "ALTER TABLE recipes AUTO_INCREMENT = $next_id");

        mysqli_commit($conn);
        header("Location: admin.php?msg=Recipe deleted successfully");
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header("Location: admin.php?msg=" . urlencode("Error deleting recipe: " . $e->getMessage()));
    }
    exit;
} else {
    header("Location: admin.php");
    exit;
}
?>