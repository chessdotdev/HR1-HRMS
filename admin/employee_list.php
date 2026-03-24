<?php
require_once '../modules/Employee.php';

$db = new Database();
$conn = $db->connect();

// Get all active employees
$query = "SELECT e.*, a.firstname, a.lastname, a.email, a.phone, a.job_title, j.department
          FROM employees e
          LEFT JOIN applicantss a ON e.applicant_id = a.apply_id
          LEFT JOIN job_openings j ON j.id = a.job_id
          WHERE e.employment_status = 'Active'
          ORDER BY e.hired_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$totalActive = count($employees);

// Count by department (from job_title for now)
$departments = [];
foreach ($employees as $emp) {
    $dept = $emp['job_title'] ?? 'Unassigned';
    if (!isset($departments[$dept])) {
        $departments[$dept] = 0;
    }
    $departments[$dept]++;
}
?>

<?php include 'includes/header.php'; ?>

<div class="main p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Employee List</h2>
            <p class="text-muted mb-0">Manage all active employees</p>
        </div>
        <span class="badge bg-success fs-6"><?= $totalActive ?> Active Employees</span>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <small class="text-muted">Total Active</small>
                    <h3 class="mb-0"><?= $totalActive ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <small class="text-muted">Departments</small>
                    <h3 class="mb-0"><?= count($departments) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <small class="text-muted">New This Month</small>
                    <h3 class="mb-0">
                        <?php
                        $thisMonth = count(array_filter($employees, function($e) {
                            return date('Y-m', strtotime($e['hired_at'])) === date('Y-m');
                        }));
                        echo $thisMonth;
                        ?>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <small class="text-muted">This Year</small>
                    <h3 class="mb-0">
                        <?php
                        $thisYear = count(array_filter($employees, function($e) {
                            return date('Y', strtotime($e['hired_at'])) === date('Y');
                        }));
                        echo $thisYear;
                        ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Table -->
    <?php if (!empty($employees)): ?>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>All Active Employees</span>
                <div class="input-group" style="max-width: 300px;">
                    <input type="text" class="form-control form-control-sm" id="searchEmployee" placeholder="Search employees...">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="employeeTable">
                        <thead class="table-light">
                            <tr>
                                <th>Employee ID</th>
                                <th>Name</th>
                                <th>Job Title</th>
                                <th>Department</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Hired Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($employees as $emp): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($emp['employee_id'] ?? '') ?></strong></td>
                                    <td>
                                        <div>
                                            <strong><?= htmlspecialchars(($emp['firstname'] ?? '') . ' ' . ($emp['lastname'] ?? '')) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($emp['username'] ?? '') ?></small>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($emp['job_title'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($emp['department'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($emp['email'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($emp['phone'] ?? '') ?></td>
                                    <td><?= !empty($emp['hired_at']) ? date('M d, Y', strtotime($emp['hired_at'])) : '—' ?></td>
                                    <td>
                                        <span class="badge bg-success">Active</span>
                                    </td>
                                    <td>
                                        <a href="view_employee.php?id=<?= $emp['employee_id'] ?>" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">No active employees yet</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Simple search functionality
document.getElementById('searchEmployee')?.addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('#employeeTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});
</script>

<?php include 'includes/footer.php'; ?>
