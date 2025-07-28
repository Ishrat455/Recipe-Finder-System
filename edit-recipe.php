<?php
include "db_conn.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please log in to edit a recipe.");
    exit;
}

$recipe_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$recipe = null;
$ingredients = [];
$error = '';

// Check if user is admin
$user_id = $_SESSION['user_id'];
$sql = "SELECT is_admin FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    $error = "Database error: Unable to prepare user query.";
    error_log("edit-recipe.php: Failed to prepare user query: " . mysqli_error($conn));
} else {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    $is_admin = $user['is_admin'] ?? 0;
    mysqli_stmt_close($stmt);
}

if ($recipe_id > 0) {
    $sql = $is_admin ? 
        "SELECT r.* FROM recipes r WHERE r.id = ?" :
        "SELECT r.* FROM recipes r WHERE r.id = ? AND r.user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        $error = "Database error: Unable to prepare recipe query.";
        error_log("edit-recipe.php: Failed to prepare recipe query: " . mysqli_error($conn));
    } else {
        if ($is_admin) {
            mysqli_stmt_bind_param($stmt, "i", $recipe_id);
        } else {
            mysqli_stmt_bind_param($stmt, "ii", $recipe_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $recipe = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($recipe) {
            $sql = "SELECT i.id, i.name, ri.amount, ri.unit 
                    FROM recipe_ingredients ri 
                    JOIN ingredients i ON ri.ingredient_id = i.id 
                    WHERE ri.recipe_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                $error = "Database error: Unable to prepare ingredients query.";
                error_log("edit-recipe.php: Failed to prepare ingredients query: " . mysqli_error($conn));
            } else {
                mysqli_stmt_bind_param($stmt, "i", $recipe_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $ingredients = mysqli_fetch_all($result, MYSQLI_ASSOC);
                mysqli_stmt_close($stmt);
            }
        } else {
            $error = "Recipe not found or you don't have permission to edit it.";
            error_log("edit-recipe.php: Recipe ID $recipe_id not found or user ID $user_id lacks permission. Is admin: $is_admin");
        }
    }
}

if (isset($_POST['submit']) && $recipe) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $cuisine = mysqli_real_escape_string($conn, $_POST['cuisine']);
    $prep_time = (int)$_POST['prep_time'];
    $instructions = mysqli_real_escape_string($conn, $_POST['instructions']);
    $ingredient_ids = isset($_POST['ingredient_ids']) ? $_POST['ingredient_ids'] : [];
    $amounts = isset($_POST['amounts']) ? $_POST['amounts'] : [];
    $units = isset($_POST['units']) ? $_POST['units'] : [];
    $names = isset($_POST['names']) ? $_POST['names'] : [];

    if (!$is_admin) {
        $sql = "SELECT id FROM recipes WHERE title = ? AND cuisine = ? AND user_id = ? AND id != ?";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            $error = "Database error: Unable to prepare duplicate check query.";
            error_log("edit-recipe.php: Failed to prepare duplicate check query: " . mysqli_error($conn));
        } else {
            mysqli_stmt_bind_param($stmt, "ssii", $title, $cuisine, $user_id, $recipe_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) > 0) {
                $error = "A recipe with this title and cuisine already exists.";
                mysqli_stmt_close($stmt);
            } else {
                mysqli_stmt_close($stmt);
                update_recipe($conn, $recipe_id, $title, $cuisine, $prep_time, $instructions, $user_id, $is_admin, $amounts, $units, $names);
            }
        }
    } else {
        update_recipe($conn, $recipe_id, $title, $cuisine, $prep_time, $instructions, $user_id, $is_admin, $amounts, $units, $names);
    }
}

