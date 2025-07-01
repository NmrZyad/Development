<?php
require_once("header.php");

// Restore session from remember_me cookie
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
  $_SESSION['user_id'] = $_COOKIE['remember_me'];
}

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// Handle new content submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['new_title'])) {
  $new_title = trim($_POST['new_title']);
  $responsible = trim($_POST['responsible']);
  $type = $_POST['type'];
  $status = isset($_POST['watched']) ? 'Watched' : 'Not Watched';
  $poster_url = trim($_POST['poster_url']);
  $description = trim($_POST['description']);

  if (!empty($new_title) && in_array($type, ['Movie', 'Series'])) {
    $insert = $conn->prepare("INSERT INTO watchlists (user_id, title, type, status, recommended_by, image_url, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insert->bind_param("issssss", $user_id, $new_title, $type, $status, $responsible, $poster_url, $description);
    $insert->execute();
    header("Location: index.php");
    exit();
  }
}

// Fetch user's watchlist
$stmt = $conn->prepare("SELECT id, title, type, date_added, status, recommended_by FROM watchlists WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-5">
  <h2 class="mb-4">My WatchList</h2>

  <!-- Toggle Add Form Button -->
  <button class="btn btn-outline-success mb-3" onclick="toggleAddForm()">➕ Add New Content</button>

  <!-- Add New Content Form (Initially Hidden) -->
  <div id="addFormContainer" class="p-3 border rounded mb-4" style="display: none;">
    <h4>Add New Content</h4>
    <form method="post" class="row g-3">
      <div class="col-md-6">
        <label for="new_title" class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="new_title" name="new_title" required>
      </div>
      <div class="col-md-6">
        <label for="responsible" class="form-label">Recommended By</label>
        <input type="text" class="form-control" id="responsible" name="responsible">
      </div>
      <div class="col-md-6">
        <label for="poster_url" class="form-label">Image URL</label>
        <input type="url" class="form-control" id="poster_url" name="poster_url" placeholder="https://...">
      </div>
      <div class="col-md-6">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="1"></textarea>
      </div>
      <div class="col-md-4">
        <label class="form-label">Type <span class="text-danger">*</span></label><br>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="type" id="movie" value="Movie" checked>
          <label class="form-check-label" for="movie">Movie</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="type" id="series" value="Series">
          <label class="form-check-label" for="series">Series</label>
        </div>
      </div>
      <div class="col-md-4 d-flex align-items-end">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="watched" id="watched">
          <label class="form-check-label" for="watched">Watched</label>
        </div>
      </div>
      <div class="col-md-4 d-flex align-items-end">
        <button type="submit" class="btn btn-success">Add to Watchlist</button>
      </div>
    </form>
  </div>

  <!-- WatchList Table -->
  <table class="table table-hover">
    <thead class="table-dark">
      <tr>
        <th>Title</th>
        <th>Type</th>
        <th>Date Added</th>
        <th>Status</th>
        <th>Recommended By</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><a href="content.php?id=<?= $row['id'] ?>"><?= htmlspecialchars($row['title']) ?></a></td>
          <td><?= htmlspecialchars($row['type']) ?></td>
          <td><?= htmlspecialchars($row['date_added']) ?></td>
          <td><?= htmlspecialchars($row['status']) ?></td>
          <td><?= htmlspecialchars($row['recommended_by']) ?></td>
          <td>
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#recommendModal">
              Recommend
            </button>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Modal -->
<div class="modal fade" id="recommendModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Add a Friend Recommendation</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label for="friendEmail" class="form-label">Friend's Email</label>
          <input type="email" class="form-control" id="friendEmail" required>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Add Friend</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- JS for toggle animation -->
<script>
function toggleAddForm() {
  const form = document.getElementById('addFormContainer');
  if (form.style.display === 'none') {
    form.style.display = 'block';
    form.classList.add('fade-in');
  } else {
    form.classList.remove('fade-in');
    form.style.display = 'none';
  }
}
</script>

<!-- Optional: Add animation style -->
<style>
.fade-in {
  animation: fadeInSlideDown 0.5s ease-in-out;
}
@keyframes fadeInSlideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>

<?php require('footer.php'); ?>
