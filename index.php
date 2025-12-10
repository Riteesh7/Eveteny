<?php
/* Main landing page for users to view events and manage cart */
session_start();
$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticketing Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header style="padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem; width: 100%;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <button id="menu-btn" class="btn btn-secondary" onclick="console.log('Inline Click Detected')">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12h18M3 6h18M3 18h18" />
                </svg>
            </button>
            <div class="header-content">
                <h1>Eventeny</h1>
                <p class="subtitle">Connect with unforgettable experiences</p>
            </div>
        </div>
        
        <div style="display: flex; gap: 1rem; align-items: center;">
            <button id="open-cart-btn" class="btn btn-outline-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                <span id="cart-count" class="cart-badge"><?php echo $cart_count; ?></span>
            </button>
            <div class="auth-buttons">
                <button id="login-btn" class="btn btn-secondary">Login</button>
                <button id="signup-btn" class="btn btn-primary">Sign Up</button>
            </div>
        </div>
    </header>

    <div class="container">

        <!-- Sidebar Navigation -->
        <div id="sidebar-overlay"></div>
        <nav id="sidebar-menu">
            <div class="sidebar-header">
                <h2>Menu</h2>
                <button id="close-menu-btn" class="close-modal">&times;</button>
            </div>
            <ul class="sidebar-links">
                <li><a href="#">Browse Events</a></li>
                <li><a href="admin.php">Organizer Dashboard</a></li>
                <li><a href="#">Venues</a></li>
                <li><a href="#">Help Center</a></li>
            </ul>
        </nav>

        <div id="public-ticket-grid" class="ticket-grid">
        </div>
    </div>

    <!-- Cart Modal -->
    <div id="cart-modal" class="modal">
        <div class="modal-content">
            <button class="close-modal">&times;</button>
            
            <div id="cart-view">
                <h2 style="margin-bottom: 1.5rem;">Your Cart</h2>
                <div id="cart-items-container">
                </div>
                <div id="cart-empty-msg" style="display: none; text-align: center; padding: 2rem;">
                    Your cart is empty.
                </div>
                
                <div class="cart-total">
                    Total: <span id="cart-total-amount">$0.00</span>
                </div>
                
                <div class="cart-actions">
                    <button class="btn btn-secondary close-modal">Continue Shopping</button>
                    <button id="checkout-btn" class="btn btn-primary">Proceed to Review</button>
                </div>
            </div>

            <div id="checkout-view" style="display: none;">
                <h2 style="margin-bottom: 1.5rem;">Review Order</h2>
                <p>Please review your order details before confirming.</p>
                
                <div class="card" style="margin: 1.5rem 0; padding: 1rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: bold;">
                        <span>Total Amount</span>
                        <span id="review-total">$0.00</span>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" placeholder="Enter your email for receipt" required>
                </div>

                <div class="cart-actions">
                    <button id="back-to-cart-btn" class="btn btn-secondary">Back to Cart</button>
                    <button id="confirm-purchase-btn" class="btn btn-primary">Confirm Purchase</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    <div id="login-modal" class="modal">
        <div class="modal-content">
            <button class="close-modal">&times;</button>
            <h2 style="margin-bottom: 1.5rem; text-align: center;">Welcome Back</h2>
            <form id="login-form">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>
            <p style="text-align: center; margin-top: 1rem;">
                Don't have an account? <a href="#" id="switch-to-signup" style="color: var(--primary-color);">Sign Up</a>
            </p>
        </div>
    </div>

    <!-- Signup Modal -->
    <div id="signup-modal" class="modal">
        <div class="modal-content">
            <button class="close-modal">&times;</button>
            <h2 style="margin-bottom: 1.5rem; text-align: center;">Create Account</h2>
            <form id="signup-form">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Sign Up</button>
            </form>
            <p style="text-align: center; margin-top: 1rem;">
                Already have an account? <a href="#" id="switch-to-login" style="color: var(--primary-color);">Login</a>
            </p>
        </div>
    </div>

    <script src="assets/js/script.js?v=<?php echo time(); ?>"></script>
</body>
</html>
