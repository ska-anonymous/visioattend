<?php
// do login if login form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $pass_hash = md5($password);

    require_once('../db-connect.php');

    // Prepare and bind
    $stmt = $conn->prepare("SELECT * FROM users_tbl WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $pass_hash); // "ss" means two strings

    // Execute the statement
    $success = $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    if (!$success) {
        $login_error = true;
        $message = "Server Error";
    }

    // Fetch data
    if ($result->num_rows > 0) {
        $login_error = false;
        $message = "Successfully Logged in";
        $redirect = true;

        session_start();
        $user_data = $result->fetch_array(MYSQLI_ASSOC);
        $_SESSION['user_data'] = $user_data;
    } else {
        $login_error = true;
        $message = "Login Failed! Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Fontawesome CSS CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <!-- Fontawesome CSS CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js" integrity="sha512-uKQ39gEGiyUJl4AI6L+ekBdGKpGw4xJ55+xyJG7YFlJokPNYegn9KwQ3P8A7aFQAUtUsAQHep+d/lrGqrbPIDQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- jQuery CSS CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Bootstrap CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body style="background-color: #cfd9df;">
    <div class="container mt-5">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-6">
                <form action="login.php" method="post">
                    <?php
                    if (isset($login_error)) {
                        $color = $login_error ? 'danger' : 'success';
                    ?>
                        <div class="alert alert-<?php echo $color ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php
                    }
                    ?>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-center">Login</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" name="username" id="username" placeholder="Enter username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" placeholder="Enter password" name="password" required>
                            </div>
                            <div class="form-group mt-3">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
    // redirect to main page if login successfull
    if (isset($redirect) && $redirect == true) {


    ?>
        <script>
            setTimeout(() => {
                window.location.href = '../index.php';
            }, 2000);
        </script>
    <?php
    }
    ?>

</body>

</html>