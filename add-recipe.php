<?php
  include "db_conn.php";

  if (!isset($_SESSION['user_id'])) {
     header("Location: login.php");
  }

  $user_id = $_SESSION['user_id'];

  if (isset($_POST['submit'])) {
     $title = mysqli_real_escape_string($conn, $_POST['title']);
     $instructions = mysqli_real_escape_string($conn, $_POST['instructions']);
     $prep_time = (int)$_POST['prep_time'];
     $cuisine = mysqli_real_escape_string($conn, $_POST['cuisine']);
     $ingredients = array_map('trim', explode(',', $_POST['ingredients']));
     $quantities = array_map('trim', explode(',', $_POST['quantities']));

     // Insert recipe
     $sql = "INSERT INTO recipes (title, instructions, prep_time, cuisine, created_by) 
             VALUES ('$title', '$instructions', $prep_time, '$cuisine', $user_id)";
     mysqli_query($conn, $sql);
     $recipe_id = mysqli_insert_id($conn);

     // Insert ingredients
     foreach ($ingredients as $index => $ing) {
        $ing = mysqli_real_escape_string($conn, $ing);
        $quantity = mysqli_real_escape_string($conn, $quantities[$index]);
        $sql = "INSERT INTO ingredients (name) VALUES ('$ing') ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)";
        mysqli_query($conn, $sql);
        $ingredient_id = mysqli_insert_id($conn) ?: mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM ingredients WHERE name='$ing'"))['id'];
        $sql = "INSERT INTO recipe_ingredients (recipe_id, ingredient_id, quantity) VALUES ($recipe_id, $ingredient_id, '$quantity')";
        mysqli_query($conn, $sql);
     }

     header("Location: dashboard.php?msg=Recipe added successfully");
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
     <title>Recipe Finder System - Add Recipe</title>
  </head>
  <body>
     <?php include "navbar.php"; ?>
     <div class="container">
        <h3>Add New Recipe</h3>
        <form action="" method="post">
           <div class="mb-3">
              <label class="form-label">Title:</label>
              <input type="text" class="form-control" name="title" required>
           </div>
           <div class="mb-3">
              <label class="form-label">Instructions:</label>
              <textarea class="form-control" name="instructions" required></textarea>
           </div>
           <div class="mb-3">
              <label class="form-label">Preparation Time (minutes):</label>
              <input type="number" class="form-control" name="prep_time" required>
           </div>
           <div class="mb-3">
              <label class="form-label">Cuisine:</label>
              <input type="text" class="form-control" name="cuisine">
           </div>
           <div class="mb-3">
              <label class="form-label">Ingredients (comma-separated):</label>
              <input type="text" class="form-control" name="ingredients" placeholder="e.g., chicken, rice, tomato" required>
           </div>
           <div class="mb-3">
              <label class="form-label">Quantities (comma-separated):</label>
              <input type="text" class="form-control" name="quantities" placeholder="e.g., 2 cups, 1 cup, 3 pieces" required>
           </div>
           <button type="submit" class="btn btn-success" name="submit">Save Recipe</button>
        </form>
     </div>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  </body>
  </html>