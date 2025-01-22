<?php
session_start();
require_once '../includes/functions.php';
require_once '../includes/config.php';
require_once '../includes/header.php';
startSession();

if (!isUserLoggedIn()) {
    handleError("You must be logged in to submit a review.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $review = $_POST['review'];
    $rating = $_POST['rating'];
    $product_link = $_POST['product_link']; // New field for Product Link
    $photo = $_FILES['photo']; // New field for Photo upload
    
    if (empty($review) || empty($rating)) {
        handleError("Review and rating cannot be empty.");
    }

    // Handle file upload
    if ($photo['error'] == 0) {
        $target_dir = "../ASSets/";
        $target_file = $target_dir . basename($photo["name"]);
        if (move_uploaded_file($photo["tmp_name"], $target_file)) {
            $photo_path = $target_file;
        } else {
            handleError("Failed to upload the photo.");
        }
    }

    $sql = "INSERT INTO product_reviews (user_id, review, rating, product_link, photo, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        handleError("Failed to prepare the SQL statement.");
    }
    $stmt->bind_param("issss", $user_id, $review, $rating, $product_link, $photo_path);
    if (!$stmt->execute()) {
        handleError("Failed to execute the SQL statement.");
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Reviews</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Product Reviews</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="review">Review</label>
                                <textarea class="form-control" name="review" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="rating">Rating</label>
                                <select class="form-control" name="rating">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="product_link">Product Link</label>
                                <input type="url" class="form-control" name="product_link" required>
                            </div>
                            <div class="form-group">
                                <label for="photo">Product Photo</label>
                                <input type="file" class="form-control-file" name="photo">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Add Review</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="dashboard.php">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Table -->
        <div class="card mt-4">
            <div class="card-header">Product Reviews</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Review</th>
                            <th>Rating</th>
                            <th>Product Link</th>
                            <th>Product Photo</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT id, user_id, review, rating, product_link, photo, created_at FROM product_reviews";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['user_id']}</td>
                                        <td>{$row['review']}</td>
                                        <td>{$row['rating']}</td>
                                        <td><a href='{$row['product_link']}' target='_blank'>View Product</a></td>
                                        <td><img src='{$row['photo']}' alt='Product Photo' width='100'></td>
                                        <td>{$row['created_at']}</td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No reviews found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php require_once '../includes/footer.php'; ?>
<?php
$conn->close();
?>
