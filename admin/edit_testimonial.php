
<?php
require_once '../config.php';
include 'includes/header.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    setMessage("Testimonial ID is required", "danger");
    redirect("manage_testimonials.php");
}

$id = (int)$_GET['id'];

// Get testimonial data
$sql = "SELECT * FROM testimonials WHERE id = $id";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    setMessage("Testimonial not found", "danger");
    redirect("manage_testimonials.php");
}

$testimonial = mysqli_fetch_assoc($result);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $role = sanitize($_POST['role']);
    $quote = sanitize($_POST['quote']);
    $is_approved = isset($_POST['is_approved']) ? 1 : 0;
    
    // Handle file upload
    $avatar = $testimonial['avatar']; // Keep existing avatar by default
    if (isset($_FILES['avatar']) && $_FILES['avatar']['size'] > 0) {
        $target_dir = "../uploads/testimonials/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Check if image file is actual image
        $check = getimagesize($_FILES['avatar']['tmp_name']);
        if ($check !== false) {
            // Check file size (max 2MB)
            if ($_FILES['avatar']['size'] < 2000000) {
                // Allow certain file formats
                if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
                        // Delete old avatar if exists
                        if (!empty($testimonial['avatar']) && file_exists($testimonial['avatar'])) {
                            unlink($testimonial['avatar']);
                        }
                        $avatar = $target_file;
                    } else {
                        $errors[] = "Sorry, there was an error uploading your file.";
                    }
                } else {
                    $errors[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                }
            } else {
                $errors[] = "Sorry, your file is too large. Max size is 2MB.";
            }
        } else {
            $errors[] = "File is not an image.";
        }
    }
    
    // Check if remove avatar is checked
    if (isset($_POST['remove_avatar']) && $_POST['remove_avatar'] == 1) {
        // Delete existing avatar if exists
        if (!empty($testimonial['avatar']) && file_exists($testimonial['avatar'])) {
            unlink($testimonial['avatar']);
        }
        $avatar = "";
    }
    
    // Validate input
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($role)) {
        $errors[] = "Role is required";
    }
    
    if (empty($quote)) {
        $errors[] = "Quote is required";
    }
    
    // If no errors, update database
    if (empty($errors)) {
        $sql = "UPDATE testimonials SET 
                name = '$name', 
                role = '$role', 
                quote = '$quote', 
                avatar = '$avatar', 
                is_approved = $is_approved 
                WHERE id = $id";
        
        if (mysqli_query($conn, $sql)) {
            setMessage("Testimonial updated successfully");
            redirect("manage_testimonials.php");
        } else {
            $errors[] = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>Edit Testimonial</h2>
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
        
        <form method="POST" class="admin-form" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" value="<?php echo $testimonial['name']; ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="role">Role <span class="required">*</span></label>
                        <input type="text" id="role" name="role" placeholder="e.g. Regular Donor, Blood Recipient" value="<?php echo $testimonial['role']; ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="quote">Quote/Testimonial <span class="required">*</span></label>
                <textarea id="quote" name="quote" rows="4" required><?php echo $testimonial['quote']; ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Avatar/Photo</label>
                <?php if (!empty($testimonial['avatar']) && file_exists($testimonial['avatar'])): ?>
                    <div class="current-avatar">
                        <img src="<?php echo $testimonial['avatar']; ?>" alt="<?php echo $testimonial['name']; ?>" style="max-width: 100px; max-height: 100px; margin: 10px 0;">
                        <div class="checkbox-group">
                            <input type="checkbox" id="remove_avatar" name="remove_avatar" value="1">
                            <label for="remove_avatar">Remove current avatar</label>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No avatar currently set</p>
                <?php endif; ?>
                
                <label for="avatar">Upload new avatar (optional)</label>
                <input type="file" id="avatar" name="avatar" accept="image/*">
                <small class="form-text text-muted">Max file size: 2MB. Allowed formats: JPG, JPEG, PNG, GIF.</small>
            </div>
            
            <div class="form-group checkbox-group">
                <input type="checkbox" id="is_approved" name="is_approved" <?php echo $testimonial['is_approved'] ? 'checked' : ''; ?>>
                <label for="is_approved">Approve testimonial</label>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Update Testimonial</button>
                <a href="manage_testimonials.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
.row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -15px;
    margin-left: -15px;
}

.col-md-6 {
    width: 50%;
    padding-right: 15px;
    padding-left: 15px;
    box-sizing: border-box;
}

@media (max-width: 768px) {
    .col-md-6 {
        width: 100%;
    }
}

.required {
    color: red;
}

.current-avatar {
    margin-bottom: 15px;
}
</style>

<?php include 'includes/footer.php'; ?>
