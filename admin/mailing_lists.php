<?php
include '../includes/config.php';
include '../includes/functions.php';
require_once '../includes/header.php';

startSession(); // Start the session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
                            <button type="submit" class="btn btn-primary btn-block">Add to Mailing List</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="dashboard.php">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mailing List Table -->
        <div class="card mt-4">
            <div class="card-header">Current Mailing List</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT id, email, created_at FROM mailing_lists";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['created_at']}</td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>No entries found</td></tr>";
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
// Close the connection after all operations
$conn->close();
?>
