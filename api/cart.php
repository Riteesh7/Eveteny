<?php
/* Handles shopping cart operations including adding, removing, and viewing items */
session_start();
require '../db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    if (empty($_SESSION['cart'])) {
        echo json_encode([]);
        exit;
    }

    $ids = implode(',', array_keys($_SESSION['cart']));
    $sql = "SELECT * FROM tickets WHERE id IN ($ids)";
    $result = $conn->query($sql);
    
    $cartItems = [];
    $total = 0;
    
    while ($row = $result->fetch_assoc()) {
        $qty = $_SESSION['cart'][$row['id']];
        $row['cart_quantity'] = $qty;
        $row['subtotal'] = $row['price'] * $qty;
        $total += $row['subtotal'];
        $cartItems[] = $row;
    }
    
    echo json_encode(['items' => $cartItems, 'total' => $total]);

} elseif ($method === 'POST') {
    $action = $input['action'] ?? '';
    
    if ($action === 'add') {
        $ticketId = $input['ticket_id'];
        $quantity = $input['quantity'];
        
        if (isset($_SESSION['cart'][$ticketId])) {
            $_SESSION['cart'][$ticketId] += $quantity;
        } else {
            $_SESSION['cart'][$ticketId] = $quantity;
        }
        echo json_encode(['status' => 'success', 'cart_count' => array_sum($_SESSION['cart'])]);
        
    } elseif ($action === 'remove') {
        $ticketId = $input['ticket_id'];
        unset($_SESSION['cart'][$ticketId]);
        echo json_encode(['status' => 'success', 'cart_count' => array_sum($_SESSION['cart'])]);
        
    } elseif ($action === 'update') {
        $ticketId = $input['ticket_id'];
        $quantity = $input['quantity'];
        if ($quantity > 0) {
            $_SESSION['cart'][$ticketId] = $quantity;
        } else {
            unset($_SESSION['cart'][$ticketId]);
        }
        echo json_encode(['status' => 'success', 'cart_count' => array_sum($_SESSION['cart'])]);
        
    } elseif ($action === 'clear') {
        $_SESSION['cart'] = [];
        echo json_encode(['status' => 'success']);
    }
}
?>
