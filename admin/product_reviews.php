<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Functions for review management
function createReview($conn, $review, $rating, $product_link, $photo_path, $user_id) {
    $sql = "INSERT INTO product_reviews (user_id, review, rating, product_link, photo, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $user_id, $review, $rating, $product_link, $photo_path);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>New review created successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: Unable to create review.</div>";
    }

    $stmt->close();
}

function editReview($conn, $id, $review, $rating, $product_link, $photo_path, $user_id) {
    if ($_SESSION['role'] !== 'admin') {
        echo "<div class='alert alert-danger'>Access denied. Admins only.</div>";
        return;
    }

    $sql = "UPDATE product_reviews SET review=?, rating=?, product_link=?, photo=?, user_id=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssii", $review, $rating, $product_link, $photo_path, $user_id, $id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Review updated successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: Unable to update review.</div>";
    }

    $stmt->close();
}

function deleteReview($conn, $id) {
    if ($_SESSION['role'] !== 'admin') {
        echo "<div class='alert alert-danger'>Access denied. Admins only.</div>";
        return;
    }

    $sql = "DELETE FROM product_reviews WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Review deleted successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: Unable to delete review.</div>";
    }

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];

    if (isset($_POST['create'])) {
        $review = $_POST['review'];
        $rating = $_POST['rating'];
        $product_link = $_POST['product_link'];
        $photo = $_FILES['photo'];

        // Handle file upload
        if ($photo['error'] == 0) {
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($photo["name"]);
            if (move_uploaded_file($photo["tmp_name"], $target_file)) {
                $photo_path = $target_file;
            } else {
                echo "<div class='alert alert-danger'>Failed to upload the photo.</div>";
                $photo_path = null;
            }
        } else {
            $photo_path = null;
        }

        createReview($conn, $review, $rating, $product_link, $photo_path, $user_id);
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $review = $_POST['review'];
        $rating = $_POST['rating'];
        $product_link = $_POST['product_link'];
        $photo = $_FILES['photo'];

        // Handle file upload
        if ($photo['error'] == 0) {
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($photo["name"]);
            if (move_uploaded_file($photo["tmp_name"], $target_file)) {
                $photo_path = $target_file;
            } else {
                echo "<div class='alert alert-danger'>Failed to upload the photo.</div>";
                $photo_path = null;
            }
        } else {
            $photo_path = null;
        }

        editReview($conn, $id, $review, $rating, $product_link, $photo_path, $user_id);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['delete_id'];

        deleteReview($conn, $id);
    }
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
        <h1>Product Reviews</h1>
        <!-- Button to trigger Create Review Modal -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createReviewModal">
            Create Review
        </button>

        <!-- Reviews Table -->
        <div class="card mt-4">
            <div class="card-header">Reviews</div>
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
                            <th>Actions</th>
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
                                        <td>";
                                if ($_SESSION['role'] === 'admin') {
                                    echo "<button type='button' class='btn btn-warning btn-sm' data-toggle='modal' data-target='#editReviewModal' data-id='{$row['id']}' data-review='{$row['review']}' data-rating='{$row['rating']}' data-product_link='{$row['product_link']}' data-photo='{$row['photo']}'>Edit</button>
                                          <button type='button' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#deleteReviewModal' data-id='{$row['id']}'>Delete</button>";
                                }
                                echo "</td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>No reviews found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Review Modal -->
        <div class="modal fade" id="createReviewModal" tabindex="-1" aria-labelledby="createReviewModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createReviewModalLabel">Create New Review</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="review">Review</label>
                                <textarea class="form-control" id="review" name="review" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="rating">Rating</label>
                                <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" required>
                            </div>
                            <div class="form-group">
                                <label for="product_link">Product Link</label>
                                <input type="url" class="form-control" id="product_link" name="product_link" required>
                            </div>
                            <div class="form-group">
                                <label for="photo">Product Photo</label>
                                <input type="file" class="form-control-file" id="photo" name="photo">
                            </div>
                            <button type="submit" name="create" class="btn btn-primary">Create Review</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Review Modal -->
        <div class="modal fade" id="editReviewModal" tabindex="-1" aria-labelledby="editReviewModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editReviewModalLabel">Edit Review</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="id">Review ID</label>
                                <input type="number" class="form-control" id="id" name="id" required>
                            </div>
                            <div class="form-group">
                                <label for="review">Review</label>
                                <textarea class="form-control" id="review" name="review" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="rating">Rating</label>
                                <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" required>
                            </div>
                            <div class="form-group">
                                <label for="product_link">Product Link</label>
                                <input type="url" class="form-control" id="product_link" name="product_link" required>
                            </div>
                            <div class="form-group">
                                <label for="photo">Product Photo</label>
                                <input type="file" class="form-control-file" id="photo" name="photo">
                            </div>
                            <button type="submit" name="edit" class="btn btn-primary">Edit Review</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Review Modal -->
        <div class="modal fade" id="deleteReviewModal" tabindex="-1" aria-labelledby="deleteReviewModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteReviewModalLabel">Delete Review</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="delete_id">Review ID</label>
                                <input type="number" class="form-control" id="delete_id" name="delete_id" required>
                            </div>
                            <button type="submit" name="delete" class="btn btn-danger">Delete Review</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Include Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
            $('#editReviewModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var review = button.data('review');
                var rating = button.data('rating');
                var product_link = button.data('product_link');
                var photo = button.data('photo');

                var modal = $(this);
                modal.find('.modal-body #id').val(id);
                modal.find('.modal-body #review').val(review);
                modal.find('.modal-body #rating').val(rating);
                modal.find('.modal-body #product_link').val(product_link);
                modal.find('.modal-body #photo').val(photo);
            });

            $('#deleteReviewModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');

                var modal = $(this);
                modal.find('.modal-body #delete_id').val(id);
            });
        </script>
    </div>
</body>
</html>

<?php require_once '../includes/footer.php'; ?>
<?php
$conn->close();
?>
