<?php
  include "db_conn.php";

  if (!isset($_SESSION['user_id'])) {
     header("Location: login.php");
  }

  $user_id = $_SESSION['user_id'];
  $recipes = [];
  $search_performed = false;

  if (isset($_POST['submit'])) {
     $ingredients = array_map('trim', explode(',', $_POST['ingredients']));
     $ingredients = array_filter($ingredients); // Remove empty entries
     $search_performed = true;

     // Save search to history
     $ingredients_str = mysqli_real_escape_string($conn, implode(',', $ingredients));
     $history_sql = "INSERT INTO search_history (user_id, ingredients) VALUES ($user_id, '$ingredients_str')";
     mysqli_query($conn, $history_sql);

     // Find recipes
     $placeholders = array_fill(0, count($ingredients), '?');
     $placeholders = implode(',', $placeholders);
     $sql = "SELECT DISTINCT r.* FROM recipes r
             JOIN recipe_ingredients ri ON r.id = ri.recipe_id
             JOIN ingredients i ON ri.ingredient_id = i.id
             WHERE i.name IN ($placeholders)";
     $stmt = mysqli_prepare($conn, $sql);
     mysqli_stmt_bind_param($stmt, str_repeat('s', count($ingredients)), ...$ingredients);
     mysqli_stmt_execute($stmt);
     $result = mysqli_stmt_get_result($stmt);
     while ($row = mysqli_fetch_assoc($result)) {
        $recipes[] = $row;
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
     <title>Recipe Finder System - Search Recipes</title>
  </head>
  <body>
     <?php include "navbar.php"; ?>
     <div class="container">
        <h3>Search Recipes</h3>
        <form action="" method="post">
           <div class="mb-3">
              <label class="form-label">Ingredients (comma-separated):</label>
              <input type="text" class="form-control" name="ingredients" placeholder="e.g., chicken, rice, tomato">
           </div>
           <button type="submit" class="btn btn-success" name="submit">Search</button>
        </form>
        <?php if ($search_performed) { ?>
           <h4>Results</h4>
           <?php if (empty($recipes)) { ?>
              <p>No recipes found.</p>
           <?php } else { ?>
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
                    <?php foreach ($recipes as $recipe) { ?>
                       <tr>
                          <td><?php echo $recipe['title']; ?></td>
                          <td><?php echo $recipe['cuisine']; ?></td>
                          <td><?php echo $recipe['prep_time']; ?> min</td>
                          <td>
                             <a href="recipe-details.php?id=<?php echo $recipe['id']; ?>" class="link-dark"><i class="fa-solid fa-eye fs-5"></i></a>
                             <a href="save-recipe.php?id=<?php echo $recipe['id']; ?>&user_id=<?php echo $user_id; ?>" class="link-dark"><i class="fa-solid fa-heart fs-5"></i></a>
                          </td>
                       </tr>
                    <?php } ?>
                 </tbody>
              </table>
           <?php } ?>
        <?php } ?>
     </div>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  </body>
  </html>