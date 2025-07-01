<?php
require_once("header.php");

// Session fallback from cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
  $_SESSION['user_id'] = $_COOKIE['remember_me'];
}

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Validate id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  die("Invalid content ID.");
}

$content_id = (int) $_GET['id'];

// Handle form submission (POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $new_type = in_array($_POST['type'], ['Movie', 'Series']) ? $_POST['type'] : null;
  $new_status = isset($_POST['watched']) ? 'Watched' : 'Not Watched';

  if ($new_type) {
    $update = $conn->prepare("UPDATE watchlists SET type = ?, status = ? WHERE id = ? AND user_id = ?");
    $update->bind_param("ssii", $new_type, $new_status, $content_id, $user_id);
    $update->execute();
    header("Location: content.php?id=" . $content_id);
    exit();
  }
}

// Fetch content
$stmt = $conn->prepare("SELECT * FROM watchlists WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $content_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
  die("Content not found or access denied.");
}

$content = $result->fetch_assoc();
?>

<div class="container mt-5">
  <h2 class="mb-4"><?= htmlspecialchars($content['title']) ?></h2>
  <div class="row">
    <div class="col-md-4 text-center mb-4 mb-md-0">
      <?php
        $poster = !empty($content['image_url']) ? htmlspecialchars($content['image_url']) : 'images/placeholder.png';
      ?>
      <img src="<?= $poster ?>" class="img-fluid rounded shadow-sm hover-gray" alt="Content Poster">
    </div>
    <div class="col-md-8">
      <form method="post">
        <table class="table table-bordered table-striped content-form-table">
          <tbody>
            <tr>
              <th scope="row" class="w-25">Description</th>
              <?php if (!empty($content['description'])): ?>
                <td><?= nl2br(htmlspecialchars($content['description'])) ?></td>
              <?php else: ?>
                <td class="text-muted">No description provided.</td>
              <?php endif; ?>
            </tr>
            <tr>
              <th scope="row">Type</th>
              <td>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="type" value="Movie" id="typeMovie"
                    <?= $content['type'] === 'Movie' ? 'checked' : '' ?>>
                  <label class="form-check-label" for="typeMovie">Movie</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="type" value="Series" id="typeSeries"
                    <?= $content['type'] === 'Series' ? 'checked' : '' ?>>
                  <label class="form-check-label" for="typeSeries">Series</label>
                </div>
              </td>
            </tr>
            <tr>
              <th scope="row">Date Added</th>
              <td><?= htmlspecialchars($content['date_added']) ?></td>
            </tr>
            <tr>
              <th scope="row">Recommended By</th>
              <td><?= htmlspecialchars($content['recommended_by']) ?></td>
            </tr>
            <tr>
              <th scope="row">Watched</th>
              <td>
                <input class="form-check-input me-2" type="checkbox" name="watched" id="watched"
                  <?= $content['status'] === 'Watched' ? 'checked' : '' ?>>
                <label class="form-check-label" for="watched">Yes</label>
              </td>
            </tr>
          </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Save Changes</button>
      </form>
    </div>
  </div>
</div>

<?php require('footer.php'); ?>
