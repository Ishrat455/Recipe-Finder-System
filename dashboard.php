<?php
  include "db_conn.php";

  if (!isset($_SESSION['user_id'])) {
     header("Location: login.php");
  }

  $user_id = $_SESSION['user_id'];

  // Fetch saved recipes
  $saved_sql = "SELECT r.* FROM recipes r JOIN saved_recipes sr ON r.id = sr.recipe_id WHERE sr.user_id = $user_id";
  $saved_result = mysqli_query($conn, $saved_sql);

  // Fetch search history
  $history_sql = "SELECT * FROM search_history WHERE user_id = $user_id ORDER BY search_time DESC LIMIT 5";
  $history_result = mysqli_query($conn, $history_sql);
  ?>

  <!DOCTYPE html>
  <html lang="en">
  <head>
     <meta charset="UTF-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
     <title>Recipe Finder System - Dashboard</title>
  </head>
  <body>
     <?php include "navbar.php"; ?>
     <div class="container">
        <h3>Welcome, <?php echo $_SESSION['first_name']; ?>!</h3>
        <h4>Saved Recipes</h4>
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
              <?php while ($row = mysqli_fetch_assoc($saved_result)) { ?>
                 <tr>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['cuisine']; ?></td>
                    <td><?php echo $row['prep_time']; ?> min</td>
                    <td>
                       <a href="recipe-details.php?id=<?php echo $row['id']; ?>" class="link-dark"><i class="fa-solid fa-eye fs-5"></i></a>
                    </td>
                 </tr>
              <?php } ?>
           </tbody>
        </table>
        <h4>Recent Searches</h4>
        <ul>
           <?php while ($row = mysqli_fetch_assoc($history_result)) { ?>
              <li><?php echo $row['ingredients']; ?> (<?php echo $row['search_time']; ?>)</li>
           <?php } ?>
        </ul>
     </div>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  </body>
  </html>