
<?php
require_once '../config.php';
include 'includes/header.php';

// Process status change if requested
if (isset($_GET['action']) && $_GET['action'] == 'status' && isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = sanitize($_GET['status']);
    
    if (in_array($status, ['active', 'inactive', 'pending'])) {
        $sql = "UPDATE donors SET status = '$status' WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
            setMessage("Donor status updated successfully");
        } else {
            setMessage("Failed to update donor status", "danger");
        }
    }
    redirect("manage_donors.php");
}

// Process deletion if requested
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $sql = "DELETE FROM donors WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        setMessage("Donor deleted successfully");
    } else {
        setMessage("Failed to delete donor", "danger");
    }
    redirect("manage_donors.php");
}

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Search functionality
$search = '';
$search_condition = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = sanitize($_GET['search']);
    $search_condition = "WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR blood_group LIKE '%$search%' OR location LIKE '%$search%'";
}

// Get total records for pagination
$total_records_sql = "SELECT COUNT(*) AS total FROM donors $search_condition";
$total_result = mysqli_query($conn, $total_records_sql);
$total_records = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get donors for current page
$sql = "SELECT * FROM donors $search_condition ORDER BY created_at DESC LIMIT $offset, $records_per_page";
$result = mysqli_query($conn, $sql);
$donors = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $donors[] = $row;
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>Donor Management</h2>
        <a href="add_donor.php" class="btn btn-primary">Add New Donor</a>
    </div>
    <div class="card-body">
        <!-- Search Form -->
        <form method="GET" class="search-form" style="margin-bottom: 20px; display: flex; gap: 10px;">
            <input 
                type="text" 
                name="search" 
                placeholder="Search donors..." 
                value="<?php echo $search; ?>"
                style="flex: 1; padding: 10px; border: 1px solid var(--admin-border); border-radius: 4px;"
            >
            <button 
                type="submit" 
                class="btn btn-primary"
                style="white-space: nowrap;"
            >
                <i class="fas fa-search"></i> Search
            </button>
            <?php if (!empty($search)): ?>
                <a 
                    href="manage_donors.php" 
                    class="btn btn-secondary"
                    style="white-space: nowrap;"
                >
                    Clear
                </a>
            <?php endif; ?>
        </form>
        
        <!-- Donors Table -->
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Blood Group</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($donors) > 0): ?>
                        <?php foreach ($donors as $donor): ?>
                            <tr>
                                <td><?php echo $donor['id']; ?></td>
                                <td><?php echo $donor['name']; ?></td>
                                <td><?php echo $donor['email']; ?></td>
                                <td><?php echo $donor['phone']; ?></td>
                                <td><?php echo $donor['blood_group']; ?></td>
                                <td><?php echo $donor['location']; ?></td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    switch ($donor['status']) {
                                        case 'active':
                                            $status_class = 'status-active';
                                            break;
                                        case 'inactive':
                                            $status_class = 'status-inactive';
                                            break;
                                        case 'pending':
                                            $status_class = 'status-pending';
                                            break;
                                    }
                                    ?>
                                    <span class="status-pill <?php echo $status_class; ?>">
                                        <?php echo ucfirst($donor['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($donor['created_at'])); ?></td>
                                <td class="actions">
                                    <a href="edit_donor.php?id=<?php echo $donor['id']; ?>" class="btn btn-secondary btn-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <div class="dropdown" style="position: relative; display: inline-block;">
                                        <button class="btn btn-primary btn-sm" onclick="toggleDropdown(<?php echo $donor['id']; ?>)" title="Change Status">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                        <div id="dropdown-<?php echo $donor['id']; ?>" class="dropdown-content" style="display: none; position: absolute; z-index: 10; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.2); border-radius: 4px; min-width: 120px;">
                                            <a href="manage_donors.php?action=status&id=<?php echo $donor['id']; ?>&status=active" class="dropdown-item">Active</a>
                                            <a href="manage_donors.php?action=status&id=<?php echo $donor['id']; ?>&status=inactive" class="dropdown-item">Inactive</a>
                                            <a href="manage_donors.php?action=status&id=<?php echo $donor['id']; ?>&status=pending" class="dropdown-item">Pending</a>
                                        </div>
                                    </div>
                                    
                                    <a href="manage_donors.php?action=delete&id=<?php echo $donor['id']; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Are you sure you want to delete this donor?')"
                                       title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align: center;">No donors found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li>
                        <a href="?page=1<?php echo !empty($search) ? '&search='.$search : ''; ?>">First</a>
                    </li>
                    <li>
                        <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search='.$search : ''; ?>">Prev</a>
                    </li>
                <?php endif; ?>
                
                <?php
                // Determine the range of page numbers to show
                $range = 2; // Show 2 pages before and after current page
                $start_page = max(1, $page - $range);
                $end_page = min($total_pages, $page + $range);
                
                for ($i = $start_page; $i <= $end_page; $i++):
                ?>
                    <li class="<?php echo $i == $page ? 'active' : ''; ?>">
                        <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search='.$search : ''; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <li>
                        <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search='.$search : ''; ?>">Next</a>
                    </li>
                    <li>
                        <a href="?page=<?php echo $total_pages; ?><?php echo !empty($search) ? '&search='.$search : ''; ?>">Last</a>
                    </li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
        
        <!-- Export Button -->
        <div style="margin-top: 20px;">
            <a href="export_donors.php" class="btn btn-success">
                <i class="fas fa-file-export"></i> Export Donors (CSV)
            </a>
        </div>
    </div>
</div>

<style>
.dropdown-content {
    margin-top: 5px;
}

.dropdown-item {
    display: block;
    padding: 8px 10px;
    text-decoration: none;
    color: var(--admin-text);
}

.dropdown-item:hover {
    background-color: #f9fafb;
}

.btn-sm {
    padding: 5px 8px;
    font-size: 12px;
}

.table-responsive {
    overflow-x: auto;
}
</style>

<script>
function toggleDropdown(id) {
    const dropdown = document.getElementById(`dropdown-${id}`);
    
    // Close all other open dropdowns
    const allDropdowns = document.querySelectorAll('.dropdown-content');
    allDropdowns.forEach(d => {
        if (d.id !== `dropdown-${id}`) {
            d.style.display = 'none';
        }
    });
    
    // Toggle the clicked dropdown
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.matches('.btn-sm')) {
        const dropdowns = document.querySelectorAll('.dropdown-content');
        dropdowns.forEach(dropdown => {
            dropdown.style.display = 'none';
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
