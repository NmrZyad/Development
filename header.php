<?php
// ✅ Start session only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('flash')) {
    function flash($key) {
        if (isset($_SESSION[$key])) {
            $msg = $_SESSION[$key];
            unset($_SESSION[$key]); // remove after first use
            return $msg;
        }
        return null;
    }
}

// ✅ DB connection required for email fetching
require_once("db.php");

// Restore session from remember_me cookie if session is missing only if valid in DB
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $_COOKIE['remember_me']);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 1) {
        $_SESSION['user_id'] = $_COOKIE['remember_me'];
    } else {
        // ❌ Invalid user ID: delete cookie, redirect to login
        setcookie("remember_me", "", time() - 3600, "/");
        header("Location: login.php");
        exit(); // 🔒 Always call exit() after redirect!
    }
}



// ✅ Helper function to get user's email by ID
if (!function_exists('getUserEmail')) {
    function getUserEmail($user_id, $conn) {
        $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['email'] ?? 'User';
    }
}
?>

<?php if (isset($_SESSION['flash'])): ?>
  <?php
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    $is_error = str_contains(strtolower($flash), 'invalid') ||
                str_contains(strtolower($flash), 'not match') ||
                str_contains(strtolower($flash), 'required') ||
                str_contains(strtolower($flash), 'already') ||
                str_contains(strtolower($flash), 'error');
  ?>
  <div class="alert <?= $is_error ? 'alert-danger' : 'alert-info' ?> custom-flash shadow-sm text-center">
    <?= htmlspecialchars($flash) ?>
  </div>
<?php endif; ?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>WatchList</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body class="">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">
      <img src="images/logo.png" alt="Logo" class="logo-navbar me-2"> WatchList
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item me-2">
          <a class="nav-link text-white" href="index.php">
            <i class="bi bi-house-door-fill me-1"></i> Home
          </a>
        </li>
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item dropdown me-2">
            <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars(getUserEmail($_SESSION['user_id'], $conn)) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item me-2">
            <a class="nav-link text-white" href="login.php">
              <i class="bi bi-box-arrow-in-right me-1"></i> Login
            </a>
          </li>
        <?php endif; ?>
        <li class="nav-item">
          <button class="btn btn-outline-light btn-sm" id="themeToggle" title="Toggle Dark Mode">
            <i class="bi bi-moon-fill" id="themeIcon"></i>
          </button>
        </li>
      </ul>
    </div>
  </div>
</nav>

<script>
  const toggleBtn = document.getElementById('themeToggle');
  const icon = document.getElementById('themeIcon');
  const body = document.body;

  // Load saved theme
  if (localStorage.getItem('theme') === 'dark') {
    body.classList.add('dark-mode');
    icon.classList.replace('bi-moon-fill', 'bi-sun-fill');
  }

  toggleBtn?.addEventListener('click', () => {
    const isDark = body.classList.toggle('dark-mode');
    icon.classList.toggle('bi-moon-fill');
    icon.classList.toggle('bi-sun-fill');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
  });
</script>