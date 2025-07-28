<?php
include "db_conn.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['recipe_id'])) {
    $recipe_id = $_GET['recipe_id'];
    $user_id = $_SESSION['user_id'];

    $check_sql = "SELECT * FROM saved_recipes WHERE user_id = $user_id AND recipe_id = $recipe_id";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) == 0) {
        $sql = "INSERT INTO saved_recipes (user_id, recipe_id) VALUES ($user_id, $recipe_id)";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            header("Location: recipe-details.php?id=$recipe_id&msg=Recipe saved successfully");
        } else {
            header("Location: recipe-details.php?id=$recipe_id&msg=Failed to save recipe");
        }
    } else {
        header("Location: recipe-details.php?id=$recipe_id&msg=Recipe already saved");
    }
} else {
    header("Location: dashboard.php");
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
    <title>Recipe Finder System - Save Recipe</title>
</head>
<body>
</body>
</html>
