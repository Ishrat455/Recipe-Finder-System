<?php
   include "db_conn.php";

   $message = '';
   $token = isset($_GET['token']) ? $_GET['token'] : '';
   $email = isset($_GET['email']) ? $_GET['email'] : '';

   if ($token && $email) {
      $sql = "SELECT * FROM password_resets WHERE email = ? AND token = ? AND expires_at > NOW()";
      $stmt = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($stmt, "ss", $email, $token);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);

      if (mysqli_num_rows($result) == 0) {
         $message = '<div class="alert alert-danger">Invalid or expired token.</div>';
         $token = '';
         $email = '';
      }
   }

   if (isset($_POST['submit']) && $token && $email) {
      $new_password = $_POST['new_password'];
      $confirm_password = $_POST['confirm_password'];

      if ($new_password === $confirm_password) {
         $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
         $sql = "UPDATE users SET password = ? WHERE email = ?";
         $stmt = mysqli_prepare($conn, $sql);
         mysqli_stmt_bind_param($stmt, "ss", $hashed_password, $email);
         if (mysqli_stmt_execute($stmt)) {
            $sql = "DELETE FROM password_resets WHERE email = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $message = '<div class="alert alert-success">Password reset successfully. <a href="login.php">Login</a></div>';
            $token = '';
            $email = '';
         } else {
            $message = '<div class="alert alert-danger">Failed to reset password.</div>';
         }
      } else {
         $message = '<div class="alert alert-danger">Passwords do not match.</div>';
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
      <title>Recipe Finder System - Set New Password</title>
   </head>
   <body>
      <?php include "navbar.php"; ?>
      <div class="container">
         <div class="text-center mb-4">
            <h3>Set New Password</h3>
            <p class="text-muted">Enter your new password below</p>
         </div>
         <?php echo $message; ?>
         <?php if ($token && $email) { ?>
            <div class="container d-flex justify-content-center">
               <form action="" method="post" style="width:50vw; min-width:300px;">
                  <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                  <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                  <div class="mb-3">
                     <label class="form-label">New Password:</label>
                     <input type="password" class="form-control" name="new_password" required>
                  </div>
                  <div class="mb-3">
                     <label class="form-label">Confirm New Password:</label>
                     <input type="password" class="form-control" name="confirm_password" required>
                  </div>
                  <div>
                     <button type="submit" class="btn btn-success" name="submit">Reset Password</button>
                     <a href="login.php" class="btn btn-primary">Back to Login</a>
                  </div>
               </form>
            </div>
         <?php } ?>
      </div>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
   </body>
   </html>