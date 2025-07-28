<?php
include "db_conn.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please log in to remove saved recipes.");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php?msg=No recipe ID provided.");
    exit;
}

$user_id = $_SESSION['user_id'];
$recipe_id = $_GET['id'];

// Check if the recipe is saved by the user
$sql = "SELECT * FROM saved_recipes WHERE user_id = ? AND recipe_id = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    header("Location: dashboard.php?msg=Database error: " . mysqli_error($conn));
    exit;
}
mysqli_stmt_bind_param($stmt, "ii", $user_id, $recipe_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    mysqli_stmt_close($stmt);
    header("Location: dashboard.php?msg=Recipe not found in your saved list.");
    exit;
}

// Delete the saved recipe
$sql = "DELETE FROM saved_recipes WHERE user_id = ? AND recipe_id = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    header("Location: dashboard.php?msg=Database error: " . mysqli_error($conn));
    exit;
}
mysqli_stmt_bind_param($stmt, "ii", $user_id, $recipe_id);
if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: dashboard.php?msg=Recipe removed successfully.");
} else {
    mysqli_stmt_close($stmt);
    header("Location: dashboard.php?msg=Failed to remove recipe.");
}
exit;
?>