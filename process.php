<?php
require_once 'db.php';
session_start();

if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    try {
        $stmt = mysqli_prepare($conn, "DELETE FROM bmi_logs WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        $_SESSION['success_msg'] = "Log entry removed successfully.";
    } catch (Exception $e) {
        $_SESSION['error_msgs'] = ["Error deleting log: " . $e->getMessage()];
    }
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = trim($_POST['user_name'] ?? '');
    $weight_kg = floatval($_POST['weight_kg'] ?? 0);
    $height_cm = floatval($_POST['height_cm'] ?? 0);
    $fitness_goal = $_POST['fitness_goal'] ?? 'Weight Loss';
    $log_date = $_POST['log_date'] ?? '';
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;

    $errors = [];
    if (empty($user_name)) $errors[] = "User name is required.";
    if ($weight_kg <= 10 || $weight_kg > 350) $errors[] = "Enter weight between 10kg and 350kg.";
    if ($height_cm <= 50 || $height_cm > 280) $errors[] = "Enter height between 50cm and 280cm.";
    if (empty($log_date)) $errors[] = "Please select a date.";

    if (!empty($errors)) {
        $_SESSION['error_msgs'] = $errors;
        header("Location: index.php" . ($id ? "?edit=$id" : ""));
        exit;
    }

    try {
        if ($id) {
            $stmt = mysqli_prepare($conn, "UPDATE bmi_logs SET user_name=?, weight_kg=?, height_cm=?, fitness_goal=?, log_date=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sddssi", $user_name, $weight_kg, $height_cm, $fitness_goal, $log_date, $id);
            mysqli_stmt_execute($stmt);
            $_SESSION['success_msg'] = "Log entry updated successfully!";
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO bmi_logs (user_name, weight_kg, height_cm, fitness_goal, log_date) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sddss", $user_name, $weight_kg, $height_cm, $fitness_goal, $log_date);
            mysqli_stmt_execute($stmt);
            $_SESSION['success_msg'] = "New entry logged successfully!";
        }
    } catch (Exception $e) {
        $_SESSION['error_msgs'] = ["Database error: " . $e->getMessage()];
    }
    header("Location: index.php");
    exit;
}
?>