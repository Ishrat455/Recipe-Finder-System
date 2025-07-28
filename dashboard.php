<?php
include "db_conn.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';

$saved_recipes = [];
$sql = "SELECT r.id, r.title, r.cuisine, r.prep_time 
        FROM saved_recipes sr 
        JOIN recipes r ON sr.recipe_id = r.id 
        WHERE sr.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $saved_recipes[] = $row;
}
mysqli_stmt_close($stmt);

$search_history = [];
$sql = "SELECT ingredients, search_time 
        FROM search_history 
        WHERE user_id = ? 
        ORDER BY search_time DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $search_history[] = $row;
}
mysqli_stmt_close($stmt);
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
    <title>Recipe Finder System - Dashboard</title>
</head>
<body>
    <?php include "navbar.php"; ?>
    <div class="container">
        <div class="text-center mb-4">
            <h3>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</h3>
            <p class="text-muted">View your saved recipes and recent searches</p>
        </div>

        <?php if ($msg) { ?>
            <div class="alert <?php echo strpos($msg, 'successfully') !== false ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $msg; ?>
            </div>
        <?php } ?>

        <h4>Saved Recipes</h4>
        <table class="table table-hover text-center">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">Cuisine</th>
                    <th scope="col">Prep Time</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($saved_recipes as $recipe) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($recipe['title']); ?></td>
                    <td><?php echo htmlspecialchars($recipe['cuisine']); ?></td>
                    <td><?php echo htmlspecialchars($recipe['prep_time']); ?> min</td>
                    <td>
                        <a href="recipe-details.php?id=<?php echo $recipe['id']; ?>" class="link-dark me-3" data-bs-toggle="tooltip" data-bs-placement="top" title="View Recipe"><i class="fa-solid fa-eye fs-5"></i></a>
                        <a href="remove-saved-recipe.php?id=<?php echo $recipe['id']; ?>" class="link-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="Remove Recipe"><i class="fa-solid fa-trash fs-5"></i></a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <h4>Recent Searches</h4>
        <table class="table table-hover text-center">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Ingredients</th>
                    <th scope="col">Search Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($search_history as $search) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($search['ingredients']); ?></td>
                    <td><?php echo htmlspecialchars($search['search_time']); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>
</html>
