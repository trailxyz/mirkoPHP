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
function createReview($conn, $review, $rating, $page_link, $user_id) {
    $sql = "INSERT INTO page_reviews (user_id, review, rating, page_link, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $review, $rating, $page_link);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>New review created successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: Unable to create review.</div>";
    }

    $stmt->close();
}

function editReview($conn, $id, $review, $rating, $page_link, $user_id) {
    if ($_SESSION['role'] !== 'admin') {
        echo "<div class='alert alert-danger'>Access denied. Admins only.</div>";
        return;
    }

    $sql = "UPDATE page_reviews SET review=?, rating=?, page_link=?, user_id=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $review, $rating, $page_link, $user_id, $id);

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

    $sql = "DELETE FROM page_reviews WHERE id=?";
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
        $page_link = $_POST['page_link'];

        createReview($conn, $review, $rating, $page_link, $user_id);
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $review = $_POST['review'];
        $rating = $_POST['rating'];
        $page_link = $_POST['page_link'];

        editReview($conn, $id, $review, $rating, $page_link, $user_id);
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
    <title>Page Reviews</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Page Reviews</h1>
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
                            <th>Page Link</th>
                            <th>Created At</th>
                            <th>Actions</th>
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
                                        <td>";
                                if ($_SESSION['role'] === 'admin') {
                                    echo "<button type='button' class='btn btn-warning btn-sm' data-toggle='modal' data-target='#editReviewModal' data-id='{$row['id']}' data-review='{$row['review']}' data-rating='{$row['rating']}' data-page_link='{$row['page_link']}'>Edit</button>
                                          <button type='button' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#deleteReviewModal' data-id='{$row['id']}'>Delete</button>";
                                }
                                echo "</td>
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
                        <form method="POST">
                            <div class="form-group">
                                <label for="review">Review</label>
                                <textarea class="form-control" id="review" name="review" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="rating">Rating</label>
                                <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" required>
                            </div>
                            <div class="form-group">
                                <label for="page_link">Page Link</label>
                                <input type="url" class="form-control" id="page_link" name="page_link" required>
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
                        <form method="POST">
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
                                <label for="page_link">Page Link</label>
                                <input type="url" class="form-control" id="page_link" name="page_link" required>
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

        <!-- Include Bootstrap JS and dependencies -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
            $('#editReviewModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var review = button.data('review');
                var rating = button.data('rating');
                var page_link = button.data('page_link');

                var modal = $(this);
                modal.find('.modal-body #id').val(id);
                modal.find('.modal-body #review').val(review);
                modal.find('.modal-body #rating').val(rating);
                modal.find('.modal-body #page_link').val(page_link);
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
