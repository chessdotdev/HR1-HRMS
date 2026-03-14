<?php
require_once '../config/Database.php';
$db = new Database();
$conn = $db->connect();

$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        
        if (!empty($name)) {
            $query = "INSERT INTO departments (name, description) VALUES (:name, :description)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            
            if ($stmt->execute()) {
                $message = 'Department created successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to create department.';
                $messageType = 'danger';
            }
        }
    } elseif ($action === 'update') {
        $id = (int)$_POST['id'];
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $status = $_POST['status'];
        
        $query = "UPDATE departments SET name = :name, description = :description, status = :status WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            $message = 'Department updated successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to update department.';
            $messageType = 'danger';
        }
    } elseif ($action === 'delete') {
        $id = (int)$_POST['id'];
        
        $query = "DELETE FROM departments WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            $message = 'Department deleted successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to delete department.';
            $messageType = 'danger';
        }
    }
}

// Get all departments
$query = "SELECT * FROM departments ORDER BY name ASC";
$stmt = $conn->prepare($query);
$stmt->execute();
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<div class="main p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Departments</h2>
            <p class="text-muted mb-0">Manage company departments</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
            <i class="bi bi-plus-circle"></i> Add Department
        </button>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($departments)): ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Department Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($departments as $dept): ?>
                                <tr>
                                    <td><?= $dept['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($dept['name']) ?></strong></td>
                                    <td><?= htmlspecialchars($dept['description'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge bg-<?= $dept['status'] === 'Active' ? 'success' : 'secondary' ?>">
                                            <?= htmlspecialchars($dept['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($dept['created_at'])) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editDepartmentModal<?= $dept['id'] ?>">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteDepartmentModal<?= $dept['id'] ?>">
                                            Delete
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editDepartmentModal<?= $dept['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Department</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="action" value="update">
                                                    <input type="hidden" name="id" value="<?= $dept['id'] ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Department Name</label>
                                                        <input type="text" name="name" class="form-control" 
                                                               value="<?= htmlspecialchars($dept['name']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Description</label>
                                                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($dept['description'] ?? '') ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="Active" <?= $dept['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                                            <option value="Inactive" <?= $dept['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="deleteDepartmentModal<?= $dept['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Delete Department</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= $dept['id'] ?>">
                                                    <p>Are you sure you want to delete <strong><?= htmlspecialchars($dept['name']) ?></strong>?</p>
                                                    <p class="text-danger"><small>This action cannot be undone.</small></p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-building" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">No departments created yet</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
                    Create First Department
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="createDepartmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label class="form-label">Department Name *</label>
                        <input type="text" name="name" class="form-control" 
                               placeholder="e.g., Kitchen, Front Desk, Housekeeping" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" 
                                  placeholder="Brief description of the department"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
