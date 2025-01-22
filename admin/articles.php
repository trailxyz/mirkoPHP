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

// Functions for article management
function createArticle($conn, $title, $content, $author_id) {
    $sql = "INSERT INTO articles (title, content, author_id, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $content, $author_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>New article created successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: Unable to create article.</div>";
    }

    $stmt->close();
}

function editArticle($conn, $id, $title, $content, $author_id) {
    if ($_SESSION['role'] !== 'admin') {
        echo "<div class='alert alert-danger'>Access denied. Admins only.</div>";
        return;
    }

    $sql = "UPDATE articles SET title=?, content=?, author_id=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $title, $content, $author_id, $id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Article updated successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: Unable to update article.</div>";
    }

    $stmt->close();
}

function deleteArticle($conn, $id) {
    if ($_SESSION['role'] !== 'admin') {
        echo "<div class='alert alert-danger'>Access denied. Admins only.</div>";
        return;
    }

    $sql = "DELETE FROM articles WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Article deleted successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: Unable to delete article.</div>";
    }

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $author_id = $_SESSION['user_id'];

    if (isset($_POST['create'])) {
        $title = $_POST['title'];
        $content = $_POST['content'];

        createArticle($conn, $title, $content, $author_id);
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $content = $_POST['content'];

        editArticle($conn, $id, $title, $content, $author_id);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['delete_id'];

        deleteArticle($conn, $id);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Article Management</h1>
        <!-- Button to trigger Create Article Modal -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createArticleModal">
            Create Article
        </button>

        <!-- Articles Table -->
        <div class="card mt-4">
            <div class="card-header">Articles</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Author ID</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT id, title, content, author_id, created_at FROM articles";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['title']}</td>
                                        <td>{$row['content']}</td>
                                        <td>{$row['author_id']}</td>
                                        <td>{$row['created_at']}</td>
                                        <td>";
                                if ($_SESSION['role'] === 'admin') {
                                    echo "<button type='button' class='btn btn-warning btn-sm' data-toggle='modal' data-target='#editArticleModal' data-id='{$row['id']}' data-title='{$row['title']}' data-content='{$row['content']}'>Edit</button>
                                          <button type='button' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#deleteArticleModal' data-id='{$row['id']}'>Delete</button>";
                                }
                                echo "</td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No articles found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Article Modal -->
        <div class="modal fade" id="createArticleModal" tabindex="-1" aria-labelledby="createArticleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createArticleModalLabel">Create New Article</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="form-group">
                                <label for="content">Content</label>
                                <textarea class="form-control" id="content" name="content" required></textarea>
                            </div>
                            <button type="submit" name="create" class="btn btn-primary">Create Article</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Article Modal -->
        <div class="modal fade" id="editArticleModal" tabindex="-1" aria-labelledby="editArticleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editArticleModalLabel">Edit Article</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="id">Article ID</label>
                                <input type="number" class="form-control" id="id" name="id" required>
                            </div>
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="form-group">
                                <label for="content">Content</label>
                                <textarea class="form-control" id="content" name="content" required></textarea>
                            </div>
                            <button type="submit" name="edit" class="btn btn-primary">Edit Article</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Article Modal -->
        <div class="modal fade" id="deleteArticleModal" tabindex="-1" aria-labelledby="deleteArticleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteArticleModalLabel">Delete Article</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="delete_id">Article ID</label>
                                <input type="number" class="form-control" id="delete_id" name="delete_id" required>
                            </div>
                            <button type="submit" name="delete" class="btn btn-danger">Delete Article</button>
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
            $('#editArticleModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var title = button.data('title');
                var content = button.data('content');

                var modal = $(this);
                modal.find('.modal-body #id').val(id);
                modal.find('.modal-body #title').val(title);
                modal.find('.modal-body #content').val(content);
            });

            $('#deleteArticleModal').on('show.bs.modal', function (event) {
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