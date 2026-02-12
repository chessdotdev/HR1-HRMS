<?php
 include '../includes/header.php';

require_once '../auth/Applicants_account.php';


if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$applicant_acc = new Applicants_account();
$messageErr = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $messageErr =  "All fields are required";
    } 

 if(empty($messageErr)){
        try {
            $user = $applicant_acc->loginApplicant($username, $password);
        
            if ($user) {
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: dashboard.php");
                exit();
            } else {
                $messageErr = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $messageErr = 'Something went wrong';
        }
    }

  
}
?>


<div class="card mx-auto" style="max-width: 400px;">
    <div class="card-header text-center">
        <h4>Login</h4>
    </div>
    <div class="card-body">
    <p class="text-center mb-3">
          Welcome! Login to apply for available job opportunities. 
    </p>
        <?php if ($messageErr): ?>
            <div class="alert alert-danger"><?php echo $messageErr; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" >
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" >
            </div>
       

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <p class="mt-3 text-center">
                Don't have an account? <a href="login.php">Login</a>
            </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
