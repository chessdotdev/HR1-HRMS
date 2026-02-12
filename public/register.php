<?php
require_once '../auth/Applicants_account.php';


$applicants_account = new Applicants_account();
$messageErr = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    if (empty(trim($username)) || empty(trim($email)) || empty(trim($password))) {
        $messageErr =  "All fields are required";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $messageErr = "Invalid email format.";
    }
    if (empty($messageErr)) {
        try {
            $create = $applicants_account->createApplicant($username, $email, $password);


            if ($create) {
                echo "Created Successfully";
            } else {
                echo "Failed to create account";
            }
        } catch (PDOException $e) {
            if($e->getCode() == 23000){
                $messageErr = "This email is already registered.";
            }else{
                $err = "Something went wrong. Please try again.";
            }
        }
    }
}

?>


<?php include '../includes/header.php'; ?>
<div class="container mt-5" style="max-width: 400px;">
    <div class="card">
        <div class="card-header">
            <h4>Register</h4>
        </div>
        <div class="card-body">
            <?php if ($messageErr): ?>
                <div class="alert alert-danger"><?php echo $messageErr; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
            </form>
            <p class="mt-3 text-center">Already have an account? <a href="register.php">Register here</a>.</p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>