<?php
require_once("header.php");



$already_logged_in = isset($_SESSION['user_id']);

$login_error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $remember = isset($_POST['remember']);

  $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res->num_rows === 1) {
    $row = $res->fetch_assoc();
    if (password_verify($password, $row['password'])) {
      $_SESSION['user_id'] = $row['id'];

      if ($remember) {
        setcookie("remember_me", $row['id'], time() + 60 * 60 * 24 * 14, "/");
      }

      header("Location: index.php");
      exit();
    } else {
      $_SESSION['flash'] = 'Invalid email or password.';
        header("Location: login.php");
        exit();

    }
  } else {
    $_SESSION['flash'] = 'Invalid email or password.';
       header("Location: login.php");
      exit();
  }
}
?>

<div class="container mt-5">
  <?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-info"><?= $_SESSION['flash']; unset($_SESSION['flash']); ?></div>
  <?php endif; ?>
  
<div class="container mt-5">
  <?php if ($already_logged_in): ?>
    <div class="alert alert-warning">
      You're already logged in<a href="logout.php" class="alert-link">Want to logout?</a>
    </div>
  <?php else: ?>

    <div class="row justify-content-center align-items-center">
      <div class="col-md-6 col-lg-5">
        <h2 class="mb-4">Login</h2>
        <form method="post">
          <div class="mb-3">
            <label for="loginEmail" class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="loginEmail" name="email" required>
          </div>

          <div class="mb-3">
            <label for="loginPassword" class="form-label">Password</label>
            <div class="input-group">
              <input type="password" class="form-control" id="loginPassword" name="password" required>
              <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="bi bi-eye-slash" id="toggleIcon"></i>
              </button>
            </div>
          </div>

          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">Remember Me</label>
          </div>

          <button type="submit" class="btn btn-primary w-100">Login</button>
          <p class="mt-3 text-center">
            Don't have an account? <a href="register.php">Register here</a>
          </p>
        </form>
      </div>

      <div class="col-md-6 text-center d-none d-md-block">
        <div class="logo-wrapper position-relative d-inline-block">
          <img src="images/logo.png" class="img-fluid main-logo" alt="Logo">
          <img src="images/logo1.png" class="img-fluid hover-logo position-absolute top-0 start-0" alt="Hover Logo">
        </div>
      </div>
    </div>

  <?php endif; ?>
</div>

<?php require('footer.php'); ?>
