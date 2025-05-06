
<?php
require_once '../config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if it's an AJAX request with POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order'])) {
    $order = json_decode($_POST['order'], true);
    
    if (is_array($order)) {
        $success = true;
        
        // Begin transaction
        mysqli_begin_transaction($conn);
        
        try {
            foreach ($order as $item) {
                $id = (int)$item['id'];
                $position = (int)$item['position'];
                
                $sql = "UPDATE faqs SET display_order = $position WHERE id = $id";
                if (!mysqli_query($conn, $sql)) {
                    throw new Exception("Error updating FAQ order: " . mysqli_error($conn));
                }
            }
            
            // Commit transaction
            mysqli_commit($conn);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'FAQ order updated successfully']);
            
        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($conn);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid data format']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
