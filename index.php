<?php
require_once("db.php");
require_once("header.php");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $user_id = $_SESSION['user_id'];
  $title = $_POST["new_title"];
  $description = $_POST["description"];
  $type = $_POST["type"];
  $recommended_by = $_POST["recommended_by"];
  $status = isset($_POST["watched"]) ? "Watched" : "Not Watched";
  $date_added = date("Y-m-d");

  // Handle image upload
  $image_path = null;
  if (isset($_FILES['poster_url']) && $_FILES['poster_url']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = "images/";
    $fileName = uniqid() . "_" . basename($_FILES["poster_url"]["name"]);
    $targetFilePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES["poster_url"]["tmp_name"], $targetFilePath)) {
      $image_path = $targetFilePath;
    }
  }

  // Insert into DB
  $stmt = $conn->prepare("INSERT INTO watchlists (user_id, title, description, image_url, type, date_added, recommended_by, status)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("isssssss", $user_id, $title, $description, $image_path, $type, $date_added, $recommended_by, $status);
  $stmt->execute();

  $_SESSION['flash'] = "Content added successfully.";
  header("Location: index.php");
  exit();
}

// Fetch current user's watchlist
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM watchlists WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$watchlist = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="container mt-4">
  <h2 class="mb-4">My Watchlist</h2>

  <!-- Add Button -->
  <button class="btn btn-primary mb-3" onclick="document.getElementById('addFormContainer').style.display='block'">+ Add New</button>

  <!-- Add Form -->
  <div id="addFormContainer" class="p-3 border rounded mb-4" style="display: none;">
    <h4>Add New Content</h4>
    <form method="post" enctype="multipart/form-data" class="row g-3">
      <div class="col-md-6">
        <label for="new_title" class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="new_title" name="new_title" required>
      </div>

      <div class="col-md-6">
        <label for="recommended_by" class="form-label">Recommended By</label>
        <input type="text" class="form-control" id="recommended_by" name="recommended_by">
      </div>

      <div class="col-md-6">
        <label for="poster_url" class="form-label">Upload Image</label>
        <input type="file" class="form-control" id="poster_url" name="poster_url" accept="image/*" required>
      </div>

      <div class="col-md-6">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
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

  <!-- Content Table -->
  <table class="table table-striped table-bordered">
    <thead class="table-dark">
      <tr>
        <th>Image</th>
        <th>Title</th>
        <th>Description</th>
        <th>Type</th>
        <th>Recommended By</th>
        <th>Watched</th>
        <th>Date Added</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($watchlist as $item): ?>
        <tr>
          <td><img src="<?= htmlspecialchars($item['image_url']) ?>" width="80" height="120" alt="Poster"></td>
<td>
<a href="content.php?id=<?= $item['id'] ?>">
    <?= htmlspecialchars($item['title']) ?>
  </a>
</td>
          <td><?= htmlspecialchars($item['description']) ?></td>
          <td><?= $item['type'] ?></td>
          <td><?= htmlspecialchars($item['recommended_by']) ?></td>
          <td><?= $item['status'] === "Watched" ? "✔️" : "❌" ?></td>
          <td><?= $item['date_added'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once("footer.php"); ?>
