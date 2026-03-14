<?php
session_start();
include 'includes/header.php';

$message     = '';
$messageType = '';
$docStatus   = $onboarding['documents_status'] ?? 'Not Submitted';
$isSubmitted = in_array($docStatus, ['Pending Review', 'Approved', 'Rejected']);
$isLocked    = in_array($docStatus, ['Pending Review', 'Approved']);
$mode        = isset($_GET['mode']) ? $_GET['mode'] : ($isSubmitted ? 'review' : 'form');
$uploadDir   = '../public/uploads/employee_documents/';

// Lock editing when Pending Review or Approved
if ($isLocked && $mode === 'edit') $mode = 'review';

if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

$allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
$allowedExts  = ['jpg', 'jpeg', 'png', 'pdf'];
$maxSize      = 5 * 1024 * 1024;

function handleUpload($fileKey, $prefix, $employeeId, $uploadDir, $allowedTypes, $allowedExts, $maxSize, &$errors) {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] === UPLOAD_ERR_NO_FILE) return null;
    $file = $_FILES[$fileKey];
    if ($file['error'] !== UPLOAD_ERR_OK) { $errors[] = "Upload error for $fileKey."; return null; }
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $mime = mime_content_type($file['tmp_name']);
    if (!in_array($ext, $allowedExts) || !in_array($mime, $allowedTypes)) {
        $errors[] = ucfirst(str_replace('_', ' ', $fileKey)) . ': Only PDF, JPG, PNG files are allowed.';
        return null;
    }
    if ($file['size'] > $maxSize) { $errors[] = ucfirst(str_replace('_', ' ', $fileKey)) . ': Max 5MB.'; return null; }
    $fileName = $prefix . '_' . $employeeId . '_' . time() . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) return $fileName;
    $errors[] = "Failed to save $fileKey.";
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isLocked) {
    $errors = [];

    $documents = [
        'government_id_path'       => $onboarding['government_id_path'] ?? null,
        'diploma_tor_path'         => $onboarding['diploma_tor_path'] ?? null,
        'nbi_clearance_path'       => $onboarding['nbi_clearance_path'] ?? null,
        'medical_certificate_path' => $onboarding['medical_certificate_path'] ?? null,
    ];

    $govId   = handleUpload('government_id', 'gov_id', $_SESSION['employee_id'], $uploadDir, $allowedTypes, $allowedExts, $maxSize, $errors);
    $diploma = handleUpload('diploma_tor', 'diploma', $_SESSION['employee_id'], $uploadDir, $allowedTypes, $allowedExts, $maxSize, $errors);
    $nbi     = handleUpload('nbi_clearance', 'nbi', $_SESSION['employee_id'], $uploadDir, $allowedTypes, $allowedExts, $maxSize, $errors);
    $medical = handleUpload('medical_certificate', 'medical', $_SESSION['employee_id'], $uploadDir, $allowedTypes, $allowedExts, $maxSize, $errors);

    if ($govId)   $documents['government_id_path'] = $govId;
    if ($diploma) $documents['diploma_tor_path']   = $diploma;
    if ($nbi)     $documents['nbi_clearance_path'] = $nbi;
    if ($medical) $documents['medical_certificate_path'] = $medical;

    if (empty($documents['government_id_path']))  $errors[] = 'Government ID is required.';
    if (empty($documents['diploma_tor_path']))     $errors[] = 'Diploma or TOR is required.';

    if (!empty($errors)) {
        $message     = implode('<br>', $errors);
        $messageType = 'danger';
        $mode        = 'form';
    } else {
        if ($employeeObj->updateDocuments($_SESSION['employee_id'], $documents)) {
            $onboarding  = $employeeObj->getOnboardingData($_SESSION['employee_id']);
            $docStatus   = 'Pending Review';
            $isSubmitted = true;
            $isLocked    = false;
            $mode        = 'review';
            $message     = 'Documents submitted! Waiting for HR review.';
            $messageType = 'success';
        } else {
            $message     = 'Failed to save documents. Please try again.';
            $messageType = 'danger';
            $mode        = 'form';
        }
    }
}

