<?php
require_once 'actions.php';

try {
    $result = mysqli_query($conn, "SELECT * FROM bmi_logs ORDER BY log_date DESC");
} catch (Exception $e) {
    die("Failed to fetch historical progress: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitTrack</title>
    <link href="styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="bg-light">

    <header class="bg-dark text-white py-4 mb-4 shadow">
        <div class="container">
            <h1 class="h2">FitTrack Engine</h1>
            <p class="mb-0 text-secondary">Mini Dashboard for BMI Tracking & Metric Analysis</p>
        </div>
    </header>

    <main class="container">
        <div class="row g-4">
            
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h2 class="h5 card-title mb-0"><?php echo $is_edit ? "Edit Metric Log" : "Log Metrics"; ?></h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger p-2 small">
                                <?php foreach ($errors as $error) echo "<p class='mb-0'>• $error</p>"; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success_msg): ?>
                            <div class="alert alert-success p-2 small"><p class="mb-0">✓ <?php echo $success_msg; ?></p></div>
                        <?php endif; ?>

                        <form action="index.php" method="POST">
                            <?php if ($is_edit): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="user_name" class="form-label font-weight-bold small">User Name</label>
                                <input type="text" id="user_name" name="user_name" class="form-control" value="<?php echo htmlspecialchars($user_name); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="weight_kg" class="form-label small">Weight (kg)</label>
                                <input type="number" step="0.01" id="weight_kg" name="weight_kg" class="form-control" placeholder="e.g. 74.5" value="<?php echo htmlspecialchars($weight_kg); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="height_cm" class="form-label small">Height (cm)</label>
                                <input type="number" step="0.01" id="height_cm" name="height_cm" class="form-control" placeholder="e.g. 178" value="<?php echo htmlspecialchars($height_cm); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="fitness_goal" class="form-label small">Goal Track</label>
                                <select id="fitness_goal" name="fitness_goal" class="form-select">
                                    <option value="Weight Loss" <?php if($fitness_goal == 'Weight Loss') echo 'selected'; ?>>Weight Loss</option>
                                    <option value="Maintenance" <?php if($fitness_goal == 'Maintenance') echo 'selected'; ?>>Maintenance</option>
                                    <option value="Muscle Gain" <?php if($fitness_goal == 'Muscle Gain') echo 'selected'; ?>>Muscle Gain</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="log_date" class="form-label small">Log Date</label>
                                <input type="date" id="log_date" name="log_date" class="form-control" value="<?php echo htmlspecialchars($log_date); ?>" required>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1"><?php echo $is_edit ? "Save Changes" : "Record Entry"; ?></button>
                                <?php if ($is_edit): ?>
                                    <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h2 class="h5 card-title mb-0">Progress Logs</h2>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Metrics</th>
                                        <th>Calculated BMI</th>
                                        <th>Goal Target</th>
                                        <th>Logged On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($result) > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($result)): 
                                            $w = $row['weight_kg'];
                                            $h_m = $row['height_cm'] / 100;
                                            $bmi = ($h_m > 0) ? round($w / ($h_m * $h_m), 1) : 0;
                                            
                                            if ($bmi < 18.5) { $lbl = "Underweight"; $badge = "bg-warning text-dark"; }
                                            elseif ($bmi < 25) { $lbl = "Normal"; $badge = "bg-success text-white"; }
                                            elseif ($bmi < 30) { $lbl = "Overweight"; $badge = "bg-warning text-dark"; }
                                            else { $lbl = "Obese"; $badge = "bg-danger text-white"; }
                                        ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($row['user_name']); ?></strong></td>
                                                <td><?php echo "{$w}kg / {$row['height_cm']}cm"; ?></td>
                                                <td>
                                                    <span class="fw-bold me-1"><?php echo $bmi; ?></span> 
                                                    <span class="badge <?php echo $badge; ?>"><?php echo $lbl; ?></span>
                                                </td>
                                                <td><span class="badge bg-info text-dark"><?php echo $row['fitness_goal']; ?></span></td>
                                                <td><?php echo $row['log_date']; ?></td>
                                                <td>
                                                    <a href="index.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-link text-decoration-none p-0 me-2">Edit</a>
                                                    <a href="index.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-link text-decoration-none text-danger p-0" onclick="return confirm('Remove entry?');">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No tracked metrics found yet. Start logging above!</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>