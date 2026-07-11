<?php
require_once 'db.php';

$errors = [];
$success_msg = "";

$is_edit = false;
$edit_id = '';
$user_name = '';
$weight_kg = '';
$height_cm = '';
$fitness_goal = 'Weight Loss';
$log_date = date('Y-m-d');

if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    try {
        $stmt = mysqli_prepare($conn, "DELETE FROM bmi_logs WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        header("Location: index.php?success=Log entry safely removed!");
        exit;
    } catch (Exception $e) {
        $errors[] = "Error deleting log: " . $e->getMessage();
    }
}

if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $is_edit = true;
    try {
        $stmt = mysqli_prepare($conn, "SELECT * FROM bmi_logs WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $edit_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $user_name = $row['user_name'];
            $weight_kg = $row['weight_kg'];
            $height_cm = $row['height_cm'];
            $fitness_goal = $row['fitness_goal'];
            $log_date = $row['log_date'];
        }
    } catch (Exception $e) {
        $errors[] = "Error loading record data: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = trim($_POST['user_name'] ?? '');
    $weight_kg = floatval($_POST['weight_kg'] ?? 0);
    $height_cm = floatval($_POST['height_cm'] ?? 0);
    $fitness_goal = $_POST['fitness_goal'] ?? 'Weight Loss';
    $log_date = $_POST['log_date'] ?? '';
    $edit_id = isset($_POST['id']) ? intval($_POST['id']) : null;

    if (empty($user_name)) $errors[] = "User name field cannot be empty.";
    if ($weight_kg <= 10 || $weight_kg > 350) $errors[] = "Weight must be between 10 kg and 350 kg.";
    if ($height_cm <= 50 || $height_cm > 280) $errors[] = "Height must be between 50 cm and 280 cm.";
    if (empty($log_date)) $errors[] = "Please select a tracking log date.";

    if (empty($errors)) {
        try {
            if ($edit_id) {                
                $stmt = mysqli_prepare($conn, "UPDATE bmi_logs SET user_name=?, weight_kg=?, height_cm=?, fitness_goal=?, log_date=? WHERE id=?");
                mysqli_stmt_bind_param($stmt, "sddssi", $user_name, $weight_kg, $height_cm, $fitness_goal, $log_date, $edit_id);
                mysqli_stmt_execute($stmt);
                header("Location: index.php?success=Fitness log updated successfully!");
                exit;
            } else {                
                $stmt = mysqli_prepare($conn, "INSERT INTO bmi_logs (user_name, weight_kg, height_cm, fitness_goal, log_date) VALUES (?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "sddss", $user_name, $weight_kg, $height_cm, $fitness_goal, $log_date);
                mysqli_stmt_execute($stmt);
                header("Location: index.php?success=New metric log saved successfully!");
                exit;
            }
        } catch (Exception $e) {
            $errors[] = "Database operation failure: " . $e->getMessage();
        }
    }
}

if (isset($_GET['success'])) {
    $success_msg = htmlspecialchars($_GET['success']);
}
?>