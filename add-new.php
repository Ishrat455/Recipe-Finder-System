<?php
include "db_conn.php";

if (isset($_POST["submit"])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);

    if ($password === $confirm_password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO `users` (`first_name`, `last_name`, `email`, `password`, `gender`) 
                VALUES ('$first_name', '$last_name', '$email', '$hashed_password', '$gender')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            header("Location: login.php?msg=Account created successfully");
        } else {
            $error = "Failed: " . mysqli_error($conn);
        }
    } else {
        $error = "Passwords do not match";
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
    <title>Recipe Finder System - Sign Up</title>
</head>
<body>
    <?php include "navbar.php"; ?>
    <div class="container">
        <div class="text-center mb-4">
            <h3>Sign Up</h3>
            <p class="text-muted">Complete the form to create a new account</p>
        </div>
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>
        <div class="container d-flex justify-content-center">
            <form action="" method="post" style="width:50vw; min-width:300px;">
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">First Name:</label>
                        <input type="text" class="form-control" name="first_name" placeholder="John" required>
                    </div>
                    <div class="col">
                        <label class="form-label">Last Name:</label>
                        <input type="text" class="form-control" name="last_name" placeholder="Doe" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" class="form-control" name="email" placeholder="name@example.com" required>
                </div>
                <div class="mb-3 position-relative">
                    <label class="form-label">Password:</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                    <span class="password-toggle" onclick="togglePassword('password')">
                        <i class="fa-solid fa-eye" id="password-toggle-icon"></i>
                    </span>
                </div>
                <div class="mb-3 position-relative">
                    <label class="form-label">Confirm Password:</label>
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                    <span class="password-toggle" onclick="togglePassword('confirm_password')">
                        <i class="fa-solid fa-eye" id="confirm_password-toggle-icon"></i>
                    </span>
                </div>
                <div class="form-group mb-3">
                    <label>Gender:</label>
                    <input type="radio" class="form-check-input" name="gender" id="male" value="male" required>
                    <label for="male" class="form-input-label">Male</label>
                    <input type="radio" class="form-check-input" name="gender" id="female" value="female">
                    <label for="female" class="form-input-label">Female</label>
                </div>
                <div>
                    <button type="submit" class="btn btn-success" name="submit" title="Sign Up">Sign Up</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-toggle-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
