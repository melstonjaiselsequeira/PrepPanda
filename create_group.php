<?php
session_start();
require __DIR__ . '/includes/db_connect.php';
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'teacher') { header('Location: login.php'); exit; }
$teacherId = (int)$_SESSION['user_id'];
$stmt = $conn->prepare('SELECT id FROM `groups` WHERE teacher_id = ? LIMIT 1');
$stmt->bind_param('i', $teacherId); $stmt->execute();
if ($stmt->get_result()->fetch_assoc()) { header('Location: dashboard_teacher.php'); exit; }
$name = (string)($_SESSION['name'] ?? 'Teacher'); $groupName = $name . "'s Group"; $code = strtoupper(bin2hex(random_bytes(3)));
$insert = $conn->prepare('INSERT INTO `groups` (teacher_id, group_code, group_name) VALUES (?, ?, ?)');
$insert->bind_param('iss', $teacherId, $code, $groupName); $insert->execute();
header('Location: dashboard_teacher.php'); exit;
