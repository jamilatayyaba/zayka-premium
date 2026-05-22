<?php
// Start session at the very top
session_start();

// Localhost hata kar yeh live details daalni hain:
$conn = new mysqli("sql306.infinityfree.com", "if0_41991462", "2lJ40TB1abYZ", "if0_41991462_restaurant_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart action
if (isset($_POST['add_to_cart'])) {
    $item_id = intval($_POST['item_id']);
    $item_name = $_POST['item_name'];
    $item_price = intval($_POST['item_price']);

    if (isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$item_id] = [
            'name' => $item_name,
            'price' => $item_price,
            'quantity' => 1
        ];
    }
    header("Location: home.php");
    exit();
}

// Fetch menu items
$query = "SELECT * FROM menu";
$result = $conn->query($query);

// Calculate total items in cart for badge count
$total_cart_items = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_cart_items += $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zayka Premium | Italian Fine Dining</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #0f1210;
            color: #f4f1ea;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-luxury {
            background-color: #070908 !important;
            border-bottom: 2px solid #c5a059;
            padding: 15px 0;
        }
        
        .navbar-brand-gold {
            color: #c5a059 !important;
            font-weight: 700;
            letter-spacing: 2px;
            font-size: 24px;
        }
        
        .hero-section {
            background: linear-gradient(rgba(7, 9, 8, 0.85), rgba(15, 18, 16, 1)), 
                        url('https://images.unsplash.com/photo-1544025162-d76694265947?w=500') center/cover;
            padding: 80px 0 60px 0;
            text-align: center;
            border-bottom: 1px solid rgba(197, 160, 89, 0.15);
        }
        
        .hero-title {
            color: #c5a059;
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 15px;
        }
        
        .food-card-visual {
            height: 220px;
            position: relative;
            background-position: center !important;
            background-size: cover !important;
            background-repeat: no-repeat !important;
            border-bottom: 1px solid rgba(197, 160, 89, 0.15);
        }
        
        .menu-card {
            background-color: #131815 !important;
            border: 1px solid rgba(255, 255, 255, 0.03) !important;
            border-radius: 14px !important;
            overflow: hidden;
            transition: transform 0.3s ease, border-color 0.3s ease;
        }
        
        .menu-card:hover {
            transform: translateY(-6px);
            border-color: rgba(197, 160, 89, 0.6) !important;
        }
        
        .category-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: rgba(7, 9, 8, 0.85);
            color: #c5a059;
            font-size: 11px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 30px;
            border: 1px solid rgba(197, 160, 89, 0.3);
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        .dish-price {
            color: #c5a059;
            font-size: 22px;
            font-weight: 700;
        }
        
        .btn-gold {
            background-color: #c5a059;
            color: #0f1210;
            font-weight: 600;
            border: none;
            padding: 8px 18px;
            border-radius: 30px;
        }
        
        .btn-gold:hover {
            background-color: #dbb368;
        }

        .nav-link-premium {
            color: #f4f1ea !important;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        
        .nav-link-premium:hover {
            color: #c5a059 !important;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-luxury sticky-top">
        <div class="container">
            <a class="navbar-brand navbar-brand-gold" href="home.php">
                <i class="fa-solid fa-pizza-slice me-2"></i> ZAYKA PREMIUM
            </a>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item"><a class="nav-link active me-3" href="home.php">Ristorante Menu</a></li>
                    
                    <?php if (isset($_SESSION['username'])): ?>
                        <li class="nav-item text-secondary me-2 small">
                            Welcome, <strong class="text-warning"><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger small me-3 fw-bold" href="logout.php">Logout</a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link me-3 position-relative nav-link-premium" href="cart.php">
                            <i class="fa-solid fa-cart-shopping me-1 text-warning"></i> Cart
                            <?php if ($total_cart_items > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                                    <?php echo $total_cart_items; ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="btn btn-outline-warning btn-sm px-3" href="admin.php">
                            <i class="fa-solid fa-user-tie me-1"></i> Admin Panel
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="container">
            <h1 class="hero-title">Zayka Ristorante Italiano</h1>
            <div class="badge bg-dark text-warning border border-warning p-2 px-3">
                <i class="fa-solid fa-list-check me-2"></i> Live Database Sync Active
            </div>
        </div>
    </header>

    <main class="container my-5">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php 
            if ($result && $result->num_rows > 0) {
                while($item = $result->fetch_assoc()) {
                    
                    // Direct Fetch from Database Column (Bulletproof Method)
                    // Note: If your column name in database is different (like image_url), change 'image' to that name.
                    $image_src = !empty($item['image']) ? $item['image'] : 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=500&q=80';
            ?>
            <div class="col">
                <div class="card h-100 menu-card">
                    <div class="food-card-visual" style="background: linear-gradient(rgba(0,0,0,0.1), rgba(0,0,0,0.7)), url('<?php echo htmlspecialchars($image_src); ?>');">
                        <span class="category-badge"><?php echo htmlspecialchars($item['category']); ?></span>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title fw-bold text-white mb-2"><?php echo htmlspecialchars($item['name']); ?></h5>
                            <p class="card-text text-secondary small mb-0"><?php echo htmlspecialchars($item['description']); ?></p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <span class="dish-price">Rs. <?php echo htmlspecialchars($item['price']); ?></span>
                            <form method="POST" action="home.php">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($item['name']); ?>">
                                <input type="hidden" name="item_price" value="<?php echo $item['price']; ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-gold btn-sm px-3 rounded-pill">
                                    <i class="fa-solid fa-cart-plus me-1"></i> Add To Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                } 
            } else {
                echo "<p class='text-center text-muted w-100'>No premium entries found in database.</p>";
            }
            ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>