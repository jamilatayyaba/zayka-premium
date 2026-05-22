<?php
session_start();

// Security Guardrail: Agar user logged in nahi hai YA uska role 'admin' nahi hai, toh block karein
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("<div style='background:#0f1210; color:#dc3545; text-align:center; padding:50px; font-family:sans-serif;'><h2>⛔ Access Denied!</h2><p style='color:#6c757d;'>Only system administrators can access the Ristorante Control Center.</p><a href='login.php' style='color:#c5a059;'>Go to Login</a></div>");
}

// Database connection
$conn = new mysqli("sql306.infinityfree.com", "if0_41991462", "2lJ40TB1abYZ", "if0_41991462_restaurant_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Order Status Update
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php");
    exit();
}

// Fetch Core Dashboard Statistics
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders")->fetch_assoc()['total'] ?? 0;
$pending_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'Pending'")->fetch_assoc()['count'];

// Fetch All Orders with User Information
$orders_result = $conn->query("SELECT orders.*, users.username FROM orders JOIN users ON orders.user_id = users.id ORDER BY orders.order_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zayka Premium | Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0f1210; color: #f4f1ea; font-family: 'Segoe UI', sans-serif; }
        .navbar-luxury { background-color: #070908 !important; border-bottom: 2px solid #c5a059; padding: 15px 0; }
        .dashboard-card { background-color: #131815; border: 1px solid rgba(197, 160, 89, 0.15); border-radius: 12px; padding: 25px; }
        .table-container { background-color: #131815; border: 1px solid rgba(197, 160, 89, 0.15); border-radius: 14px; padding: 30px; margin-top: 40px; }
        .table-luxury { color: #f4f1ea !important; }
        .table-luxury th { color: #c5a059 !important; border-bottom: 2px solid rgba(197, 160, 89, 0.3) !important; }
        .select-luxury { background-color: #1a201c; color: #f4f1ea; border: 1px solid rgba(197, 160, 89, 0.3); padding: 4px 8px; border-radius: 6px; }
        .btn-gold { background-color: #c5a059; color: #0f1210; font-weight: 600; border: none; }
        .btn-gold:hover { background-color: #dbb368; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-luxury sticky-top">
        <div class="container">
            <a class="navbar-brand text-warning fw-bold" href="home.php"><i class="fa-solid fa-pizza-slice me-2"></i> ZAYKA ADMIN</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link btn btn-outline-warning btn-sm px-3 text-warning" href="home.php">Back To Live Site</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row mb-4">
            <div class="col-12">
                <h2 style="color: #c5a059;" class="fw-bold"><i class="fa-solid fa-gauge-high me-2"></i> Ristorante Control Center</h2>
                <p class="text-secondary">Manage premium guest orders and operations.</p>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="dashboard-card d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-secondary small mb-1">Total Orders Placed</h6>
                        <h2 class="fw-bold mb-0 text-white"><?php echo $total_orders; ?></h2>
                    </div>
                    <div class="text-warning fs-1"><i class="fa-solid fa-utensils"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-secondary small mb-1">Gross Revenue Stream</h6>
                        <h2 class="fw-bold mb-0 text-success">Rs. <?php echo $total_revenue; ?></h2>
                    </div>
                    <div class="text-warning fs-1"><i class="fa-solid fa-wallet"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dashboard-card d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-secondary small mb-1">Active Kitchen Queue</h6>
                        <h2 class="fw-bold mb-0 text-warning"><?php echo $pending_orders; ?></h2>
                    </div>
                    <div class="text-warning fs-1"><i class="fa-solid fa-fire-burner"></i></div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <h4 class="mb-4 text-white"><i class="fa-solid fa-list-check me-2 text-warning"></i> Incoming Order Stream Log</h4>
            <div class="table-responsive">
                <table class="table table-luxury">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer Account</th>
                            <th>Timestamp</th>
                            <th>Total Bill Amount</th>
                            <th>Live System Status</th>
                            <th class="text-end">Operation Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($order = $orders_result->fetch_assoc()): ?>
                        <tr>
                            <td class="fw-bold text-warning">#00<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                            <td class="small text-secondary"><?php echo $order['order_date']; ?></td>
                            <td class="fw-bold">Rs. <?php echo $order['total_amount']; ?></td>
                            <td>
                                <?php if($order['status'] == 'Pending'): ?>
                                    <span class="badge bg-warning text-dark">Cooking</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Served</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <form method="POST" action="admin.php" class="d-inline-flex gap-2">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" class="select-luxury small">
                                        <option value="Pending" <?php if($order['status']=='Pending') echo 'selected'; ?>>Pending</option>
                                        <option value="Completed" <?php if($order['status']=='Completed') echo 'selected'; ?>>Completed</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-gold btn-sm rounded px-3">Update</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>