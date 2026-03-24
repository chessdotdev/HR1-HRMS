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
        'tin_photo_path'         => null,
        'sss_photo_path'         => null,
        'pagibig_photo_path'     => null,
        'philhealth_photo_path'  => null,
        'id_photo_path'          => null,
        'bank_photo_path'        => null,
    ];

    $uploadDir = '../public/uploads/personal/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    // Handle each photo upload
    $photoFields = [
        'tin_photo'         => 'tin_photo_path',
        'sss_photo'         => 'sss_photo_path',
        'pagibig_photo'     => 'pagibig_photo_path',
        'philhealth_photo'  => 'philhealth_photo_path',
        'bank_photo'        => 'bank_photo_path',
    ];
    foreach ($photoFields as $inputName => $dataKey) {
        if (!empty($_FILES[$inputName]['name'])) {
            $ext = strtolower(pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','webp'])) {
                $errors[] = ucfirst(str_replace('_', ' ', $inputName)) . ' must be JPG, PNG, or WEBP.';
            } else {
                $filename = $inputName . '_' . $_SESSION['employee_id'] . '_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $uploadDir . $filename)) {
                    $data[$dataKey] = 'uploads/personal/' . $filename;
                }
            }
        }
    }

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
            <?php
            $idReview = [
                'TIN Number'      => ['number'=>$onboarding['tin_number'],      'path'=>$onboarding['tin_photo_path'] ?? ''],
                'SSS Number'      => ['number'=>$onboarding['sss_number'],      'path'=>$onboarding['sss_photo_path'] ?? ''],
                'Pag-IBIG Number' => ['number'=>$onboarding['pagibig_number'],  'path'=>$onboarding['pagibig_photo_path'] ?? ''],
                'PhilHealth Number'=> ['number'=>$onboarding['philhealth_number'],'path'=>$onboarding['philhealth_photo_path'] ?? ''],
            ];
            foreach ($idReview as $label => $val):
            ?>
            <div class="info-row d-flex align-items-center justify-content-between">
                <div>
                    <span class="info-label"><?= $label ?></span>
                    <span class="info-value"><?= htmlspecialchars($val['number']) ?></span>
                </div>
                <?php if (!empty($val['path'])): ?>
                <a href="../public/<?= htmlspecialchars($val['path']) ?>" target="_blank"
                   style="display:flex;align-items:center;gap:6px;text-decoration:none;">
                    <img src="../public/<?= htmlspecialchars($val['path']) ?>" style="height:44px;width:70px;object-fit:cover;border-radius:6px;border:1px solid #e4e4e7;">
                    <span style="font-size:0.75rem;color:#71717a;"><i class="bi bi-eye"></i> View</span>
                </a>
                <?php else: ?>
                <span class="text-muted" style="font-size:0.78rem;"><i class="bi bi-image"></i> No photo</span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
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
                <span class="info-value">
                    <?= htmlspecialchars($onboarding['bank_account_number']) ?>
                    <?php if (!empty($onboarding['bank_photo_path'])): ?>
                        <a href="../public/<?= htmlspecialchars($onboarding['bank_photo_path']) ?>" target="_blank" class="ms-2">
                            <img src="../public/<?= htmlspecialchars($onboarding['bank_photo_path']) ?>" style="max-height:50px;border-radius:4px;border:1px solid #e4e4e7;vertical-align:middle;">
                        </a>
                    <?php endif; ?>
                </span>
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

    <form method="POST" enctype="multipart/form-data">
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
                    <?php
                    $idFields = [
                        ['label'=>'TIN Number','name'=>'tin_number','photo'=>'tin_photo','path'=>'tin_photo_path','placeholder'=>'000-000-000-000'],
                        ['label'=>'SSS Number','name'=>'sss_number','photo'=>'sss_photo','path'=>'sss_photo_path','placeholder'=>'00-0000000-0'],
                        ['label'=>'Pag-IBIG Number','name'=>'pagibig_number','photo'=>'pagibig_photo','path'=>'pagibig_photo_path','placeholder'=>'0000-0000-0000'],
                        ['label'=>'PhilHealth Number','name'=>'philhealth_number','photo'=>'philhealth_photo','path'=>'philhealth_photo_path','placeholder'=>'00-000000000-0'],
                    ];
                    foreach ($idFields as $f):
                    ?>
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="border:1px solid #e4e4e7;background:#fafafa;">
                            <label class="form-label fw-semibold mb-1" style="font-size:0.85rem;"><?= $f['label'] ?> *</label>
                            <input type="text" name="<?= $f['name'] ?>" class="form-control form-control-sm mb-2"
                                   placeholder="<?= $f['placeholder'] ?>"
                                   value="<?= htmlspecialchars($onboarding[$f['name']] ?? '') ?>" required>
                            <div class="upload-area" style="border:1.5px dashed #d4d4d8;border-radius:8px;padding:10px;text-align:center;background:#fff;cursor:pointer;" onclick="document.getElementById('<?= $f['photo'] ?>').click()">
                                <?php if (!empty($onboarding[$f['path']])): ?>
                                    <img src="../public/<?= htmlspecialchars($onboarding[$f['path']]) ?>" id="preview_<?= $f['photo'] ?>" style="max-height:80px;border-radius:6px;object-fit:cover;">
                                    <div class="text-muted mt-1" style="font-size:0.72rem;">Click to replace</div>
                                <?php else: ?>
                                    <div id="preview_<?= $f['photo'] ?>_wrap">
                                        <i class="bi bi-cloud-upload" style="font-size:1.4rem;color:#a1a1aa;"></i>
                                        <div style="font-size:0.75rem;color:#71717a;margin-top:2px;">Click to upload photo</div>
                                    </div>
                                    <img id="preview_<?= $f['photo'] ?>" style="max-height:80px;border-radius:6px;object-fit:cover;display:none;">
                                <?php endif; ?>
                            </div>
                            <input type="file" id="<?= $f['photo'] ?>" name="<?= $f['photo'] ?>" accept="image/*" class="d-none" onchange="previewImg(this,'preview_<?= $f['photo'] ?>','preview_<?= $f['photo'] ?>_wrap')">
                        </div>
                    </div>
                    <?php endforeach; ?>
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
                    <div class="col-12">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.85rem;">Bank Passbook / ATM Card Photo</label>
                        <div class="upload-area" style="border:1.5px dashed #d4d4d8;border-radius:8px;padding:10px;text-align:center;background:#fff;cursor:pointer;max-width:320px;" onclick="document.getElementById('bank_photo').click()">
                            <?php if (!empty($onboarding['bank_photo_path'])): ?>
                                <img src="../public/<?= htmlspecialchars($onboarding['bank_photo_path']) ?>" id="preview_bank_photo" style="max-height:80px;border-radius:6px;object-fit:cover;">
                                <div class="text-muted mt-1" style="font-size:0.72rem;">Click to replace</div>
                            <?php else: ?>
                                <div id="preview_bank_photo_wrap">
                                    <i class="bi bi-credit-card" style="font-size:1.4rem;color:#a1a1aa;"></i>
                                    <div style="font-size:0.75rem;color:#71717a;margin-top:2px;">Click to upload bank photo</div>
                                </div>
                                <img id="preview_bank_photo" style="max-height:80px;border-radius:6px;object-fit:cover;display:none;">
                            <?php endif; ?>
                        </div>
                        <input type="file" id="bank_photo" name="bank_photo" accept="image/*" class="d-none" onchange="previewImg(this,'preview_bank_photo','preview_bank_photo_wrap')">
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

<script>
function previewImg(input, previewId, wrapId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.getElementById(previewId);
            img.src = e.target.result;
            img.style.display = 'block';
            const wrap = document.getElementById(wrapId);
            if (wrap) wrap.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'includes/footer.php'; ?>
