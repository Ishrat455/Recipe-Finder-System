<?php
  include "db_conn.php";

  if (!isset($_SESSION['user_id'])) {
     header("Location: login.php");
  }

  $recipe_id = $_GET['id'];
  $user_id = $_GET['user_id'];

  $sql = "INSERT INTO saved_recipes (user_id, recipe_id, is_favorite) VALUES ($user_id, $recipe_id, TRUE)
          ON DUPLICATE KEY UPDATE is_favorite = TRUE";
  mysqli_query($conn, $sql);

  header("Location: dashboard.php?msg=Recipe saved successfully");
  ?>