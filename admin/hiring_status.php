<?php
require_once '../modules/Applicants.php';
$applicantObj = new Applicants();

$filter     = $_GET['status'] ?? 'All';
$validFilters = ['All', 'Pending', 'Interview', 'Hired', 'Rejected'];
if (!in_array($filter, $validFilters)) $filter = 'All';

$applicants = $filter === 'All' ? $applicantObj->getApplicants() : $applicantObj->getApplicants($filter);

// Count per status for tabs
$db   = new Database();
$conn = $db->connect();
$counts = [];
foreach (['Pending', 'Interview', 'Hired', 'Rejected'] as $s) {
    $st = $conn->prepare("SELECT COUNT(*) FROM applicantss WHERE status = :s");
    $st->bindParam(':s', $s);
    $st->execute();
    $counts[$s] = $st->fetchColumn();
}
$counts['All'] = array_sum($counts);

include 'includes/header.php';
?>

<div class="main p-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Hiring Status</h2>
            <p class="text-muted mb-0">Overview of all applicants across every stage</p>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <?php
        $cards = [
            ['label' => 'Total',     'key' => 'All',      'color' => 'secondary'],
            ['label' => 'Pending',   'key' => 'Pending',  'color' => 'warning'],
            ['label' => 'Interview', 'key' => 'Interview','color' => 'info'],
            ['label' => 'Hired',     'key' => 'Hired',    'color' => 'success'],
            ['label' => 'Rejected',  'key' => 'Rejected', 'color' => 'danger'],
        ];
        foreach ($cards as $c): ?>
        <div class="col">
            <a href="?status=<?= $c['key'] ?>" class="text-decoration-none">
                <div class="card text-center border-<?= $c['color'] ?> <?= $filter === $c['key'] ? 'bg-'.$c['color'].' text-white' : '' ?>">
                    <div class="card-body py-2">
                        <div class="fs-4 fw-bold"><?= $counts[$c['key']] ?></div>
                        <div class="small"><?= $c['label'] ?></div>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Applicant</th>
                            <th>Job Title</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Applied At</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($applicants)): $i = 1; ?>
                        <?php foreach ($applicants as $app): ?>
                        <?php
                        $badge = match($app['status']) {
                            'Pending'   => 'bg-warning text-dark',
                            'Interview' => 'bg-info text-dark',
                            'Hired'     => 'bg-success',
                            'Rejected'  => 'bg-danger',
                            default     => 'bg-secondary',
                        };
                        ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><strong><?= htmlspecialchars($app['firstname'] . ' ' . $app['lastname']) ?></strong></td>
                            <td><?= htmlspecialchars($app['job_title'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($app['email']) ?></td>
                            <td><?= htmlspecialchars($app['phone']) ?></td>
                            <td><?= date('M d, Y', strtotime($app['applied_at'])) ?></td>
                            <td><span class="badge <?= $badge ?>"><?= $app['status'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No applicants found</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
