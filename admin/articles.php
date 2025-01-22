<?php
include_once '../includes/config.php';
require_once '../includes/header.php';

$sql = "SELECT articles.id, articles.title, articles.content, users.username AS author, articles.created_at 
        FROM articles 
        JOIN users ON articles.author_id = users.id";
$result = $conn->query($sql);

if (isset($_POST['create_article'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author_id = $_POST['author_id'];

    $stmt = $conn->prepare("INSERT INTO articles (title, content, author_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $content, $author_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Article created successfully</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

if (isset($_POST['edit_article'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author_id = $_POST['author_id'];

    $stmt = $conn->prepare("UPDATE articles SET title=?, content=?, author_id=? WHERE id=?");
    $stmt->bind_param("ssii", $title, $content, $author_id, $id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Article updated successfully</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

if (isset($_POST['delete_article'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM articles WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Article deleted successfully</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Articles</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Manage Articles</h2>
        <!-- Button to trigger the add article modal -->
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addArticleModal">Add Article</button>

        <!-- Articles List -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Author</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['title']}</td>
                                <td>{$row['content']}</td>
                                <td>{$row['author']}</td>
                                <td>{$row['created_at']}</td>
                                <td>
                                    <button class='btn btn-warning btn-sm' data-toggle='modal' data-target='#editArticleModal' data-id='{$row['id']}' data-title='{$row['title']}' data-content='{$row['content']}' data-author='{$row['author']}'>Edit</button>
                                    <button class='btn btn-danger btn-sm' data-toggle='modal' data-target='#deleteArticleModal' data-id='{$row['id']}'>Delete</button>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No articles found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Add Article Modal -->
    <div class="modal fade" id="addArticleModal" tabindex="-1" aria-labelledby="addArticleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addArticleModalLabel">Add Article</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="content">Content</label>
                            <textarea class="form-control" id="content" name="content" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="author_id">Author ID</label>
                            <input type="number" class="form-control" id="author_id" name="author_id" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="create_article" class="btn btn-primary">Create</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Article Modal -->
    <div class="modal fade" id="editArticleModal" tabindex="-1" aria-labelledby="editArticleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editArticleModalLabel">Edit Article</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="form-group">
                            <label for="edit_title">Title</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_content">Content</label>
                            <textarea class="form-control" id="edit_content" name="content" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_author_id">Author ID</label>
                            <input type="number" class="form-control" id="edit_author_id" name="author_id" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="edit_article" class="btn btn-warning">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Article Modal -->
    <div class="modal fade" id="deleteArticleModal" tabindex="-1" aria-labelledby="deleteArticleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteArticleModalLabel">Delete Article</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="delete_id" name="id">
                        <p>Are you sure you want to delete this article?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="delete_article" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Pass data to the edit modal
        $('#editArticleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var title = button.data('title');
            var content = button.data('content');
            var author = button.data('author');

            var modal = $(this);
            modal.find('#edit_id').val(id);
            modal.find('#edit_title').val(title);
            modal.find('#edit_content').val(content);
            modal.find('#edit_author_id').val(author);
        });

        // Pass data to the delete modal
        $('#deleteArticleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');

            var modal = $(this);
            modal.find('#delete_id').val(id);
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php require_once '../includes/footer.php'; ?>