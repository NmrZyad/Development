<?php
mysqli_report(MYSQLI_REPORT_OFF);

// Config
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hw2_212018535_212016406"; 

// 1. Connect 
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Create DB
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if (!$conn->query($sql)) {
    die("Error creating database: " . $conn->error);
}
echo "✅ Database created successfully<br>";

// 3. Use DB
$conn->select_db($dbname);

// 4. Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
)";
$conn->query($sql);

// 5. Create watchlists table
$sql = "CREATE TABLE IF NOT EXISTS watchlists (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  image_url VARCHAR(512),
  type ENUM('Movie', 'Series') NOT NULL,
  date_added DATE DEFAULT CURRENT_DATE,
  recommended_by VARCHAR(255),
  status ENUM('Watched', 'Not Watched') DEFAULT 'Not Watched',
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
$conn->query($sql);

// 6. Insert default user (hashed password)
$default_email = 'test2025@test.com';
$default_password = password_hash('test2025', PASSWORD_DEFAULT);

// Only insert if user not already exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $default_email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $default_email, $default_password);
    $stmt->execute();
    $user_id = $conn->insert_id;


    // 7. Add 3–4 example entries
$stmt = $conn->prepare("INSERT INTO watchlists 
  (user_id, title, description, image_url, type, date_added, recommended_by, status) 
  VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

// Sample content items (title, description, image, type, status, recommended_by)
$sample_entries = [
    [
        "Inception",
        "A thief who steals corporate secrets through dream-sharing technology.",
        "images/inceptionposter.png",
        "Movie",
        "Watched",
        "Jane Smith"
    ],
    [
        "Stranger Things",
        "A group of kids uncovering secret experiments and supernatural forces.",
        "images/strangerthings.jpg",
        "Series",
        "Watched",
        "John Doe"
    ],
    [
        "Breaking Bad",
        "A high school chemistry teacher turned methamphetamine producer.",
        "images/breakingbad.webp",
        "Series",
        "Not Watched",
        "Mike Ross"
    ],
    [
        "Interstellar",
        "A team of explorers travel through a wormhole in space to save humanity.",
        "images/interstellar.jpg",
        "Movie",
        "Watched",
        "Elliot Page"
    ]
];

// Bind parameters
$stmt->bind_param("isssssss", $user_id, $title, $description, $image_url, $type, $date_added, $recommended_by, $status);

$user_id = 1;
$date_added = date("Y-m-d");

foreach ($sample_entries as $entry) {
    list($title, $description, $image_url, $type, $status, $recommended_by) = $entry;
    $stmt->execute();
}

} 


// ✅ Redirect to login page after setup
header("Location: login.php");
exit();
?>
