<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION["user_id"];

// Fetch teacher's group ID
$group_query = $conn->prepare("SELECT id FROM `groups` WHERE teacher_id = ?");
$group_query->bind_param("i", $teacher_id);
$group_query->execute();
$group_result = $group_query->get_result();
$group = ($group_result->num_rows > 0) ? $group_result->fetch_assoc()['id'] : null;

// Handle note deletion (only if the teacher owns the note)
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Check if the note belongs to this teacher before deleting
    $check_query = $conn->prepare("SELECT file_path FROM notes WHERE id = ? AND teacher_id = ?");
    $check_query->bind_param("ii", $delete_id, $teacher_id);
    $check_query->execute();
    $check_result = $check_query->get_result();

    if ($check_result->num_rows > 0) {
        $row = $check_result->fetch_assoc();
        $file_path = $row['file_path'];

        // Delete file from the server
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Delete note from database
        $delete_query = $conn->prepare("DELETE FROM notes WHERE id = ?");
        $delete_query->bind_param("i", $delete_id);
        $delete_query->execute();
        header("Location: notes_teacher.php?success=Note deleted successfully.");
        exit();
    } else {
        header("Location: notes_teacher.php?error=Unauthorized deletion attempt.");
        exit();
    }
}

// Fetch notes uploaded by the teacher
$query = "SELECT id, title, description, file_path, uploaded_at FROM notes WHERE teacher_id = ? ORDER BY uploaded_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notes</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="dashboard">
        <h2>Manage Notes</h2>

        <?php if (isset($_GET['success'])): ?>
            <p class="success"><?= htmlspecialchars($_GET['success']); ?></p>
        <?php elseif (isset($_GET['error'])): ?>
            <p class="error"><?= htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>

        <form action="upload_notes.php" method="POST" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" name="title" required>

            <label for="description">Description:</label>
            <textarea name="description" rows="3" required></textarea>

            <label for="file">Upload PDF:</label>
            <input type="file" name="file" accept="application/pdf" required>

            <input type="hidden" name="group_id" value="<?= $group ?>">

            <button type="submit">Upload</button>
            
        </form>

        <?php if ($result->num_rows > 0): ?>
            <h3>Uploaded Notes</h3>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Uploaded At</th>
                    <th>Download</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']); ?></td>
                        <td><?= htmlspecialchars($row['description']); ?></td>
                        <td><?= date("d M Y, H:i A", strtotime($row['uploaded_at'])); ?></td>
                        <td><a href="<?= htmlspecialchars($row['file_path']); ?>" target="_blank" download>Download</a></td>
                        <td>
                            <a href="notes_teacher.php?delete_id=<?= $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this note?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="no-notes">No notes have been uploaded yet.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2025 StudyPlanner. All Rights Reserved.</p>
    </footer>
</body>
</html>
