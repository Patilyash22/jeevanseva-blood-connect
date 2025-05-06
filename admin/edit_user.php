
<?php
require_once '../config.php';
include 'includes/header.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    setMessage("User ID is required", "danger");
    redirect("manage_users.php");
}

$id = (int)$_GET['id'];

// Get user data
$sql = "SELECT * FROM users WHERE id = $id";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    setMessage("User not found", "danger");
    redirect("manage_users.php");
}

$user = mysqli_fetch_assoc($result);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    
    // Validate input
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username is required";
    } else {
        // Check if username already exists (excluding current user)
        $check_sql = "SELECT * FROM users WHERE username = '$username' AND id != $id";
        $check_result = mysqli_query($conn, $check_sql);
        if (mysqli_num_rows($check_result) > 0) {
            $errors[] = "Username already exists";
        }
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    } else {
        // Check if email already exists (excluding current user)
        $check_sql = "SELECT * FROM users WHERE email = '$email' AND id != $id";
        $check_result = mysqli_query($conn, $check_sql);
        if (mysqli_num_rows($check_result) > 0) {
            $errors[] = "Email already exists";
        }
    }
    
    // If this is the current user, don't allow changing admin status
    if ($id == $_SESSION['admin_id'] && $is_admin != $user['is_admin']) {
        $errors[] = "You cannot change your own admin status";
        $is_admin = $user['is_admin']; // Reset to original value
    }
    
    // If no errors, update user
    if (empty($errors)) {
        // Start with base SQL for update
        $sql = "UPDATE users SET username = '$username', email = '$email', is_admin = $is_admin";
        
        // Add password update if provided
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", password = '$hashed_password'";
        }
        
        // Complete the SQL
        $sql .= " WHERE id = $id";
        
        if (mysqli_query($conn, $sql)) {
            setMessage("User updated successfully");
            redirect("manage_users.php");
        } else {
            $errors[] = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>Edit User</h2>
    </div>
    <div class="card-body">
        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="admin-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password">
                <small class="form-text text-muted">Leave blank to keep current password.</small>
            </div>
            
            <div class="form-group checkbox-group">
                <input type="checkbox" id="is_admin" name="is_admin" <?php echo $user['is_admin'] ? 'checked' : ''; ?>
                       <?php echo ($id == $_SESSION['admin_id']) ? 'disabled' : ''; ?>>
                <label for="is_admin">Administrator</label>
                <?php if ($id == $_SESSION['admin_id']): ?>
                    <small class="form-text text-muted">You cannot change your own admin status.</small>
                    <input type="hidden" name="is_admin" value="1">
                <?php endif; ?>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
