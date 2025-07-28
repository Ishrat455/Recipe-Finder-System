<?php
include "db_conn.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $recipe_id = $_GET['id'];

    // Fetch recipe details
    $sql = "SELECT * FROM recipes WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $recipe_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $recipe = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$recipe) {
        header("Location: dashboard.php?msg=Recipe not found");
        exit;
    }

    // Fetch ingredients
    $sql = "SELECT i.name, ri.amount, ri.unit 
            FROM recipe_ingredients ri 
            JOIN ingredients i ON ri.ingredient_id = i.id 
            WHERE ri.recipe_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $recipe_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $ingredients = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
} else {
    header("Location: dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$is_saved = false;
$check_sql = "SELECT * FROM saved_recipes WHERE user_id = ? AND recipe_id = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $recipe_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);
if (mysqli_num_rows($check_result) > 0) {
    $is_saved = true;
}
mysqli_stmt_close($check_stmt);
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
    <title>Recipe Finder System - Recipe Details</title>
</head>
<body>
    <?php include "navbar.php"; ?>
    <div class="container">
        <div class="text-center mb-4">
            <h3><?php echo htmlspecialchars($recipe['title']); ?></h3>
            <p class="text-muted">Explore the details of this delicious recipe</p>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                <p class="card-text"><strong>Cuisine:</strong> <?php echo htmlspecialchars($recipe['cuisine']); ?></p>
                <p class="card-text"><strong>Prep Time:</strong> <?php echo htmlspecialchars($recipe['prep_time']); ?> minutes</p>
                <p class="card-text"><strong>Ingredients:</strong><br>
                    <?php if (!empty($ingredients)) { ?>
                        <?php foreach ($ingredients as $ing) { ?>
                            <?php echo htmlspecialchars($ing['amount'] . ' ' . $ing['unit'] . ' ' . $ing['name']) . '<br>'; ?>
                        <?php } ?>
                    <?php } else { ?>
                        No ingredients listed.
                    <?php } ?>
                </p>
                <p class="card-text"><strong>Instructions:</strong><br><?php echo nl2br(htmlspecialchars($recipe['instructions'])); ?></p>
                <a href="save-recipe.php?recipe_id=<?php echo $recipe['id']; ?>" class="btn btn-<?php echo $is_saved ? 'secondary' : 'success'; ?>">
                    <?php echo $is_saved ? 'Saved' : 'Save Recipe'; ?>
                </a>
                <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>
