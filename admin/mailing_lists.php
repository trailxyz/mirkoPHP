<?php
include '../includes/config.php';
include '../includes/functions.php';
require_once '../includes/header.php';

startSession(); // Start the session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        $email = $_POST['email'];

        // Add a new mailing list
        $sql = "INSERT INTO mailing_lists (email) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Mailing List added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error adding Mailing List.</div>";
        }
    } elseif (isset($_POST['edit'])) {
        if ($_SESSION['role'] !== 'admin') {
            echo "<div class='alert alert-danger'>Access denied. Admins only.</div>";
            return;
        }

        $id = $_POST['id'];
        $email = $_POST['email'];

        // Edit an existing mailing list
        $sql = "UPDATE mailing_lists SET email=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $email, $id);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Mailing List updated successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error updating Mailing List.</div>";
        }
    } elseif (isset($_POST['delete'])) {
        if ($_SESSION['role'] !== 'admin') {
            echo "<div class='alert alert-danger'>Access denied. Admins only.</div>";
            return;
        }

        $id = $_POST['delete_id'];

        // Delete a mailing list
        $sql = "DELETE FROM mailing_lists WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Mailing List deleted successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error deleting Mailing List.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mailing List</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Mailing List</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <button type="submit" name="create" class="btn btn-primary btn-block">Add to Mailing List</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mailing List Table -->
        <div class="card mt-4">
            <div class="card-header">Mailing List</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT id, email FROM mailing_lists";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['email']}</td>
                                        <td>";
                                if ($_SESSION['role'] === 'admin') {
                                    echo "<button type='button' class='btn btn-warning btn-sm' data-toggle='modal' data-target='#editMailingListModal' data-id='{$row['id']}' data-email='{$row['email']}'>Edit</button>
                                          <button type='button' class='btn btn-danger btn-sm' data-toggle='modal' data-target='#deleteMailingListModal' data-id='{$row['id']}'>Delete</button>";
                                }
                                echo "</td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>No mailing lists found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Mailing List Modal -->
        <div class="modal fade" id="editMailingListModal" tabindex="-1" aria-labelledby="editMailingListModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editMailingListModalLabel">Edit Mailing List</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="id">Mailing List ID</label>
                                <input type="number" class="form-control" id="id" name="id" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <button type="submit" name="edit" class="btn btn-primary">Edit Mailing List</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Mailing List Modal -->
        <div class="modal fade" id="deleteMailingListModal" tabindex="-1" aria-labelledby="deleteMailingListModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteMailingListModalLabel">Delete Mailing List</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="delete_id">Mailing List ID</label>
                                <input type="number" class="form-control" id="delete_id" name="delete_id" required>
                            </div>
                            <button type="submit" name="delete" class="btn btn-danger">Delete Mailing List</button>
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
            $('#editMailingListModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var email = button.data('email');

                var modal = $(this);
                modal.find('.modal-body #id').val(id);
                modal.find('.modal-body #email').val(email);
            });

            $('#deleteMailingListModal').on('show.bs.modal', function (event) {
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
