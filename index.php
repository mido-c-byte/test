<?php
include 'db.php'; // Include the database connection

// Check if the database connection is working
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle creating a new post
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title']) && isset($_POST['content'])) {
    $title = $_POST['title'];  // ✅ Title is correctly assigned
    $content = $_POST['content'];  // ✅ Content is correctly assigned

    // Debugging: Log values to check if they're swapped
    error_log("Received Title: " . $title);
    error_log("Received Content: " . $content);

    $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $content);
    $stmt->execute();
    $stmt->close();
    exit;
}

// Handle deleting a post
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $post_id = $_POST['delete_id'];

    $stmt = $conn->prepare("DELETE FROM posts WHERE post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->close();
    exit;
}

// Fetch all posts
$result = $conn->query("SELECT * FROM posts ORDER BY post_id DESC");
$posts = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Page</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Post Page</h2>

    <input type="text" id="title" placeholder="Enter Title">
    <textarea id="content" placeholder="Enter Content"></textarea>
    <button onclick="createPost()">Create Post</button>

    <div id="posts">
        <?php foreach ($posts as $post): ?>
            <div id="post-<?= $post['post_id'] ?>" style="border: 1px solid black; padding: 10px; margin: 10px;">
                <h3><?= htmlspecialchars($post['title']) ?></h3> <!-- ✅ Title displayed correctly -->
                <p><?= nl2br(htmlspecialchars($post['content'])) ?></p> <!-- ✅ Content displayed correctly -->
                <button onclick="deletePost(<?= $post['post_id'] ?>)">Delete</button>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        function createPost() {
            var title = $("#title").val();
            var content = $("#content").val();

            if (title.trim() === "" || content.trim() === "") {
                alert("Title and Content cannot be empty!");
                return;
            }

            console.log("Sending:", { title: title, content: content }); // Debugging line

            $.post("index.php", { title: title, content: content }, function() {
                location.reload();
            });
        }

        function deletePost(postId) {
            $.post("index.php", { delete_id: postId }, function() {
                $("#post-" + postId).remove();
            });
        }
    </script>
</body>
</html>
