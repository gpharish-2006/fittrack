<?php
require_once 'db.php';
session_start();

$is_edit = false;
$edit_id = '';
$user_name = '';
$weight_kg = '';
$height_cm = '';
$fitness_goal = 'Weight Loss';
$log_date = date('Y-m-d');

if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $is_edit = true;
    try {
        $stmt = mysqli_prepare($conn, "SELECT * FROM bmi_logs WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $edit_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($res)) {
            $user_name = $row['user_name'];
            $weight_kg = $row['weight_kg'];
            $height_cm = $row['height_cm'];
            $fitness_goal = $row['fitness_goal'];
            $log_date = $row['log_date'];
        }
    } catch (Exception $e) {
        $_SESSION['error_msgs'] = ["Error loading data: " . $e->getMessage()];
    }
}

try {
    $result = mysqli_query($conn, "SELECT * FROM bmi_logs ORDER BY log_date DESC");
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

require_once 'header.php';
?>

<main class="container flex-grow-1">
    <?php if (isset($_SESSION['success_msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            ✓ <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_msgs'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php foreach ($_SESSION['error_msgs'] as $err) echo "<p class='mb-0'>• $err</p>"; unset($_SESSION['error_msgs']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        
        <div class="col-lg-4">
            
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><?php echo $is_edit ? "Edit BMI Entry" : "BMI Calculator & Log"; ?></h5>
                </div>
                <div class="card-body">
                    <form action="process.php" method="POST">
                        <?php if ($is_edit): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">User Name</label>
                            <input type="text" name="user_name" class="form-control" value="<?php echo htmlspecialchars($user_name); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Weight (kg)</label>
                            <input type="number" step="0.01" name="weight_kg" class="form-control" placeholder="e.g. 70" value="<?php echo htmlspecialchars($weight_kg); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Height (cm)</label>
                            <input type="number" step="0.01" name="height_cm" class="form-control" placeholder="e.g. 175" value="<?php echo htmlspecialchars($height_cm); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Fitness Goal</label>
                            <select name="fitness_goal" class="form-select">
                                <option value="Weight Loss" <?php echo $fitness_goal == 'Weight Loss' ? 'selected' : ''; ?>>Weight Loss</option>
                                <option value="Maintenance" <?php echo $fitness_goal == 'Maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                                <option value="Muscle Gain" <?php echo $fitness_goal == 'Muscle Gain' ? 'selected' : ''; ?>>Muscle Gain</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Log Date</label>
                            <input type="date" name="log_date" class="form-control" value="<?php echo htmlspecialchars($log_date); ?>" required>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1"><?php echo $is_edit ? "Save Changes" : "Calculate & Save"; ?></button>
                            <?php if ($is_edit): ?>
                                <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="card-title mb-0 fw-bold text-dark">BMI Classifications</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light border-start border-warning border-4">
                            <span class="small fw-semibold">Underweight</span>
                            <span class="badge bg-warning text-dark">&lt; 18.5</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light border-start border-success border-4">
                            <span class="small fw-semibold">Normal Weight</span>
                            <span class="badge bg-success text-white">18.5 – 24.9</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light border-start border-warning border-4">
                            <span class="small fw-semibold">Overweight</span>
                            <span class="badge bg-warning text-dark">25.0 – 29.9</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light border-start border-danger border-4">
                            <span class="small fw-semibold">Obese</span>
                            <span class="badge bg-danger text-white">≥ 30.0</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold">Log History Database</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Height & Weight</th>
                                    <th>BMI Index</th>
                                    <th>Date</th>
                                    <th class="text-end px-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): 
                                        $w = $row['weight_kg'];
                                        $h_m = $row['height_cm'] / 100;
                                        $bmi = ($h_m > 0) ? round($w / ($h_m * $h_m), 1) : 0;
                                        
                                        if ($bmi < 18.5) { $badge = "bg-warning text-dark"; }
                                        elseif ($bmi < 25) { $badge = "bg-success text-white"; }
                                        elseif ($bmi < 30) { $badge = "bg-warning text-dark"; }
                                        else { $badge = "bg-danger text-white"; }
                                    ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($row['user_name']); ?></strong></td>
                                            <td><?php echo "{$w}kg / {$row['height_cm']}cm"; ?></td>
                                            <td>
                                                <span class="badge <?php echo $badge; ?>"><?php echo $bmi; ?> BMI</span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($row['log_date'])); ?></td>
                                            <td class="text-end px-4">
                                                <a href="index.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-secondary py-0 px-2 me-1">Edit</a>
                                                <a href="process.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="return confirm('Delete this log?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">No active records. Log weight and height above!</td>
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

<?php require_once 'footer.php'; ?>