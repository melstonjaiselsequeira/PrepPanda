<?php
session_start();
include 'includes/db_connect.php'; // Ensure DB connection is included

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    echo json_encode(["status" => "unauthorized", "message" => "You must be logged in as a student to join a group."]);
    exit();
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_code = trim($_POST['group_code'] ?? '');
    $student_id = $_SESSION['user_id'];

    // Validate group code
    if (empty($group_code)) {
        echo json_encode(["status" => "error", "message" => "Group code cannot be empty."]);
        exit();
    }

    // Check if the group exists
    $stmt = $conn->prepare("SELECT id FROM `groups` WHERE group_code = ?");
    $stmt->bind_param("s", $group_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $group_id = $row['id'];
        
        // Check if the student is already in a group
        $check_stmt = $conn->prepare("SELECT group_id FROM users WHERE id = ?");
        $check_stmt->bind_param("i", $student_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $student_data = $check_result->fetch_assoc();

        if ($student_data['group_id'] == $group_id) {
            echo json_encode(["status" => "error", "message" => "You are already in this group."]);
            exit();
        }

        // Assign the student to the group
        $update_stmt = $conn->prepare("UPDATE users SET group_id = ? WHERE id = ?");
        $update_stmt->bind_param("ii", $group_id, $student_id);
        
        if ($update_stmt->execute()) {
            $_SESSION['group_id'] = $group_id; // ✅ Update session
            echo json_encode(["status" => "success", "message" => "Successfully joined the group!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database error. Please try again."]);
        }
        $update_stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid group code."]);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>