<?php
require_once 'db.php';
session_start();

try {
    $raw_logs = mysqli_query($conn, "SELECT * FROM bmi_logs ORDER BY user_name ASC, log_date ASC");
} catch (Exception $e) {
    die("Tracker failure: " . $e->getMessage());
}

$user_trends = [];
while ($row = mysqli_fetch_assoc($raw_logs)) {
    $user = $row['user_name'];
    if (!isset($user_trends[$user])) {
        $user_trends[$user] = [];
    }
    $user_trends[$user][] = $row;
}

$total_users = count($user_trends);
$total_logs = 0;
$weight_loss_goals = 0;
$muscle_gain_goals = 0;

foreach ($user_trends as $name => $logs) {
    $total_logs += count($logs);
    $last_log = end($logs);
    if ($last_log['fitness_goal'] == 'Weight Loss') $weight_loss_goals++;
    if ($last_log['fitness_goal'] == 'Muscle Gain') $muscle_gain_goals++;
}

require_once 'header.php';
?>

<main class="container flex-grow-1">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h3 fw-bold text-dark">Live Fitness Tracker & Trends</h2>
            <p class="text-muted">Calculates weight progress, trend changes, and goals logged over time.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white p-3">
                <span class="small opacity-75">Active Users Tracked</span>
                <h3 class="fw-bold m-0"><?php echo $total_users; ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white p-3">
                <span class="small opacity-75">Total Progress Logs</span>
                <h3 class="fw-bold m-0"><?php echo $total_logs; ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-dark p-3">
                <span class="small opacity-75">Users Targeting Weight Loss</span>
                <h3 class="fw-bold m-0"><?php echo $weight_loss_goals; ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-info text-dark p-3">
                <span class="small opacity-75">Users Targeting Muscle Gain</span>
                <h3 class="fw-bold m-0"><?php echo $muscle_gain_goals; ?></h3>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <?php if (!empty($user_trends)): ?>
            <?php foreach ($user_trends as $username => $logs): 
                $start_weight = $logs[0]['weight_kg'];
                $current_weight = end($logs)['weight_kg'];
                $current_goal = end($logs)['fitness_goal'];
                $total_logs_count = count($logs);
                $net_difference = $current_weight - $start_weight;
                
                if ($net_difference < 0) {
                    $diff_badge = "bg-danger text-white";
                    $diff_text = round(abs($net_difference), 2) . " kg lost";
                } elseif ($net_difference > 0) {
                    $diff_badge = "bg-success text-white";
                    $diff_text = "+" . round($net_difference, 2) . " kg gained";
                } else {
                    $diff_badge = "bg-secondary text-white";
                    $diff_text = "No Net Change";
                }
            ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center border-0 pt-3">
                            <h5 class="fw-bold text-dark m-0"><?php echo htmlspecialchars($username); ?></h5>
                            <span class="badge bg-light text-dark border"><?php echo $current_goal; ?></span>
                        </div>
                        <div class="card-body">
                            <div class="row text-center bg-light py-2 rounded mb-3 g-0 border">
                                <div class="col-4 border-end">
                                    <span class="small text-muted d-block">Start</span>
                                    <strong><?php echo $start_weight; ?> kg</strong>
                                </div>
                                <div class="col-4 border-end">
                                    <span class="small text-muted d-block">Current</span>
                                    <strong><?php echo $current_weight; ?> kg</strong>
                                </div>
                                <div class="col-4">
                                    <span class="small text-muted d-block">Logs</span>
                                    <strong><?php echo $total_logs_count; ?></strong>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="small text-muted">Overall Journey Progress:</span>
                                <span class="badge <?php echo $diff_badge; ?> fs-7"><?php echo $diff_text; ?></span>
                            </div>

                            <h6 class="small fw-bold text-secondary text-uppercase border-bottom pb-1 mb-2">Progress Timeline</h6>
                            <div style="max-height: 180px; overflow-y: auto;" class="pe-1">
                                <?php 
                                $prev_weight = null;
                                foreach ($logs as $index => $log): 
                                    $current_log_weight = $log['weight_kg'];
                                    
                                    $step_change = "";
                                    if ($prev_weight !== null) {
                                        $diff = $current_log_weight - $prev_weight;
                                        if ($diff < 0) {
                                            $step_change = "<span class='text-danger small fw-semibold'>↓ " . abs(round($diff, 2)) . " kg</span>";
                                        } elseif ($diff > 0) {
                                            $step_change = "<span class='text-success small fw-semibold'>↑ " . round($diff, 2) . " kg</span>";
                                        } else {
                                            $step_change = "<span class='text-muted small'>= No change</span>";
                                        }
                                    } else {
                                        $step_change = "<span class='text-muted small italic'>(Starting Baseline)</span>";
                                    }
                                    $prev_weight = $current_log_weight;
                                ?>
                                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light">
                                        <span class="small text-muted"><?php echo date('M d', strtotime($log['log_date'])); ?></span>
                                        <span class="small fw-bold"><?php echo $current_log_weight; ?> kg</span>
                                        <span><?php echo $step_change; ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted m-0">No progress tracking data available. Log metrics first on the BMI Calculator page!</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'footer.php'; ?>