function docRow($label, $path, $required = true) {
    echo '<div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid #f4f4f5;">';
    echo '<div><span style="font-size: 0.875rem; font-weight: 500;">' . $label . '</span>';
    if (!$required) echo ' <span class="badge bg-secondary" style="font-size: 0.65rem;">Optional</span>';
    echo '</div>';
    if ($path) {
        echo '<a href="../public/uploads/employee_documents/' . htmlspecialchars($path) . '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View</a>';
    } else {
        echo '<span class="badge bg-secondary">Not uploaded</span>';
    }
    echo '</div>';
}
?>

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="page-title">Document Submission</h1>
        <p class="page-subtitle">
            <?php if ($docStatus === 'Approved'): ?>
                Your documents have been approved.
            <?php elseif ($docStatus === 'Pending Review'): ?>
                Your documents are under review.
            <?php elseif ($docStatus === 'Rejected'): ?>
                Your documents were rejected. Please re-upload.
            <?php else: ?>
                Upload required documents for verification.
            <?php endif; ?>
        </p>
    </div>
    <?php if ($isSubmitted && $mode === 'review' && !$isLocked): ?>
        <a href="?mode=edit" class="btn btn-sm <?= $docStatus === 'Rejected' ? 'btn-outline-danger' : 'btn-outline-primary' ?>">
            <i class="bi bi-arrow-repeat"></i> <?= $docStatus === 'Rejected' ? 'Re-upload Documents' : 'Replace Documents' ?>
        </a>
    <?php endif; ?>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($isSubmitted && $mode === 'review'): ?>

    <?php if ($docStatus === 'Pending Review'): ?>
        <div class="alert alert-info" style="font-size: 0.85rem;">
            <i class="bi bi-hourglass-split"></i> Your documents are currently under review. You cannot make changes until they are reviewed.
        </div>
    <?php elseif ($docStatus === 'Rejected'): ?>
        <div class="alert alert-danger" style="font-size: 0.85rem;">
            <i class="bi bi-x-circle"></i> Your documents were rejected. Please click "Re-upload Documents" to resubmit.
        </div>
    <?php elseif ($docStatus === 'Approved'): ?>
        <div class="alert alert-success" style="font-size: 0.85rem;">
            <i class="bi bi-check-circle"></i> Your documents have been approved.
        </div>
    <?php endif; ?>

    <?php
    $statusBadge = match($docStatus) {
        'Approved'       => '<span class="badge bg-success">&#10003; Approved</span>',
        'Pending Review' => '<span class="badge bg-info text-dark">&#9203; Pending Review</span>',
        'Rejected'       => '<span class="badge bg-danger">&#10007; Rejected</span>',
        default          => '',
    };
    ?>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            Submitted Documents
            <?= $statusBadge ?>
        </div>
        <div class="card-body">
            <?php docRow('Government ID', $onboarding['government_id_path'], true); ?>
            <?php docRow('Diploma / TOR', $onboarding['diploma_tor_path'], true); ?>
            <?php docRow('NBI Clearance', $onboarding['nbi_clearance_path'], false); ?>
            <?php docRow('Medical Certificate', $onboarding['medical_certificate_path'], false); ?>
        </div>
    </div>

<?php else: ?>
    <!-- ===== UPLOAD FORM ===== -->

    <?php if ($isSubmitted && $mode === 'edit'): ?>
        <div class="alert alert-warning" style="font-size: 0.8rem;">
            <i class="bi bi-exclamation-triangle"></i>
            You are replacing already submitted documents. After saving, HR will need to re-review your submission. Existing files are kept if no new file is selected.
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="card">
            <div class="card-header">Required Documents</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Government ID * <small class="text-muted fw-normal">(PDF, JPG, PNG — max 5MB)</small></label>
                        <input type="file" name="government_id" class="form-control" accept=".pdf,.jpg,.jpeg,.png" <?= !$isSubmitted ? 'required' : '' ?>>
                        <?php if ($onboarding['government_id_path'] ?? null): ?>
                            <small class="text-success"><i class="bi bi-check-circle"></i> Already uploaded — <a href="../public/uploads/employee_documents/<?= htmlspecialchars($onboarding['government_id_path']) ?>" target="_blank">view file</a></small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Diploma or TOR * <small class="text-muted fw-normal">(PDF, JPG, PNG — max 5MB)</small></label>
                        <input type="file" name="diploma_tor" class="form-control" accept=".pdf,.jpg,.jpeg,.png" <?= !$isSubmitted ? 'required' : '' ?>>
                        <?php if ($onboarding['diploma_tor_path'] ?? null): ?>
                            <small class="text-success"><i class="bi bi-check-circle"></i> Already uploaded — <a href="../public/uploads/employee_documents/<?= htmlspecialchars($onboarding['diploma_tor_path']) ?>" target="_blank">view file</a></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Optional Documents</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">NBI Clearance <small class="text-muted fw-normal">(PDF, JPG, PNG — max 5MB)</small></label>
                        <input type="file" name="nbi_clearance" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <?php if ($onboarding['nbi_clearance_path'] ?? null): ?>
                            <small class="text-success"><i class="bi bi-check-circle"></i> Already uploaded — <a href="../public/uploads/employee_documents/<?= htmlspecialchars($onboarding['nbi_clearance_path']) ?>" target="_blank">view file</a></small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Medical Certificate <small class="text-muted fw-normal">(PDF, JPG, PNG — max 5MB)</small></label>
                        <input type="file" name="medical_certificate" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        <?php if ($onboarding['medical_certificate_path'] ?? null): ?>
                            <small class="text-success"><i class="bi bi-check-circle"></i> Already uploaded — <a href="../public/uploads/employee_documents/<?= htmlspecialchars($onboarding['medical_certificate_path']) ?>" target="_blank">view file</a></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="border-left: 3px solid #3b82f6;">
            <div class="card-body py-2">
                <p class="mb-1" style="font-size: 0.8rem; font-weight: 600;"><i class="bi bi-info-circle"></i> Guidelines</p>
                <ul style="font-size: 0.78rem; color: #71717a; margin-bottom: 0; padding-left: 1.25rem;">
                    <li>Accepted formats: PDF, JPG, PNG only</li>
                    <li>Maximum file size: 5MB per document</li>
                    <li>Ensure documents are clear and readable</li>
                    <li>Government ID must be valid and not expired</li>
                </ul>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-2 mb-4">
            <?php if ($isSubmitted): ?>
                <a href="?mode=review" class="btn btn-outline-secondary">Cancel</a>
            <?php else: ?>
                <a href="onboarding.php" class="btn btn-outline-secondary">Cancel</a>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary">
                <?= $isSubmitted ? 'Re-submit Documents' : 'Submit Documents' ?>
            </button>
        </div>
    </form>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>
