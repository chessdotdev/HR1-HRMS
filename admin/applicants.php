<?php
require_once '../modules/Applicants.php';

$applicantObj = new Applicants();

// pending applications
$applicants = $applicantObj->getApplicants('Pending');
?>

<?php include 'includes/header.php'; ?>

<div class="main p-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Pending Applications</h2>
        <span class="badge bg-warning text-dark fs-6">
            <?= count($applicants) ?> Pending
        </span>
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
                            <th>Skills</th>
                            <th>Applied At</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if(!empty($applicants)): ?>
                        <?php $i=1; foreach($applicants as $app): ?>

                        <tr>

                            <td><?= $i++ ?></td>

                            <td>
                                <div class="d-flex align-items-center gap-2">

                                    <!-- <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center"
                                         style="width:35px;height:35px;font-size:14px;">
                                        <?= strtoupper($app['firstname'][0]) ?>
                                    </div> -->

                                    <div>
                                        <?= htmlspecialchars($app['firstname'].' '.$app['lastname']) ?>
                                    </div>

                                </div>
                            </td>

                            <td><?= htmlspecialchars($app['job_title'] ?? 'N/A') ?></td>

                            <td><?= htmlspecialchars($app['email']) ?></td>

                            <td><?= htmlspecialchars($app['phone']) ?></td>

                            <td class="text-muted small" style="max-width:250px;">
                                <?php 
                                $skills = htmlspecialchars($app['skills'] ?? '');
                                echo strlen($skills) > 50 ? substr($skills,0,50).'...' : $skills;
                                ?>
                            </td>

                            <td><?= htmlspecialchars($app['applied_at']) ?></td>

                            <td>
                                <span class="badge bg-warning text-dark">
                                    <?= htmlspecialchars($app['status']) ?>
                                </span>
                            </td>

                            <td>
                                <a href="view_applicant.php?id=<?= $app['apply_id'] ?>" 
                                   class="btn btn-sm btn-primary">
                                   View
                                </a>
                            </td>

                        </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                No pending applications
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>

<style>
.table tbody tr:hover{
background:#f8f9fa;
}
</style>

<?php include 'includes/footer.php'; ?>