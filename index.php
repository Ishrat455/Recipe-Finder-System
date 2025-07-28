<?php
include "db_conn.php";
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
    <title>Recipe Finder System - Home</title>
</head>
<body>
    <?php include "navbar.php"; ?>
    <div class="container">
        <div class="text-center mb-4">
            <h3>Welcome to Recipe Finder System</h3>
            <p class="text-muted">Discover and share delicious recipes with ease</p>
        </div>
        <div class="text-center">
            <p>Join our community to save your favorite recipes, search for new ones, and contribute your own!</p>
            <?php if (!isset($_SESSION['user_id'])) { ?>
                <a href="login.php" class="btn btn-success me-2">Login</a>
                <a href="add-new.php" class="btn btn-primary">Sign Up</a>
            <?php } else { ?>
                <a href="search.php" class="btn btn-success me-2">Search Recipes</a>
                <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
            <?php } ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>
