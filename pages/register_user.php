<?php
// store user in database if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user inputs
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'cr';

    // Hash the password for security
    $hashed_password = md5($password);

    // Prepare statement to insert user data
    $stmt = $conn->prepare("INSERT INTO users_tbl (username, email, password, role) VALUES (?, ?, ?, ?)");

    // Bind parameters
    $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

    // Execute the statement
    if (!$stmt->execute()) {
        $registration_error = true;
        $message = 'Server Error. Try again later.';
    }

    // Check if the insertion was successful
    if ($stmt->affected_rows === 1) {
        $registration_error = false;
        $message = 'User Registered Successfully';
    } else {
        $registration_error = true;
        $message = 'User registration failed! Try again later.';
    }
}
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <?php
            if (isset($registration_error)) {
                $color = $registration_error ? 'danger' : 'success';
            ?>
                <div class="alert alert-<?php echo $color ?>">
                    <?php echo $message; ?>
                </div>
            <?php
            }
            ?>
            <form action="" method="post">
                <div class="card">
                    <div class="card-header">
                        <div class="text-center">Register User</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" placeholder="Enter username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input type="email" class="form-control" id="email" placeholder="Enter email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" placeholder="Enter password" minlength="5" name="password" required>
                        </div>
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>