function update_recipe($conn, $recipe_id, $title, $cuisine, $prep_time, $instructions, $user_id, $is_admin, $amounts, $units, $names) {
    mysqli_begin_transaction($conn);
    try {
        $sql = $is_admin ?
            "UPDATE recipes SET title = ?, cuisine = ?, prep_time = ?, instructions = ? WHERE id = ?" :
            "UPDATE recipes SET title = ?, cuisine = ?, prep_time = ?, instructions = ? WHERE id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare recipe update query: " . mysqli_error($conn));
        }
        if ($is_admin) {
            mysqli_stmt_bind_param($stmt, "ssisi", $title, $cuisine, $prep_time, $instructions, $recipe_id);
        } else {
            mysqli_stmt_bind_param($stmt, "ssisii", $title, $cuisine, $prep_time, $instructions, $recipe_id, $user_id);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $sql = "DELETE FROM recipe_ingredients WHERE recipe_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare ingredient delete query: " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($stmt, "i", $recipe_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        for ($i = 0; $i < count($names); $i++) {
            if (empty($names[$i])) continue;
            $name = mysqli_real_escape_string($conn, trim($names[$i]));
            $amount = !empty($amounts[$i]) ? (float)$amounts[$i] : 1;
            $unit = !empty($units[$i]) ? mysqli_real_escape_string($conn, trim($units[$i])) : 'unit';

            $sql = "INSERT IGNORE INTO ingredients (name) VALUES (?)";
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare ingredient insert query: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, "s", $name);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $sql = "SELECT id FROM ingredients WHERE name = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare ingredient select query: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, "s", $name);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            $ingredient_id = $row['id'];
            mysqli_stmt_close($stmt);

            $sql = "INSERT INTO recipe_ingredients (recipe_id, ingredient_id, amount, unit) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare recipe_ingredients insert query: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, "iids", $recipe_id, $ingredient_id, $amount, $unit);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        mysqli_commit($conn);
        header("Location: dashboard.php?msg=Recipe updated successfully");
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        global $error;
        $error = "Failed to update recipe: " . $e->getMessage();
        error_log("edit-recipe.php: " . $e->getMessage());
    }
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
    <title>Recipe Finder System - Edit Recipe</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .card-body {
            padding: 2rem;
        }
        .form-label {
            font-weight: 500;
            color: #333;
        }
        .instructions-textarea {
            min-height: 150px;
            resize: vertical;
            border-radius: 6px;
        }
        .ingredient-row {
            margin-bottom: 0.75rem;
            opacity: 1;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .ingredient-row.fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        .ingredient-row.fade-out {
            opacity: 0;
            transform: translateY(10px);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .input-group select, .input-group input {
            border-radius: 6px;
            border: 1px solid #ced4da;
        }
        .input-group select:focus, .input-group input:focus {
            border-color: #4dabf7;
            box-shadow: 0 0 0 0.2rem rgba(77, 171, 247, 0.25);
        }
        .btn-add-ingredient, .btn-remove {
            border-radius: 6px;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }
        .btn-add-ingredient:hover, .btn-remove:hover {
            transform: translateY(-2px);
        }
        .btn-add-ingredient:active, .btn-remove:active {
            transform: translateY(0);
        }
        @media (max-width: 576px) {
            .ingredient-row .input-group {
                flex-direction: column;
                gap: 0.5rem;
            }
            .ingredient-row .input-group > * {
                width: 100%;
            }
            .btn-remove {
                margin-left: 0;
                margin-top: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include "navbar.php"; ?>
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h3>Edit Recipe</h3>
            <p class="text-muted">Update your recipe details</p>
        </div>
        <?php if ($error) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>
        <?php if ($recipe) { ?>
            <div class="card">
                <div class="card-body">
                    <form action="" method="post">
                        <div class="mb-3">
                            <label class="form-label">Recipe Title:</label>
                            <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($recipe['title']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cuisine:</label>
                            <input type="text" class="form-control" name="cuisine" value="<?php echo htmlspecialchars($recipe['cuisine']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Preparation Time (minutes):</label>
                            <input type="number" class="form-control" name="prep_time" value="<?php echo htmlspecialchars($recipe['prep_time']); ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Ingredients:</label>
                            <div id="ingredient-list">
                                <?php foreach ($ingredients as $index => $ingredient) { ?>
                                    <div class="ingredient-row input-group mb-2">
                                        <input type="hidden" name="ingredient_ids[]" value="<?php echo htmlspecialchars($ingredient['id']); ?>">
                                        <input type="number" step="0.1" class="form-control" name="amounts[]" value="<?php echo htmlspecialchars($ingredient['amount']); ?>" placeholder="Amount" required>
                                        <select class="form-select" name="units[]" required>
                                            <option value="<?php echo htmlspecialchars($ingredient['unit']); ?>" selected><?php echo htmlspecialchars($ingredient['unit']); ?></option>
                                            <option value="grams">grams</option>
                                            <option value="cups">cups</option>
                                            <option value="tbsp">tbsp</option>
                                            <option value="tsp">tsp</option>
                                            <option value="units">units</option>
                                            <option value="ml">ml</option>
                                            <option value="oz">oz</option>
                                        </select>
                                        <input type="text" class="form-control" name="names[]" value="<?php echo htmlspecialchars($ingredient['name']); ?>" placeholder="Ingredient name" required>
                                        <button type="button" class="btn btn-outline-danger btn-remove" data-bs-toggle="tooltip" data-bs-placement="top" title="Remove Ingredient">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                <?php } ?>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-add-ingredient mt-2" id="add-ingredient" data-bs-toggle="tooltip" data-bs-placement="top" title="Add Ingredient">
                                <i class="fa-solid fa-plus me-1"></i> Add Ingredient
                            </button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Instructions:</label>
                            <textarea class="form-control instructions-textarea" name="instructions" required><?php echo htmlspecialchars($recipe['instructions']); ?></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success" name="submit" data-bs-toggle="tooltip" data-bs-placement="top" title="Update Recipe">
                                <i class="fa-solid fa-check me-1"></i> Update Recipe
                            </button>
                            <a href="dashboard.php" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Back to Dashboard">
                                <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                No recipe found or you don't have permission to edit it.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            // Add ingredient row
            document.getElementById('add-ingredient').addEventListener('click', function () {
                const ingredientList = document.getElementById('ingredient-list');
                const newRow = document.createElement('div');
                newRow.className = 'ingredient-row input-group mb-2 fade-in';
                newRow.innerHTML = `
                    <input type="hidden" name="ingredient_ids[]" value="">
                    <input type="number" step="0.1" class="form-control" name="amounts[]" placeholder="Amount" required>
                    <select class="form-select" name="units[]" required>
                        <option value="" disabled selected>Select unit</option>
                        <option value="grams">grams</option>
                        <option value="cups">cups</option>
                        <option value="tbsp">tbsp</option>
                        <option value="tsp">tsp</option>
                        <option value="units">units</option>
                        <option value="ml">ml</option>
                        <option value="oz">oz</option>
                    </select>
                    <input type="text" class="form-control" name="names[]" placeholder="Ingredient name" required>
                    <button type="button" class="btn btn-outline-danger btn-remove" data-bs-toggle="tooltip" data-bs-placement="top" title="Remove Ingredient">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                `;
                ingredientList.appendChild(newRow);
                // Re-initialize tooltips for new button
                new bootstrap.Tooltip(newRow.querySelector('.btn-remove'));
                // Remove fade-in class after animation
                setTimeout(() => newRow.classList.remove('fade-in'), 300);
            });

            // Remove ingredient row
            document.getElementById('ingredient-list').addEventListener('click', function (e) {
                if (e.target.closest('.btn-remove')) {
                    const row = e.target.closest('.ingredient-row');
                    row.classList.add('fade-out');
                    setTimeout(() => row.remove(), 300);
                }
            });
        });
    </script>
</body>
</html>