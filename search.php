<?php
include "db_conn.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$search_results = [];
if (isset($_POST['search'])) {
    $ingredients = mysqli_real_escape_string($conn, $_POST['ingredients']);
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO search_history (user_id, ingredients, search_time) 
            VALUES (?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $user_id, $ingredients);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "SELECT DISTINCT r.* 
            FROM recipes r 
            JOIN recipe_ingredients ri ON r.id = ri.recipe_id 
            JOIN ingredients i ON ri.ingredient_id = i.id 
            WHERE i.name LIKE ?";
    $stmt = mysqli_prepare($conn, $sql);
    $search_term = "%$ingredients%";
    mysqli_stmt_bind_param($stmt, "s", $search_term);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $search_results = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
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
    <title>Recipe Finder System - Search Recipes</title>
</head>
<body>
    <?php include "navbar.php"; ?>
    <div class="container">
        <div class="text-center mb-4">
            <h3>Search Recipes</h3>
            <p class="text-muted">Find recipes by ingredients</p>
        </div>
        <div class="container d-flex justify-content-center">
            <form action="" method="post" style="width:50vw; min-width:300px;">
                <div class="mb-3">
                    <label class="form-label">Ingredients:</label>
                    <input type="text" class="form-control" name="ingredients" placeholder="e.g., chicken, tomato" required>
                </div>
                <div>
                    <button type="submit" class="btn btn-success" name="search" data-bs-toggle="tooltip" data-bs-placement="top" title="Search">Search</button>
                    <a href="dashboard.php" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Back to Dashboard">Back to Dashboard</a>
                </div>
            </form>
        </div>
        <?php if (!empty($search_results)) { ?>
            <h4 class="mt-4">Search Results</h4>
            <table class="table table-hover text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Cuisine</th>
                        <th>Prep Time</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($search_results as $recipe) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($recipe['title']); ?></td>
                            <td><?php echo htmlspecialchars($recipe['cuisine']); ?></td>
                            <td><?php echo htmlspecialchars($recipe['prep_time']); ?> min</td>
                            <td>
                                <a href="recipe-details.php?id=<?php echo $recipe['id']; ?>" class="link-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="View Recipe"><i class="fa-solid fa-eye fs-5"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script>
        // Initialize Bootstrap tooltips
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>
