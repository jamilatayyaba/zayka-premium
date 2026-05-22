<?php
session_start();

// Security Guardrail: Agar user logged in nahi hai, toh cart access nahi karne dena
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("sql306.infinityfree.com", "if0_41991462", "2lJ40TB1abYZ", "if0_41991462_restaurant_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle empty cart action
if (isset($_POST['empty_cart'])) {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit();
}

// Handle Order Confirmation
if (isset($_POST['place_order']) && !empty($_SESSION['cart'])) {
    $user_id = $_SESSION['user_id']; // Logged in user ki real ID
    $total_amount = 0;
    
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
    
    $status = "Pending";
    
    // Insert order record securely
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)");
    $stmt->bind_param("ids", $user_id, $total_amount, $status);
    
    if ($stmt->execute()) {
        $_SESSION['cart'] = []; // Clear cart after successful order
        echo "<script>
    alert('Thank You! Your order is confirmed and placed. 😍');
    window.location.href='home.php';
     </script>";
exit();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zayka Premium | Shopping Cart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0f1210; color: #f4f1ea; font-family: 'Segoe UI', sans-serif; }
        .navbar-luxury { background-color: #070908 !important; border-bottom: 2px solid #c5a059; padding: 15px 0; }
        .cart-wrapper { background-color: #131815; border: 1px solid rgba(197, 160, 89, 0.15); border-radius: 14px; padding: 40px; margin-top: 50px; }
        .table-luxury { color: #f4f1ea !important; }
        .table-luxury th { color: #c5a059 !important; border-bottom: 2px solid rgba(197, 160, 89, 0.3) !important; }
        .table-luxury td { border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important; padding: 15px 10px; }
        .btn-gold { background-color: #c5a059; color: #0f1210; font-weight: 600; border-radius: 30px; border: none; padding: 12px 28px; }
        .btn-gold:hover { background-color: #dbb368; color: #0f1210; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-luxury">
        <div class="container">
            <a class="navbar-brand text-warning fw-bold" href="home.php"><i class="fa-solid fa-pizza-slice me-2"></i> ZAYKA PREMIUM</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link text-secondary" href="home.php">Back To Menu</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="cart-wrapper">
            <h2 class="mb-4 text-warning fw-bold"><i class="fa-solid fa-basket-shopping me-2"></i> Il Tuo Carrello (Your Cart)</h2>
            
            <?php if (empty($_SESSION['cart'])): ?>
                <div class="text-center py-5 text-secondary">
                    <i class="fa-solid fa-cart-arrow-down fs-1 mb-3"></i>
                    <p>Your premium selection is empty.</p>
                    <a href="home.php" class="btn btn-outline-warning btn-sm px-4 rounded-pill">Explore Menu</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-luxury align-middle">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $grand_total = 0;
                            foreach ($_SESSION['cart'] as $id => $item): 
                                $subtotal = $item['price'] * $item['quantity'];
                                $grand_total += $subtotal;
                            ?>
                            <tr>
                                <td><i class="fa-regular fa-circle-dot text-warning me-2" style="font-size: 10px;"></i><?php echo $item['name']; ?></td>
                                <td>Rs. <?php echo $item['price']; ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td class="text-end text-warning fw-semibold">Rs. <?php echo $subtotal; ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="border-0">
                                <td colspan="2" class="border-0">
                                    <form method="POST" action="cart.php">
                                        <button type="submit" name="empty_cart" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                            <i class="fa-regular fa-trash-can me-1"></i> Empty Cart
                                        </button>
                                    </form>
                                </td>
                                <td class="text-end border-0 fw-bold fs-4 text-white">Grand Total:</td>
                                <td class="text-end border-0 fw-bold fs-4 text-warning">Rs. <?php echo $grand_total; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="text-end mt-4">
                    <form method="POST" action="cart.php">
                        <button type="submit" name="place_order" class="btn btn-gold">
                            <i class="fa-solid fa-bolt me-2"></i> Confirm & Place Order
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>