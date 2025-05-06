
<?php
require_once '../config.php';
include 'includes/header.php';

// Process approval/disapproval if requested
if (isset($_GET['action']) && $_GET['action'] == 'toggle_approval' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Get current approval status
    $sql = "SELECT is_approved FROM testimonials WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $testimonial = mysqli_fetch_assoc($result);
        $new_status = $testimonial['is_approved'] ? 0 : 1;
        
        $update_sql = "UPDATE testimonials SET is_approved = $new_status WHERE id = $id";
        if (mysqli_query($conn, $update_sql)) {
            $message = $new_status ? "Testimonial approved successfully" : "Testimonial unapproved successfully";
            setMessage($message);
        } else {
            setMessage("Failed to update testimonial status", "danger");
        }
    }
    
    redirect("manage_testimonials.php");
}

// Process deletion if requested
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $sql = "DELETE FROM testimonials WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        setMessage("Testimonial deleted successfully");
    } else {
        setMessage("Failed to delete testimonial", "danger");
    }
    redirect("manage_testimonials.php");
}

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total records for pagination
$total_records_sql = "SELECT COUNT(*) AS total FROM testimonials";
$total_result = mysqli_query($conn, $total_records_sql);
$total_records = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get testimonials for current page
$sql = "SELECT * FROM testimonials ORDER BY created_at DESC LIMIT $offset, $records_per_page";
$result = mysqli_query($conn, $sql);
$testimonials = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $testimonials[] = $row;
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>Testimonials Management</h2>
        <a href="add_testimonial.php" class="btn btn-primary">Add New Testimonial</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Quote</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($testimonials) > 0): ?>
                        <?php foreach ($testimonials as $testimonial): ?>
                            <tr>
                                <td><?php echo $testimonial['id']; ?></td>
                                <td><?php echo $testimonial['name']; ?></td>
                                <td><?php echo $testimonial['role']; ?></td>
                                <td><?php 
                                    // Truncate long quotes
                                    echo strlen($testimonial['quote']) > 100 
                                        ? substr($testimonial['quote'], 0, 100) . '...' 
                                        : $testimonial['quote']; 
                                ?></td>
                                <td>
                                    <span class="status-pill <?php echo $testimonial['is_approved'] ? 'status-active' : 'status-pending'; ?>">
                                        <?php echo $testimonial['is_approved'] ? 'Approved' : 'Pending'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($testimonial['created_at'])); ?></td>
                                <td class="actions">
                                    <a href="edit_testimonial.php?id=<?php echo $testimonial['id']; ?>" class="btn btn-secondary btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <a href="manage_testimonials.php?action=toggle_approval&id=<?php echo $testimonial['id']; ?>" 
                                       class="btn <?php echo $testimonial['is_approved'] ? 'btn-warning' : 'btn-success'; ?> btn-sm" 
                                       title="<?php echo $testimonial['is_approved'] ? 'Unapprove' : 'Approve'; ?>">
                                        <i class="fas <?php echo $testimonial['is_approved'] ? 'fa-times-circle' : 'fa-check-circle'; ?>"></i>
                                    </a>
                                    
                                    <a href="manage_testimonials.php?action=delete&id=<?php echo $testimonial['id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Are you sure you want to delete this testimonial?')"
                                       title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No testimonials found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li><a href="?page=1">First</a></li>
                    <li><a href="?page=<?php echo $page - 1; ?>">Prev</a></li>
                <?php endif; ?>
                
                <?php
                // Determine the range of page numbers to show
                $range = 2; // Show 2 pages before and after current page
                $start_page = max(1, $page - $range);
                $end_page = min($total_pages, $page + $range);
                
                for ($i = $start_page; $i <= $end_page; $i++):
                ?>
                    <li class="<?php echo $i == $page ? 'active' : ''; ?>">
                        <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <li><a href="?page=<?php echo $page + 1; ?>">Next</a></li>
                    <li><a href="?page=<?php echo $total_pages; ?>">Last</a></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
