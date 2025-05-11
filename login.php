<?php
  include "db_conn.php";

  if (isset($_POST["submit"])) {
     $email = mysqli_real_escape_string($conn, $_POST['email']);
     $password = $_POST['password'];

     $sql = "SELECT * FROM `users` WHERE email = '$email'";
     $result = mysqli_query($conn, $sql);

     if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
           $_SESSION['user_id'] = $row['id'];
           $_SESSION['first_name'] = $row['first_name'];
           header("Location: dashboard.php");
        } else {
           $error = "Invalid password";
        }
     } else {
        $error = "No user found with that email";
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
     <title>Recipe Finder System - Login</title>
  </head>
  <body>
     <?php include "navbar.php"; ?>
     <div class="container">
        <div class="text-center mb-4">
           <h3>Login</h3>
           <p class="text-muted">Enter your credentials to access your account</p>
        </div>
        <?php if (isset($error)) { ?>
           <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>
        <div class="container d-flex justify-content-center">
           <form action="" method="post" style="width:50vw; min-width:300px;">
              <div class="mb-3">
                 <label class="form-label">Email:</label>
                 <input type="email" class="form-control" name="email" placeholder="name@example.com" required>
              </div>
              <div class="mb-3">
                 <label class="form-label">Password:</label>
                 <input type="password" class="form-control" name="password" placeholder="Password" required>
              </div>
              <div>
                 <button type="submit" class="btn btn-success" name="submit">Login</button>
                 <a href="add-new.php" class="btn btn-primary">Sign Up</a>
              </div>
           </form>
        </div>
     </div>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  </body>
  </html>