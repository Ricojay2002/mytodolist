<?php include 'db.php'; ?>

<?php
// ADD TASK
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task']) && !isset($_POST['edit_id'])) {
    $task = $conn->real_escape_string($_POST['task']);  // Sanitize input

    if (!empty($task)) {
        $insertQuery = "INSERT INTO tasks (task) VALUES ('$task')";
        if ($conn->query($insertQuery) === FALSE) {
            die("Error inserting task: " . $conn->error);
        }
    }
}

// DELETE TASK
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM tasks WHERE id=$id");
    header("Location: index.php");
    exit();
}

// EDIT TASK
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id'])) {
    $id = (int)$_POST['edit_id'];
    $task = $conn->real_escape_string($_POST['task']);
    $conn->query("UPDATE tasks SET task='$task' WHERE id=$id");
    header("Location: index.php");
    exit();
}

// Fetch tasks from DB
$tasks = $conn->query("SELECT * FROM tasks ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>To-Do List App</title>
    <link rel="stylesheet" href="style.css">
    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="container">
    <h2><i class="fas fa-list-check"></i> My To-Do List</h2>

    <!-- Add or Edit Form -->
    <form method="POST" action="">
        <input type="text" name="task" placeholder="Enter a task..." required value="<?php echo isset($_GET['edit']) ? htmlspecialchars($conn->query("SELECT task FROM tasks WHERE id=".(int)$_GET['edit'])->fetch_assoc()['task']) : ''; ?>">
        <?php if (isset($_GET['edit'])): ?>
            <input type="hidden" name="edit_id" value="<?php echo $_GET['edit']; ?>">
            <button type="submit"><i class="fas fa-save"></i> Update</button>
            <a href="index.php" class="cancel-btn"><i class="fas fa-times"></i> Cancel</a>
        <?php else: ?>
            <button type="submit"><i class="fas fa-plus"></i> Add</button>
        <?php endif; ?>
    </form>

    <!-- Task List -->
    <ul>
        <?php if ($tasks && $tasks->num_rows > 0): ?>
            <?php while ($row = $tasks->fetch_assoc()): ?>
                <li>
                    <span><?php echo htmlspecialchars($row['task']); ?></span>
                    <div class="actions">
                        <a href="?edit=<?php echo $row['id']; ?>" class="edit" title="Edit"><i class="fas fa-pen"></i></a>
                        <a href="?delete=<?php echo $row['id']; ?>" class="delete" title="Delete" onclick="return confirm('Delete this task?');"><i class="fas fa-trash-alt"></i></a>
                    </div>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li><span>No tasks yet. Add one above!</span></li>
        <?php endif; ?>
    </ul>
</div>
</body>
</html>
