
<?php
require_once '../config.php';
include 'includes/header.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $role = sanitize($_POST['role']);
    $quote = sanitize($_POST['quote']);
    $is_approved = isset($_POST['is_approved']) ? 1 : 0;
    
    // Handle file upload
    $avatar = "";
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
    
    // If no errors, insert into database
    if (empty($errors)) {
        $sql = "INSERT INTO testimonials (name, role, quote, avatar, is_approved) VALUES ('$name', '$role', '$quote', '$avatar', $is_approved)";
        
        if (mysqli_query($conn, $sql)) {
            setMessage("Testimonial added successfully");
            redirect("manage_testimonials.php");
        } else {
            $errors[] = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>Add New Testimonial</h2>
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
                        <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="role">Role <span class="required">*</span></label>
                        <input type="text" id="role" name="role" placeholder="e.g. Regular Donor, Blood Recipient" value="<?php echo isset($_POST['role']) ? $_POST['role'] : ''; ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="quote">Quote/Testimonial <span class="required">*</span></label>
                <textarea id="quote" name="quote" rows="4" required><?php echo isset($_POST['quote']) ? $_POST['quote'] : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="avatar">Avatar/Photo (optional)</label>
                <input type="file" id="avatar" name="avatar" accept="image/*">
                <small class="form-text text-muted">Max file size: 2MB. Allowed formats: JPG, JPEG, PNG, GIF.</small>
            </div>
            
            <div class="form-group checkbox-group">
                <input type="checkbox" id="is_approved" name="is_approved" <?php echo isset($_POST['is_approved']) ? 'checked' : ''; ?>>
                <label for="is_approved">Approve testimonial immediately</label>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Add Testimonial</button>
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
</style>

<?php include 'includes/footer.php'; ?>
