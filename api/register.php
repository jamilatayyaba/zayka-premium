<?php
session_start();

// Agar user pehle se logged in hai, toh use seedha home page par bhej dein
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

// Database connection
$conn = new mysqli("sql306.infinityfree.com", "if0_41991462", "2lJ40TB1abYZ", "if0_41991462_restaurant_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";
$success = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = 'customer'; // Default role customer hoga

    if (!empty($username) && !empty($email) && !empty($password)) {
        // Check if email already exists
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();

        if ($check_email->num_rows > 0) {
            $error = "This email is already registered!";
        } else {
            // Password hashing for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert into database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);
            
            if ($stmt->execute()) {
                $success = "Account created successfully! You can now login.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
            $stmt->close();
        }
        $check_email->close();
    } else {
        $error = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zayka Premium | Create Account</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #0f1210;
            color: #f4f1ea;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-container {
            background-color: #131815;
            border: 1px solid rgba(197, 160, 89, 0.15);
            border-radius: 14px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .form-control-luxury {
            background-color: #1a201c !important;
            border: 1px solid rgba(197, 160, 89, 0.2) !important;
            color: #f4f1ea !important;
            padding: 12px;
            border-radius: 8px;
        }
        .form-control-luxury:focus {
            border-color: #c5a059 !important;
            box-shadow: 0 0 8px rgba(197, 160, 89, 0.2) !important;
        }
        .btn-gold {
            background-color: #c5a059;
            color: #0f1210;
            font-weight: 600;
            padding: 12px;
            border: none;
            border-radius: 30px;
            width: 100%;
        }
        .btn-gold:hover {
            background-color: #dbb368;
        }
        .auth-link {
            color: #c5a059;
            text-decoration: none;
        }
        .auth-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <div class="text-center mb-4">
            <h2 style="color: #c5a059;" class="fw-bold"><i class="fa-solid fa-pizza-slice me-2"></i> ZAYKA</h2>
            <p class="text-secondary small">Join the premium Italian culinary experience</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger bg-dark text-danger border-danger small p-2 text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if(!empty($success)): ?>
            <div class="alert alert-success bg-dark text-warning border-warning small p-2 text-center"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="mb-3">
                <label class="form-label text-secondary small">Full Name</label>
                <input type="text" name="username" class="form-control form-control-luxury" placeholder="Enter your name" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-secondary small">Email Address</label>
                <input type="email" name="email" class="form-control form-control-luxury" placeholder="name@example.com" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-secondary small">Password</label>
                <input type="password" name="password" class="form-control form-control-luxury" placeholder="••••••••" required>
            </div>
            <button type="submit" name="register" class="btn btn-gold mb-3">Create Account</button>
        </form>

        <div class="text-center mt-3 small">
            <span class="text-secondary">Already have an account?</span> 
            <a href="login.php" class="auth-link ms-1">Login here</a>
        </div>
    </div>

</body>
</html>