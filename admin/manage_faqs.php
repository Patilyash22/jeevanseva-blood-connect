
<?php
require_once '../config.php';
include 'includes/header.php';

// Process status change if requested
if (isset($_GET['action']) && $_GET['action'] == 'toggle_status' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Get current status
    $sql = "SELECT is_active FROM faqs WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $faq = mysqli_fetch_assoc($result);
        $new_status = $faq['is_active'] ? 0 : 1;
        
        $update_sql = "UPDATE faqs SET is_active = $new_status WHERE id = $id";
        if (mysqli_query($conn, $update_sql)) {
            $message = $new_status ? "FAQ activated successfully" : "FAQ deactivated successfully";
            setMessage($message);
        } else {
            setMessage("Failed to update FAQ status", "danger");
        }
    }
    
    redirect("manage_faqs.php");
}

// Process deletion if requested
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $sql = "DELETE FROM faqs WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        setMessage("FAQ deleted successfully");
    } else {
        setMessage("Failed to delete FAQ", "danger");
    }
    redirect("manage_faqs.php");
}

// Handle form submission for new FAQ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_faq'])) {
    $question = sanitize($_POST['question']);
    $answer = sanitize($_POST['answer']);
    
    // Get the highest display order
    $order_sql = "SELECT MAX(display_order) AS max_order FROM faqs";
    $order_result = mysqli_query($conn, $order_sql);
    $order_row = mysqli_fetch_assoc($order_result);
    $display_order = $order_row['max_order'] + 1;
    
    $sql = "INSERT INTO faqs (question, answer, display_order) VALUES ('$question', '$answer', $display_order)";
    if (mysqli_query($conn, $sql)) {
        setMessage("FAQ added successfully");
    } else {
        setMessage("Failed to add FAQ: " . mysqli_error($conn), "danger");
    }
    redirect("manage_faqs.php");
}

// Get FAQs ordered by display_order
$sql = "SELECT * FROM faqs ORDER BY display_order ASC";
$result = mysqli_query($conn, $sql);
$faqs = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $faqs[] = $row;
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>FAQ Management</h2>
    </div>
    <div class="card-body">
        <!-- Add New FAQ Form -->
        <form method="POST" class="admin-form" style="margin-bottom: 30px;">
            <h3 style="margin-bottom: 15px;">Add New FAQ</h3>
            
            <div class="form-group">
                <label for="question">Question</label>
                <input type="text" id="question" name="question" required>
            </div>
            
            <div class="form-group">
                <label for="answer">Answer</label>
                <textarea id="answer" name="answer" required></textarea>
            </div>
            
            <div class="form-buttons">
                <button type="submit" name="add_faq" class="btn btn-primary">Add FAQ</button>
            </div>
        </form>
        
        <hr style="margin: 30px 0;">
        
        <!-- FAQ List with Drag and Drop -->
        <h3 style="margin-bottom: 15px;">Manage FAQs</h3>
        <div class="faq-list" id="faq-sortable">
            <?php if (count($faqs) > 0): ?>
                <?php foreach ($faqs as $faq): ?>
                    <div class="faq-item" data-id="<?php echo $faq['id']; ?>">
                        <div class="faq-content">
                            <div class="faq-header">
                                <h4><?php echo $faq['question']; ?></h4>
                                <span class="status-pill <?php echo $faq['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $faq['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </div>
                            <div class="faq-body">
                                <p><?php echo $faq['answer']; ?></p>
                            </div>
                        </div>
                        <div class="faq-actions">
                            <a href="edit_faq.php?id=<?php echo $faq['id']; ?>" class="btn btn-secondary btn-sm" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            
                            <a href="manage_faqs.php?action=toggle_status&id=<?php echo $faq['id']; ?>" 
                               class="btn <?php echo $faq['is_active'] ? 'btn-warning' : 'btn-success'; ?> btn-sm" 
                               title="<?php echo $faq['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                <i class="fas <?php echo $faq['is_active'] ? 'fa-eye-slash' : 'fa-eye'; ?>"></i> 
                                <?php echo $faq['is_active'] ? 'Deactivate' : 'Activate'; ?>
                            </a>
                            
                            <a href="manage_faqs.php?action=delete&id=<?php echo $faq['id']; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this FAQ?')"
                               title="Delete">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-data">No FAQs found. Add your first FAQ above.</p>
            <?php endif; ?>
        </div>
        
        <p class="help-text" style="margin-top: 15px;">
            <i class="fas fa-info-circle"></i> Drag and drop items to reorder FAQs. Changes will be saved automatically.
        </p>
    </div>
</div>

<style>
.faq-list {
    margin-top: 20px;
}

.faq-item {
    background-color: white;
    border: 1px solid var(--admin-border);
    border-radius: 4px;
    margin-bottom: 10px;
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.faq-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.faq-header h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.faq-body p {
    margin: 10px 0 0;
    color: var(--admin-gray);
}

.faq-actions {
    display: flex;
    gap: 8px;
    margin-top: 10px;
}

.faq-item.ui-sortable-helper {
    box-shadow: 0 5px 10px rgba(0,0,0,0.1);
    border: 1px dashed var(--admin-primary);
}

.faq-item.ui-sortable-placeholder {
    visibility: visible !important;
    background-color: rgba(229, 62, 62, 0.1);
    border: 1px dashed var(--admin-primary);
    height: 80px;
}

.help-text {
    color: var(--admin-gray);
    font-size: 14px;
}

.help-text i {
    margin-right: 5px;
}

.no-data {
    text-align: center;
    color: var(--admin-gray);
    padding: 30px 0;
}

@media (min-width: 768px) {
    .faq-item {
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
    }
    
    .faq-content {
        flex: 1;
    }
    
    .faq-actions {
        margin-top: 0;
    }
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize sortable
    $("#faq-sortable").sortable({
        handle: ".faq-header",
        placeholder: "faq-item ui-sortable-placeholder",
        update: function(event, ui) {
            let order = [];
            $(".faq-item").each(function(index) {
                order.push({
                    id: $(this).data("id"),
                    position: index + 1
                });
            });
            
            // Save the new order via AJAX
            $.ajax({
                url: "update_faq_order.php",
                method: "POST",
                data: { order: JSON.stringify(order) },
                success: function(response) {
                    // Show notification
                    const notification = $('<div class="alert alert-success">FAQ order updated successfully</div>');
                    $(".card-body").prepend(notification);
                    
                    // Auto-dismiss notification
                    setTimeout(function() {
                        notification.fadeOut(500, function() {
                            $(this).remove();
                        });
                    }, 3000);
                },
                error: function() {
                    // Show error notification
                    const notification = $('<div class="alert alert-danger">Failed to update FAQ order</div>');
                    $(".card-body").prepend(notification);
                    
                    // Auto-dismiss notification
                    setTimeout(function() {
                        notification.fadeOut(500, function() {
                            $(this).remove();
                        });
                    }, 3000);
                }
            });
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
