<?php
/* API endpoint for managing tickets (CRUD operations) */
require '../db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

function getJsonInput() {
    return json_decode(file_get_contents('php://input'), true);
}

switch ($method) {
    case 'GET':
        $sql = "SELECT * FROM tickets";
        if (isset($_GET['public_only']) && $_GET['public_only'] == '1') {
            $sql .= " WHERE is_public = 1 AND sale_start <= NOW() AND sale_end >= NOW()";
        }
        $sql .= " ORDER BY created_at DESC";
        
        $result = $conn->query($sql);
        $tickets = [];
        while ($row = $result->fetch_assoc()) {
            $tickets[] = $row;
        }
        echo json_encode($tickets);
        break;

    case 'POST':
        $id = $_POST['id'] ?? '';

        if (!empty($id)) {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $quantity = $_POST['quantity'];
            $sale_start = $_POST['sale_start'];
            $sale_end = $_POST['sale_end'];
            $is_public = $_POST['is_public'];
            
            $sql = "UPDATE tickets SET title=?, description=?, price=?, quantity=?, sale_start=?, sale_end=?, is_public=?";
            $params = [$title, $description, $price, $quantity, $sale_start, $sale_end, $is_public];
            $types = "ssdissi";

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $uploadDir = '../uploads/';
                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $targetFile = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $sql .= ", image_path=?";
                    $params[] = 'uploads/' . $fileName;
                    $types .= "s";
                }
            }
            
            $sql .= " WHERE id=?";
            $params[] = $id;
            $types .= "i";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Ticket updated']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => $stmt->error]);
            }

        } else {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;
            $quantity = $_POST['quantity'] ?? 0;
            $sale_start = $_POST['sale_start'] ?? '';
            $sale_end = $_POST['sale_end'] ?? '';
            $is_public = $_POST['is_public'] ?? 1;
            
            $image_path = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $uploadDir = '../uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $fileName = time() . '_' . basename($_FILES['image']['name']);
                $targetFile = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $image_path = 'uploads/' . $fileName;
                }
            }

            $stmt = $conn->prepare("INSERT INTO tickets (title, description, price, quantity, sale_start, sale_end, is_public, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdissis", $title, $description, $price, $quantity, $sale_start, $sale_end, $is_public, $image_path);
            
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Ticket created']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => $stmt->error]);
            }
        }
        break;

    case 'PUT':
        $data = getJsonInput();
        if (!$data) break;
        
        $id = $data['id'];
        $title = $data['title'];
        $description = $data['description'];
        $price = $data['price'];
        $quantity = $data['quantity'];
        $sale_start = $data['sale_start'];
        $sale_end = $data['sale_end'];
        $is_public = $data['is_public'];

        $stmt = $conn->prepare("UPDATE tickets SET title=?, description=?, price=?, quantity=?, sale_start=?, sale_end=?, is_public=? WHERE id=?");
        $stmt->bind_param("ssdissii", $title, $description, $price, $quantity, $sale_start, $sale_end, $is_public, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Ticket updated']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
        break;

    case 'DELETE':
        $data = getJsonInput();
        $id = $data['id'];
        
        $stmt = $conn->prepare("DELETE FROM tickets WHERE id=?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Ticket deleted']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }
        break;
}
?>
