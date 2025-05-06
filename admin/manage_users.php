
<?php
require_once '../config.php';
include 'includes/header.php';

// Process deletion if requested
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Don't allow deletion of current user
    if ($id == $_SESSION['admin_id']) {
        setMessage("You cannot delete your own account", "danger");
    } else {
        $sql = "DELETE FROM users WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            setMessage("User deleted successfully");
        } else {
            setMessage("Failed to delete user", "danger");
        }
    }
    redirect("manage_users.php");
}

// Process admin status toggle if requested
if (isset($_GET['action']) && $_GET['action'] == 'toggle_admin' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Don't allow changing admin status of current user
    if ($id == $_SESSION['admin_id']) {
        setMessage("You cannot change your own admin status", "danger");
    } else {
        // Get current status
        $sql = "SELECT is_admin FROM users WHERE id = $id";
        $result = mysqli_query($conn, $sql);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            $new_status = $user['is_admin'] ? 0 : 1;
            
            $update_sql = "UPDATE users SET is_admin = $new_status WHERE id = $id";
            if (mysqli_query($conn, $update_sql)) {
                $message = $new_status ? "User promoted to admin successfully" : "Admin privileges removed successfully";
                setMessage($message);
            } else {
                setMessage("Failed to update user status", "danger");
            }
        }
    }
    redirect("manage_users.php");
}

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Get total records for pagination
$total_records_sql = "SELECT COUNT(*) AS total FROM users";
$total_result = mysqli_query($conn, $total_records_sql);
$total_records = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get users for current page
$sql = "SELECT * FROM users ORDER BY created_at DESC LIMIT $offset, $records_per_page";
$result = mysqli_query($conn, $sql);
$users = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>User Management</h2>
        <a href="add_user.php" class="btn btn-primary">Add New User</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td>
                                    <span class="status-pill <?php echo $user['is_admin'] ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $user['is_admin'] ? 'Administrator' : 'User'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td class="actions">
                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-secondary btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <a href="manage_users.php?action=toggle_admin&id=<?php echo $user['id']; ?>" 
                                       class="btn <?php echo $user['is_admin'] ? 'btn-warning' : 'btn-success'; ?> btn-sm"
                                       <?php if ($user['id'] == $_SESSION['admin_id']): ?>
                                       onclick="return false;" style="opacity: 0.5; cursor: not-allowed;"
                                       <?php endif; ?>
                                       title="<?php echo $user['is_admin'] ? 'Remove Admin' : 'Make Admin'; ?>">
                                        <i class="fas <?php echo $user['is_admin'] ? 'fa-user-minus' : 'fa-user-plus'; ?>"></i>
                                    </a>
                                    
                                    <a href="manage_users.php?action=delete&id=<?php echo $user['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       <?php if ($user['id'] == $_SESSION['admin_id']): ?>
                                       onclick="return false;" style="opacity: 0.5; cursor: not-allowed;"
                                       <?php else: ?>
                                       onclick="return confirm('Are you sure you want to delete this user?')"
                                       <?php endif; ?>
                                       title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No users found</td>
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
