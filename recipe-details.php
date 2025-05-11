<?php
  include "db_conn.php";

  if (!isset($_SESSION['user_id'])) {
     header("Location: login.php");
  }

  $recipe_id = $_GET['id'];
  $sql = "SELECT r.*, GROUP_CONCAT(CONCAT(i.name, ' (', ri.quantity, ')') SEPARATOR ', ') as ingredients
          FROM recipes r
          JOIN recipe_ingredients ri ON r.id = ri.recipe_id
          JOIN ingredients i ON ri.ingredient_id = i.id
          WHERE r.id = $recipe_id
          GROUP BY r.id";
  $result = mysqli_query($conn, $sql);
  $recipe = mysqli_fetch_assoc($result);
  ?>

  <!DOCTYPE html>
  <html lang="en">
  <head>
     <meta charset="UTF-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
     <title>Recipe Finder System - Recipe Details</title>
  </head>
  <body>
     <?php include "navbar.php"; ?>
     <div class="container">
        <h3><?php echo $recipe['title']; ?></h3>
        <p><strong>Cuisine:</strong> <?php echo $recipe['cuisine']; ?></p>
        <p><strong>Preparation Time:</strong> <?php echo $recipe['prep_time']; ?> minutes</p>
        <p><strong>Ingredients:</strong> <?php echo $recipe['ingredients']; ?></p>
        <p><strong>Instructions:</strong> <?php echo nl2br($recipe['instructions']); ?></p>
        <a href="save-recipe.php?id=<?php echo $recipe['id']; ?>&user_id=<?php echo $_SESSION['user_id']; ?>" class="btn btn-primary">Save/Favorite</a>
     </div>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  </body>
  </html>