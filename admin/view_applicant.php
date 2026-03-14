<?php
require_once '../modules/Applicants.php';
require_once '../MailService.php';

$applicantObj = new Applicants();

$id = $_GET['id'];
$app = $applicantObj->getApplicant($id);

include 'includes/header.php';
?>
<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --shadow-lg: 0 20px 25px -5px rgba(0, 0,0, 0.1), 0 10px 10px -5px rgba(0, 0,0, 0.04);
    --shadow-xl: 0 25px 50px -12px rgba(0, 0,0, 0.25);
}

.card {
    border-radius: 20px !important;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl) !important;
}

.btn {
    border-radius: 12px !important;
    font-weight: 500 !important;
    padding: 12px 24px !important;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.2) !important;
}

.btn-primary, .btn-success {
    background: var(--primary-gradient) !important;
    border: none !important;
}

.info-item {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1.5rem;
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,0.05);
}

.modal-content {
    border-radius: 24px !important;
}

.modal-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 24px 24px 0 0 !important;
}

.badge {
    font-size: 0.875rem !important;
    padding: 0.75rem 1.25rem !important;
}

.shadow-lg {
    box-shadow: var(--shadow-lg) !important;
}

.shadow-xl {
    box-shadow: var(--shadow-xl) !important;
}
</style>
<div class="container-fluid py-4 px-3">
    <?php if (!$app): ?>
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="alert alert-danger text-center shadow-sm border-0" role="alert">
                    <div class="alert-icon mb-3">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                    </div>
                    <h4 class="alert-heading mb-2">Applicant Not Found</h4>
                    <p class="mb-0">The applicant you're looking for doesn't exist.</p>
                    <a href="applicants.php" class="btn btn-outline-danger mt-3">
                        <i class="fas fa-arrow-left me-2"></i>Back to Applicants
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h1 class="h2 mb-1 fw-bold text-dark">
                            <?= htmlspecialchars($app['firstname'].' '.$app['lastname']). ' ' ?>
                            <?=ucfirst($app['suffix']) ? ucfirst($app['suffix']).'.' : null ?>
                        </h1>
                        <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-<?= strtolower($app['status']) == 'interview' ? 'info' : 'secondary' ?> fs-6 px-3 py-2">
                            <?= $app['status'] ?>
                        </span>
                                <span class="text-muted small"><?= $app['email'] ?></span>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="text-muted small d-block">Apply ID: #<?= $app['apply_id'] ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 h-100">
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-2">Email</label>
                                    <p class="h6 mb-0 text-dark lh-sm"><?= htmlspecialchars($app['email']) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-2">Phone</label>
                                    <p class="h6 mb-0 text-dark lh-sm"><?= htmlspecialchars($app['phone']) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-2">Gender</label>
                                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fs-6">
                                        <?= ucfirst($app['gender']) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-2">Apply ID</label>
                                    <p class="h6 mb-0 text-primary fw-semibold">#<?= $app['apply_id'] ?></p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-2">Skills</label>
                                    <p class="mb-0 lh-lg text-dark"><?= htmlspecialchars($app['skills']) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-lg border-0 h-100">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h5 class="card-title mb-0 fw-semibold text-dark">Quick Actions</h5>
                    </div>
                    <div class="card-body pt-2">
                        <div class="d-grid gap-2 mb-4">
                            <form method="POST" action="update_status.php" class="d-grid gap-2">
                                <input type="hidden" name="id" value="<?= $app['apply_id'] ?>">
                                <button type="submit" name="status" value="Rejected" class="btn btn-outline-danger btn-lg shadow-sm border-2" onclick="return confirm('Are you sure you want to reject this applicant?' )">
                                    <i class="fas fa-times-circle me-2"></i>Reject
                                </button>
                            </form>
                        </div>

                        <button class="btn btn-warning btn-lg w-100 shadow-sm mb-3" data-bs-toggle="modal" data-bs-target="#interviewModal">
                            <i class="fas fa-calendar-plus me-2"></i>Schedule Interview
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="interviewModal" tabindex="-1" aria-labelledby="interviewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-xl">
                    <div class="modal-header border-0 pb-0">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="fas fa-calendar-check fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h5 class="modal-title mb-1 fw-bold" id="interviewModalLabel">Schedule Interview</h5>
                                <p class="text-muted mb-0 small">Set up interview details for <?= htmlspecialchars($app['firstname']) ?></p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="scheduleInterviewForm">
                        <div class="modal-body p-4">
                            <input type="hidden" name="applicant_id" value="<?= $app['apply_id'] ?>">
                            
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-2">Interview Date</label>
                                <input type="date" name="date" class="form-control form-control-lg shadow-sm border-primary" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-semibold mb-2">Interview Time</label>
                                <input type="time" name="time" class="form-control form-control-lg shadow-sm border-primary" required>
                            </div>
                            
                            <div class="mb-0">
                                <label class="form-label fw-semibold mb-2">Interview Type</label>
                                <select name="type" class="form-select form-select-lg shadow-sm">
                                    <option value="Online"> Online</option>
                                    <option value="Onsite"> Onsite</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-0 bg-light px-4 py-3">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-primary px-4" id="submitInterviewBtn">
                                <i class="fas fa-save me-2"></i>Save Interview
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
        document.getElementById('scheduleInterviewForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = document.getElementById('submitInterviewBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
            
            fetch('schedule_interview.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('interviewModal'));
                    modal.hide();
                    
                    // Show success message
                    alert(data.message);
                    
                    // Reload page to show updated status
                    location.reload();
                } else {
                    alert(data.message || 'Failed to schedule interview');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while scheduling the interview');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Save Interview';
            });
        });
        </script>
    <?php endif; ?>
</div>



<?php include 'includes/footer.php'; ?>
