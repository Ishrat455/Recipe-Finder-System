<?php
  include "db_conn.php";

  if (isset($_POST["submit"])) {
     $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
     $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
     $email = mysqli_real_escape_string($conn, $_POST['email']);
     $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
     $性别 = mysqli_real_escape_string($conn, $_POST['gender']);

     $sql = "INSERT INTO `users`(`id`, `first_name`, `last_name`, `email`, `password`, `gender`) 
             VALUES (NULL, '$first_name', '$last_name', '$email', '$password', '$gender')";

     $result = mysqli_query($conn, $sql);

     if ($result) {
        header("Location: login.php?msg=Account created successfully");
     } else {
        echo "Failed: " . mysqli_error($conn);
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
     <title>Recipe Finder System - Sign Up</title>
  </head>
  <body>
     <?php include "navbar.php"; ?>
     <div class="container">
        <div class="text-center mb-4">
           <h3>Sign Up</h3>
           <p class="text-muted">Complete the form below to create a new account</p>
        </div>
        <div class="container d-flex justify-content-center">
           <form action="" method="post" style="width:50vw; min-width:300px;">
              <div class="row mb-3">
                 <div class="col">
                    <label class="form-label">First Name:</label>
                    <input type="text" class="form-control" name="first_name" placeholder="First Name" required>
                 </div>
                 <div class="col">
                    <label class="form-label">Last Name:</label>
                    <input type="text" class="form-control" name="last_name" placeholder="Last Name" required>
                 </div>
              </div>
              <div class="mb-3">
                 <label class="form-label">Email:</label>
                 <input type="email" class="form-control" name="email" placeholder="name@example.com" required>
              </div>
              <div class="mb-3">
                 <label class="form-label">Password:</label>
                 <input type="password" class="form-control" name="password" placeholder="Password" required>
              </div>
              <div class="form-group mb-3">
                 <label>Gender:</label>
                 &nbsp;
                 <input type="radio" class="form-check-input" name="gender" id="male" value="male" required>
                 <label for="male" class="form-input-label">Male</label>
                 &nbsp;
                 <input type="radio" class="form-check-input" name="gender" id="female" value="female">
                 <label for="female" class="form-input-label">Female</label>
              </div>
              <div>
                 <button type="submit" class="btn btn-success" name="submit">Sign Up</button>
                 <a href="login.php" class="btn btn-primary">Login</a>
              </div>
           </form>
        </div>
     </div>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  </body>
  </html>
