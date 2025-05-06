
<?php
require_once '../config.php';
include 'includes/header.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    setMessage("FAQ ID is required", "danger");
    redirect("manage_faqs.php");
}

$id = (int)$_GET['id'];

// Get FAQ data
$sql = "SELECT * FROM faqs WHERE id = $id";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    setMessage("FAQ not found", "danger");
    redirect("manage_faqs.php");
}

$faq = mysqli_fetch_assoc($result);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = sanitize($_POST['question']);
    $answer = sanitize($_POST['answer']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $sql = "UPDATE faqs SET question = '$question', answer = '$answer', is_active = $is_active WHERE id = $id";
    
    if (mysqli_query($conn, $sql)) {
        setMessage("FAQ updated successfully");
        redirect("manage_faqs.php");
    } else {
        setMessage("Failed to update FAQ: " . mysqli_error($conn), "danger");
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2>Edit FAQ</h2>
    </div>
    <div class="card-body">
        <form method="POST" class="admin-form">
            <div class="form-group">
                <label for="question">Question</label>
                <input type="text" id="question" name="question" value="<?php echo $faq['question']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="answer">Answer</label>
                <textarea id="answer" name="answer" required><?php echo $faq['answer']; ?></textarea>
            </div>
            
            <div class="form-group checkbox-group">
                <input type="checkbox" id="is_active" name="is_active" <?php echo $faq['is_active'] ? 'checked' : ''; ?>>
                <label for="is_active">Active</label>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">Update FAQ</button>
                <a href="manage_faqs.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
