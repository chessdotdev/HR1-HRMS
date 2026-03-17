<?php
session_start();
require_once '../modules/Employee.php';
require_once '../modules/AuditLog.php';
require_once 'includes/verify_admin.php';

$employeeObj = new Employee();
$audit = new AuditLog();
$admin_id   = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_username'];
$employee_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$messageType = '';

$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $employee_id) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'schedule') {
        $day1_date = $_POST['day1_date'] ?? null;
        $day2_date = $_POST['day2_date'] ?? null;
        $day3_date = $_POST['day3_date'] ?? null;
        
        $query = "UPDATE employee_onboarding SET 
                  orientation_day1_date = :day1_date,
                  orientation_day2_date = :day2_date,
                  orientation_day3_date = :day3_date
                  WHERE employee_id = :employee_id";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':day1_date', $day1_date);
        $stmt->bindParam(':day2_date', $day2_date);
        $stmt->bindParam(':day3_date', $day3_date);
        $stmt->bindParam(':employee_id', $employee_id);
        
        if ($stmt->execute()) {
            $audit->log($admin_id, $admin_name, 'Schedule Orientation', 'Onboarding', "Scheduled orientation dates for employee ID {$employee_id}");
            $message = 'Orientation dates scheduled successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to schedule dates.';
            $messageType = 'danger';
        }
    } elseif ($action === 'update_status') {
        // Update orientation status
        $day = $_POST['day'] ?? '';
        $status = $_POST['status'] ?? '';
        
        if (in_array($day, ['1', '2', '3']) && in_array($status, ['Pending', 'Completed', 'Missed'])) {
            $employeeObj->updateOrientationStatus($employee_id, $day, $status);
            $audit->log($admin_id, $admin_name, 'Update Orientation Status', 'Onboarding', "Employee ID {$employee_id} Day {$day} set to {$status}");
            $message = "Day $day status updated to $status";
            $messageType = 'success';
        }
    }
}

// Get all new hires for the list
$query = "SELECT e.employee_id, a.firstname, a.lastname, 
          eo.orientation_day1_date, eo.orientation_day1_status,
          eo.orientation_day2_date, eo.orientation_day2_status,
          eo.orientation_day3_date, eo.orientation_day3_status,
          eo.orientation_completed
          FROM employees e
          LEFT JOIN applicantss a ON e.applicant_id = a.apply_id
          LEFT JOIN employee_onboarding eo ON e.employee_id = eo.employee_id
          WHERE e.employment_status = 'New Hire'
          ORDER BY e.hired_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$newHires = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get selected employee details
$selectedEmployee = null;
$selectedOnboarding = null;
if ($employee_id) {
    $selectedEmployee = $employeeObj->getEmployeeById($employee_id);
    $selectedOnboarding = $employeeObj->getOnboardingData($employee_id);
}
?>

<?php include 'includes/header.php'; ?>

<div class="main p-3">
    <div class="mb-3">
        <h2 class="mb-0">Orientation Schedule</h2>
        <p class="text-muted mb-0">Manage 3-day orientation schedule for new hires</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">New Hires</div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($newHires as $hire): ?>
                            <a href="?id=<?= $hire['employee_id'] ?>" 
                               class="list-group-item list-group-item-action <?= $employee_id == $hire['employee_id'] ? 'active' : '' ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= htmlspecialchars($hire['firstname'] . ' ' . $hire['lastname']) ?></strong>
                                        <?php if ($hire['orientation_completed']): ?>
                                            <br><small class="badge bg-success">Completed</small>
                                        <?php endif; ?>
                                    </div>
                                    <i class="bi bi-chevron-right"></i>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <?php if ($selectedEmployee && $selectedOnboarding): ?>
                <div class="card mb-3">
                    <div class="card-header">Schedule Orientation Dates</div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="schedule">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Day 1 Date</label>
                                    <input type="date" name="day1_date" class="form-control" 
                                           value="<?= $selectedOnboarding['orientation_day1_date'] ?? '' ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Day 2 Date</label>
                                    <input type="date" name="day2_date" class="form-control" 
                                           value="<?= $selectedOnboarding['orientation_day2_date'] ?? '' ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Day 3 Date</label>
                                    <input type="date" name="day3_date" class="form-control" 
                                           value="<?= $selectedOnboarding['orientation_day3_date'] ?? '' ?>" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Save Schedule</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Orientation Attendance</div>
                    <div class="card-body">
                        <?php for ($day = 1; $day <= 3; $day++): ?>
                            <?php
                            $dateField = "orientation_day{$day}_date";
                            $statusField = "orientation_day{$day}_status";
                            $date = $selectedOnboarding[$dateField];
                            $status = $selectedOnboarding[$statusField];
                            ?>
                            <div class="card mb-2">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <strong>Day <?= $day ?></strong>
                                            <?php if ($date): ?>
                                                <br><small class="text-muted"><?= date('M d, Y', strtotime($date)) ?></small>
                                            <?php else: ?>
                                                <br><small class="text-muted">Not scheduled</small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="badge bg-<?= $status === 'Completed' ? 'success' : ($status === 'Missed' ? 'danger' : 'warning text-dark') ?>">
                                                <?= $status ?>
                                            </span>
                                        </div>
                                        <div class="col-md-6">
                                            <form method="POST" class="d-flex gap-2">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="day" value="<?= $day ?>">
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="Pending" <?= $status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="Completed" <?= $status === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                                    <option value="Missed" <?= $status === 'Missed' ? 'selected' : '' ?>>Missed</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-calendar-event" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-3">Select a new hire to manage orientation schedule</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
