<?php
session_start();
include 'includes/header.php';

$message     = '';
$messageType = '';
$piStatus    = $onboarding['personal_info_status'] ?? 'Not Submitted';
$isSubmitted = in_array($piStatus, ['Pending Review', 'Approved', 'Rejected']);
$isLocked    = in_array($piStatus, ['Pending Review', 'Approved']);
$mode        = isset($_GET['mode']) ? $_GET['mode'] : ($isSubmitted ? 'review' : 'form');

// Lock editing when Pending Review or Approved
if ($isLocked && $mode === 'edit') $mode = 'review';

function validateField($value, $label, &$errors) {
    if (empty(trim($value))) $errors[] = "$label is required.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isLocked) {
    $errors = [];

    $data = [
        'emergency_contact'      => trim($_POST['emergency_contact'] ?? ''),
        'emergency_phone'        => trim($_POST['emergency_phone'] ?? ''),
        'emergency_relationship' => trim($_POST['emergency_relationship'] ?? ''),
        'tin_number'             => trim($_POST['tin_number'] ?? ''),
        'sss_number'             => trim($_POST['sss_number'] ?? ''),
        'pagibig_number'         => trim($_POST['pagibig_number'] ?? ''),
        'philhealth_number'      => trim($_POST['philhealth_number'] ?? ''),
        'address'                => trim($_POST['address'] ?? ''),
        'city'                   => trim($_POST['city'] ?? ''),
        'province'               => trim($_POST['province'] ?? ''),
        'postal_code'            => trim($_POST['postal_code'] ?? ''),
        'bank_name'              => trim($_POST['bank_name'] ?? ''),
        'bank_account_number'    => trim($_POST['bank_account_number'] ?? ''),
    ];

    validateField($data['emergency_contact'], 'Emergency contact name', $errors);
    validateField($data['emergency_phone'], 'Emergency contact phone', $errors);
    validateField($data['emergency_relationship'], 'Relationship', $errors);
    validateField($data['tin_number'], 'TIN number', $errors);
    validateField($data['sss_number'], 'SSS number', $errors);
    validateField($data['pagibig_number'], 'Pag-IBIG number', $errors);
    validateField($data['philhealth_number'], 'PhilHealth number', $errors);
    validateField($data['address'], 'Address', $errors);
    validateField($data['city'], 'City/Municipality', $errors);
    validateField($data['province'], 'Province', $errors);
    validateField($data['postal_code'], 'Postal code', $errors);
    validateField($data['bank_name'], 'Bank name', $errors);
    validateField($data['bank_account_number'], 'Bank account number', $errors);

    if (!empty($data['emergency_phone']) && !preg_match('/^[0-9+\-\s]{7,15}$/', $data['emergency_phone'])) {
        $errors[] = 'Emergency phone number format is invalid.';
    }

    if (!empty($errors)) {
        $message     = implode('<br>', $errors);
        $messageType = 'danger';
        $mode        = 'form';
    } else {
        if ($employeeObj->updatePersonalInfo($_SESSION['employee_id'], $data)) {
            $onboarding  = $employeeObj->getOnboardingData($_SESSION['employee_id']);
            $piStatus    = 'Pending Review';
            $isSubmitted = true;
            $isLocked    = false;
            $mode        = 'review';
            $message     = 'Information submitted! Waiting for HR review.';
            $messageType = 'success';
        } else {
            $message     = 'Failed to save information. Please try again.';
            $messageType = 'danger';
            $mode        = 'form';
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="page-title">Personal Information</h1>
        <p class="page-subtitle">
            <?php if ($piStatus === 'Approved'): ?>
                Your information has been approved.
            <?php elseif ($piStatus === 'Pending Review'): ?>
                Your information is under review.
            <?php elseif ($piStatus === 'Rejected'): ?>
                Your information was rejected. Please review and re-submit.
            <?php else: ?>
                Complete your personal details for HR records.
            <?php endif; ?>
        </p>
    </div>
    <?php if ($isSubmitted && $mode === 'review' && !$isLocked): ?>
        <a href="?mode=edit" class="btn btn-sm <?= $piStatus === 'Rejected' ? 'btn-outline-danger' : 'btn-outline-primary' ?>">
            <i class="bi bi-pencil"></i> <?= $piStatus === 'Rejected' ? 'Re-submit Information' : 'Edit Information' ?>
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
    <?php if ($piStatus === 'Pending Review'): ?>
        <div class="alert alert-info" style="font-size: 0.85rem;">
            <i class="bi bi-hourglass-split"></i> Your information is currently under review. You cannot make changes until it is reviewed.
        </div>
    <?php elseif ($piStatus === 'Rejected'): ?>
        <div class="alert alert-danger" style="font-size: 0.85rem;">
            <i class="bi bi-x-circle"></i> Your information was rejected. Please click "Re-submit Information" to update and resubmit.
        </div>
    <?php elseif ($piStatus === 'Approved'): ?>
        <div class="alert alert-success" style="font-size: 0.85rem;">
            <i class="bi bi-check-circle"></i> Your information has been approved.
        </div>
    <?php endif; ?>

    <?php
    $statusBadge = match($piStatus) {
        'Approved'       => '<span class="badge bg-success">&#10003; Approved</span>',
        'Pending Review' => '<span class="badge bg-info text-dark">&#9203; Pending Review</span>',
        'Rejected'       => '<span class="badge bg-danger">&#10007; Rejected</span>',
        default          => '',
    };
    ?>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            Emergency Contact
            <?= $statusBadge ?>
        </div>
        <div class="card-body">
            <div class="info-row">
                <span class="info-label">Contact Name</span>
                <span class="info-value"><?= htmlspecialchars($onboarding['emergency_contact']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone Number</span>
                <span class="info-value"><?= htmlspecialchars($onboarding['emergency_phone']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Relationship</span>
                <span class="info-value"><?= htmlspecialchars($onboarding['emergency_relationship']) ?></span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Government IDs</div>
        <div class="card-body">
            <div class="info-row">
                <span class="info-label">TIN Number</span>
                <span class="info-value"><?= htmlspecialchars($onboarding['tin_number']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">SSS Number</span>
                <span class="info-value"><?= htmlspecialchars($onboarding['sss_number']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Pag-IBIG Number</span>
                <span class="info-value"><?= htmlspecialchars($onboarding['pagibig_number']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">PhilHealth Number</span>
                <span class="info-value"><?= htmlspecialchars($onboarding['philhealth_number']) ?></span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Address & Contact Details</div>
        <div class="card-body">
            <div class="info-row">
                <span class="info-label">Address</span>
                <span class="info-value"><?= htmlspecialchars($onboarding['address']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">City/Municipality</span>
                <span class="info-value"><?= htmlspecialchars($onboarding['city']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Province</span>
                <span class="info-value"><?= htmlspecialchars($onboarding['province']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Postal Code</span>
                <span class="info-value"><?= htmlspecialchars($onboarding['postal_code']) ?></span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Bank Account Details</div>
        <div class="card-body">
            <div class="info-row">
                <span class="info-label">Bank Name</span>
                <span class="info-value"><?= htmlspecialchars($onboarding['bank_name']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Account Number</span>
                <span class="info-value"><?= htmlspecialchars($onboarding['bank_account_number']) ?></span>
            </div>
        </div>
    </div>

<?php else: ?>

    <?php if ($isSubmitted && $mode === 'edit'): ?>
        <div class="alert alert-warning" style="font-size: 0.8rem;">
            <i class="bi bi-exclamation-triangle"></i>
            You are editing already submitted information. After saving, HR will need to re-review your submission.
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="card">
            <div class="card-header">Emergency Contact Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Contact Name *</label>
                        <input type="text" name="emergency_contact" class="form-control"
                               value="<?= htmlspecialchars($onboarding['emergency_contact'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Contact Phone *</label>
                        <input type="text" name="emergency_phone" class="form-control"
                               placeholder="e.g., 09171234567"
                               value="<?= htmlspecialchars($onboarding['emergency_phone'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Relationship *</label>
                        <select name="emergency_relationship" class="form-select" required>
                            <option value="">Select relationship</option>
                            <?php foreach (['Spouse', 'Parent', 'Sibling', 'Child', 'Relative', 'Friend', 'Other'] as $rel): ?>
                                <option value="<?= $rel ?>" <?= ($onboarding['emergency_relationship'] ?? '') === $rel ? 'selected' : '' ?>>
                                    <?= $rel ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Government ID Numbers (Philippines)</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">TIN Number *</label>
                        <input type="text" name="tin_number" class="form-control"
                               placeholder="000-000-000-000"
                               value="<?= htmlspecialchars($onboarding['tin_number'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">SSS Number *</label>
                        <input type="text" name="sss_number" class="form-control"
                               placeholder="00-0000000-0"
                               value="<?= htmlspecialchars($onboarding['sss_number'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pag-IBIG Number *</label>
                        <input type="text" name="pagibig_number" class="form-control"
                               placeholder="0000-0000-0000"
                               value="<?= htmlspecialchars($onboarding['pagibig_number'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">PhilHealth Number *</label>
                        <input type="text" name="philhealth_number" class="form-control"
                               placeholder="00-000000000-0"
                               value="<?= htmlspecialchars($onboarding['philhealth_number'] ?? '') ?>" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Address & Contact Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Complete Address *</label>
                        <textarea name="address" class="form-control" rows="2"
                                  placeholder="House/Unit No., Street, Barangay" required><?= htmlspecialchars($onboarding['address'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">City/Municipality *</label>
                        <input type="text" name="city" class="form-control"
                               value="<?= htmlspecialchars($onboarding['city'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Province *</label>
                        <input type="text" name="province" class="form-control"
                               value="<?= htmlspecialchars($onboarding['province'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Postal Code *</label>
                        <input type="text" name="postal_code" class="form-control"
                               placeholder="e.g., 1000"
                               value="<?= htmlspecialchars($onboarding['postal_code'] ?? '') ?>" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Bank Account Details (For Payroll)</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Bank Name *</label>
                        <select name="bank_name" class="form-select" required>
                            <option value="">Select bank</option>
                            <?php foreach (['BDO', 'BPI', 'Metrobank', 'PNB', 'UnionBank', 'Landbank', 'RCBC', 'Security Bank', 'EastWest Bank', 'Other'] as $bank): ?>
                                <option value="<?= $bank ?>" <?= ($onboarding['bank_name'] ?? '') === $bank ? 'selected' : '' ?>>
                                    <?= $bank ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Account Number *</label>
                        <input type="text" name="bank_account_number" class="form-control"
                               value="<?= htmlspecialchars($onboarding['bank_account_number'] ?? '') ?>" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-2 mb-4">
            <?php if ($isSubmitted): ?>
                <a href="?mode=review" class="btn btn-outline-secondary">Cancel</a>
            <?php else: ?>
                <a href="onboarding.php" class="btn btn-outline-secondary">Cancel</a>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary">
                <?= $isSubmitted ? 'Re-submit Information' : 'Save Information' ?>
            </button>
        </div>
    </form>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>
