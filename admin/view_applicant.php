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
        padding: 8px 10px !important;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2) !important;
    }

    .btn-primary, .btn-success {
        background: black;
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
        <div class="alert alert-danger">
            Applicant not found. <a href="applicants.php">Back to Applicants</a>
        </div>
    <?php elseif ($app['status'] !== 'Pending'): ?>
        <div class="mb-3">
            <a href="applicants.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Back to Applicants
            </a>
        </div>
        <div class="alert alert-warning d-flex align-items-center gap-3">
            <i class="bi bi-info-circle-fill fs-4"></i>
            <div>
                <strong><?= htmlspecialchars($app['firstname'] . ' ' . $app['lastname']). ' '.(ucfirst($app['suffix']) ? ucfirst($app['suffix']).'.' : '') ?></strong> cannot be actioned — this applicant is no longer pending.<br>
                Current status: 
                <span class="badge bg-<?= match($app['status']) { 'Rejected' => 'danger', 'Interview' => 'info', 'Hired' => 'success', default => 'secondary' } ?>">
                    <?= htmlspecialchars($app['status']) ?>
                </span>
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
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-transparent border-0 pb-0"><h6 class="fw-semibold mb-0">Personal Information</h6></div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-1">First Name</label>
                                    <p class="h6 mb-0"><?= htmlspecialchars($app['firstname']) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-1">Last Name</label>
                                    <p class="h6 mb-0"><?= htmlspecialchars($app['lastname']) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-1">Middle Name</label>
                                    <p class="h6 mb-0"><?= htmlspecialchars($app['middle_name'] ?? '—') ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-1">Suffix</label>
                                    <p class="h6 mb-0"><?= htmlspecialchars(strtoupper($app['suffix'] ?? 'None')) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-1">Birthdate</label>
                                    <p class="h6 mb-0"><?= htmlspecialchars($app['birthdate'] ?? '—') ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-1">Age</label>
                                    <p class="h6 mb-0"><?= htmlspecialchars($app['age'] ?? '—') ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-1">Gender</label>
                                    <p class="h6 mb-0"><?= htmlspecialchars($app['gender']) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-1">Civil Status</label>
                                    <p class="h6 mb-0"><?= htmlspecialchars($app['civil_status'] ?? '—') ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-1">Nationality</label>
                                    <p class="h6 mb-0"><?= htmlspecialchars($app['nationality'] ?? '—') ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-1">City</label>
                                    <p class="h6 mb-0"><?= htmlspecialchars($app['city'] ?? '—') ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-1">Province</label>
                                    <p class="h6 mb-0"><?= htmlspecialchars($app['province'] ?? '—') ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-1">Phone</label>
                                    <p class="h6 mb-0"><?= htmlspecialchars($app['phone']) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-1">Email</label>
                                    <p class="h6 mb-0"><?= htmlspecialchars($app['email']) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-1">Applying For</label>
                                    <p class="h6 mb-0"><?= htmlspecialchars($app['job_title'] ?? '—') ?></p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="info-item">
                                    <label class="form-label fw-semibold text-muted small mb-1">Skills</label>
                                    <p class="mb-0 lh-lg"><?= nl2br(htmlspecialchars($app['skills'])) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-lg border-0 h-100">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h5 class="card-title mb-0 fw-semibold text-dark">Actions</h5>
                    </div>
                    <div class="card-body pt-2">
                        <div class="d-grid gap-2 mb-4">
                            <form method="POST" action="update_status.php" class="d-grid gap-2" onsubmit="return confirm('Are you sure you want to reject this applicant?')">
                                <input type="hidden" name="id" value="<?= $app['apply_id'] ?>">
                                <input type="hidden" name="status" value="Rejected">
                                <button type="submit" class="btn btn-outline-danger shadow-sm border-2">
                                    <i class="bi bi-x-circle me-2"></i>Reject
                                </button>
                            </form>
                        </div>

                        <button class="btn btn-dark btn-lg w-100 shadow-sm mb-3" data-bs-toggle="modal" data-bs-target="#interviewModal">
                            <i class="fas fa-calendar-plus me-2"></i>Schedule Interview
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-2">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-transparent border-0 pb-0">
                        <h5 class="card-title mb-0 fw-semibold text-dark"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>Resume</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($app['resume_path'])): ?>
                            <div class="mb-3">
                                <a href="../public/uploads/resumes/<?= htmlspecialchars($app['resume_path']) ?>" target="_blank" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>View PDF
                                </a>
                                <a href="../public/uploads/resumes/<?= htmlspecialchars($app['resume_path']) ?>" download class="btn btn-outline-secondary btn-sm ms-2">
                                    <i class="bi bi-download me-1"></i>Download
                                </a>
                            </div>
                            <iframe src="../public/uploads/resumes/<?= htmlspecialchars($app['resume_path']) ?>" width="50%" height="100%" style="border:1px solid #dee2e6; border-radius:8px;"></iframe>
                        <?php else: ?>
                            <p class="text-muted mb-0"><i class="bi bi-exclamation-circle me-2"></i>No resume submitted.</p>
                        <?php endif; ?>
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
                                <i class="fas fa-save me-2"></i>Schedule Interview
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
