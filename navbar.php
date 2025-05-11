<?php
// No session_start() needed here
?>

<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #00ff5573;">
   <div class="container-fluid">
      <a class="navbar-brand fs-3" href="#">Recipe Finder System</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
         <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
         <ul class="navbar-nav ms-auto">
            <?php if (isset($_SESSION['user_id'])) { ?>
               <li class="nav-item">
                  <a class="nav-link" href="dashboard.php">Dashboard</a>
               </li>
               <li class="nav-item">
                  <a class="nav-link" href="search.php">Search Recipes</a>
               </li>
               <li class="nav-item">
                  <a class="nav-link" href="add-recipe.php">Add Recipe</a>
               </li>
               <li class="nav-item">
                  <a class="nav-link" href="logout.php">Logout</a>
               </li>
            <?php } else { ?>
               <li class="nav-item">
                  <a class="nav-link" href="login.php">Login</a>
               </li>
               <li class="nav-item">
                  <a class="nav-link" href="add-new.php">Sign Up</a>
               </li>
            <?php } ?>
         </ul>
      </div>
   </div>
</nav>