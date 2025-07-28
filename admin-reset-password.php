<?php
include "db_conn.php";

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php?msg=Access denied. Admins only.");
    exit;
}

$user_id = isset($_GET['id']) ? $_GET['id'] : null;
$message = '';

if (!$user_id) {
    header("Location: admin.php?msg=Invalid user ID.");
    exit;
}

// Fetch user details
$sql = "SELECT first_name, last_name, email FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
    header("Location: admin.php?msg=User not found.");
    exit;
}
$user = mysqli_fetch_assoc($result);

if (isset($_POST['submit'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
        if (mysqli_query($conn, $sql)) {
            $message = '<div class="alert alert-success">Password updated successfully for ' . $user['first_name'] . '. New password: ' . $new_password . '</div>';
        } else {
            $message = '<div class="alert alert-danger">Failed to update password.</div>';
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <title>Recipe Finder System - Reset User Password</title>
</head>
<body>
    <?php include "navbar.php"; ?>
    <div class="container">
        <div class="text-center mb-4">
            <h3>Reset Password for <?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h3>
            <p class="text-muted">Enter a new password for <?php echo $user['email']; ?></p>
        </div>
        <?php echo $message; ?>
        <div class="container d-flex justify-content-center">
            <form action="" method="post" style="width:50vw; min-width:300px;">
                <div class="mb-3 position-relative">
                    <label class="form-label">New Password:</label>
                    <input type="password" class="form-control" name="new_password" id="new_password" required>
                    <span class="password-toggle" onclick="togglePassword('new_password')">
                        <i class="fa-solid fa-eye" id="new_password-toggle-icon"></i>
                    </span>
                </div>
                <div class="mb-3 position-relative">
                    <label class="form-label">Confirm New Password:</label>
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                    <span class="password-toggle" onclick="togglePassword('confirm_password')">
                        <i class="fa-solid fa-eye" id="confirm_password-toggle-icon"></i>
                    </span>
                </div>
                <div>
                    <button type="submit" class="btn btn-success" name="submit">Update Password</button>
                    <a href="admin.php" class="btn btn-primary">Back to Admin Panel</a>
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