<?php
session_start();

// Agar user pehle se logged in hai, toh use home par bhej dein
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

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Password verification
            if (password_verify($password, $user['password'])) {
                // Session variables set karein
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Agar admin hai toh admin dashboard par bhejey, warna home par
                if ($user['role'] == 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: home.php");
                }
                exit();
            } else {
                $error = "Invalid email or password!";
            }
        } else {
            $error = "No account found with this email!";
        }
        $stmt->close();
    } else {
        $error = "Please fill in all fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zayka Premium | Sign In</title>
    <link class="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
            <p class="text-secondary small">Sign in to manage your orders</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger bg-dark text-danger border-danger small p-2 text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-3">
                <label class="form-label text-secondary small">Email Address</label>
                <input type="email" name="email" class="form-control form-control-luxury" placeholder="name@example.com" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-secondary small">Password</label>
                <input type="password" name="password" class="form-control form-control-luxury" placeholder="••••••••" required>
            </div>
            <button type="submit" name="login" class="btn btn-gold mb-3">Sign In</button>
        </form>

        <div class="text-center mt-3 small">
            <span class="text-secondary">Don't have an account?</span> 
            <a href="register.php" class="auth-link ms-1">Register here</a>
        </div>
    </div>

</body>
</html>