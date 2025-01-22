<?php
include '../includes/config.php';
include '../includes/functions.php';
require_once '../includes/header.php';

startSession();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $review = $_POST['review'];
    $rating = $_POST['rating'];
    $page_link = $_POST['page_link']; // New field for Page Link

    $sql = "INSERT INTO page_reviews (user_id, review, rating, page_link) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isis", $user_id, $review, $rating, $page_link);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Page Review added successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error adding Page Review.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Reviews</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Page Reviews</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="user_id">User ID</label>
                                <input type="number" class="form-control" name="user_id" required>
                            </div>
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
                                <label for="page_link">Page Link</label>
                                <input type="url" class="form-control" name="page_link" required>
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
            <div class="card-header">Page Reviews</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Review</th>
                            <th>Rating</th>
                            <th>Page Link</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT id, user_id, review, rating, page_link, created_at FROM page_reviews";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['user_id']}</td>
                                        <td>{$row['review']}</td>
                                        <td>{$row['rating']}</td>
                                        <td><a href='{$row['page_link']}' target='_blank'>View Page</a></td>
                                        <td>{$row['created_at']}</td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No reviews found</td></tr>";
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
