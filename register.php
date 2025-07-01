<?php
require_once("header.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $confirm = $_POST['confirm'];

  if (empty($email) || empty($password) || empty($confirm)) {
    $_SESSION['flash'] = "All fields are required.";
    header("Location: register.php");
    exit();
  } elseif ($password !== $confirm) {
    $_SESSION['flash'] = "Passwords do not match.";
    header("Location: register.php");
    exit();
  } else {
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
      $_SESSION['flash'] = "Email already registered.";
      header("Location: register.php");
      exit();
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $insert = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
      $insert->bind_param("ss", $email, $hash);
      if ($insert->execute()) {
        $_SESSION['flash'] = "Registration succeeded. You can now log in.";
        header("Location: login.php");
        exit();
      } else {
        $_SESSION['flash'] = "Database error. Please try again.";
        header("Location: register.php");
        exit();
      }
    }
  }
}
?>

<div class="container mt-5">
  <?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-info"><?= $_SESSION['flash']; unset($_SESSION['flash']); ?></div>
  <?php endif; ?>

  <h2 class="mb-4">Register</h2>

  <form method="post">
    <div class="row">
      <div class="mb-3 col-md-6">
        <label for="firstName" class="form-label">First Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="firstName" name="firstName" required>
      </div>
      <div class="mb-3 col-md-6">
        <label for="lastName" class="form-label">Last Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="lastName" name="lastName" required>
      </div>
    </div>
    <div class="mb-3">
      <label for="registerEmail" class="form-label">Email <span class="text-danger">*</span></label>
      <input type="email" class="form-control" id="registerEmail" name="email" required>
    </div>

    <div class="mb-3">
      <label for="registerPassword" class="form-label">Password <span class="text-danger">*</span></label>
      <div class="input-group">
        <input type="password" class="form-control" id="registerPassword" name="password" required>
        <button class="btn btn-outline-secondary" type="button" id="toggleRegisterPassword">
          <i class="bi bi-eye-slash" id="toggleIconRegister"></i>
        </button>
      </div>
    </div>

    <div class="mb-3">
      <label for="confirmPassword" class="form-label">Confirm Password <span class="text-danger">*</span></label>
      <div class="input-group">
        <input type="password" class="form-control" id="confirmPassword" name="confirm" required>
        <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
          <i class="bi bi-eye-slash" id="toggleIconConfirm"></i>
        </button>
      </div>
    </div>

    <button type="submit" class="btn btn-success w-100">Register</button>

    <p class="mt-3 text-center">
      Already have an account? 
      <a href="login.php" class="text-decoration-none fw-semibold">Login here</a>
    </p>
  </form>
</div>

<?php require('footer.php'); ?>
