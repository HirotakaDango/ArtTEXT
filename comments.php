<?php
session_start();

$db = new PDO('sqlite:database.db');

// Check if the user is logged in
$user = null;
$id = isset($_GET['id']) ? $_GET['id'] : null;

if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];

  // Fetch user information
  $query = "SELECT * FROM users WHERE id='$user_id'";
  $user = $db->query($query)->fetch();

  // Get the 'id' parameter from the URL
  if (isset($_GET['id'])) {
    $id = $_GET['id'];
  }

  // Handle comment creation
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $username = $user['username'];
    $comment = nl2br(filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW));

    // Insert the comment with the associated page_id
    $stmt = $db->prepare('INSERT INTO comments (username, comment, date, page_id) VALUES (?, ?, ?, ?)');
    $stmt->execute([$username, $comment, date("Y-m-d H:i:s"), $id]);

    // Redirect to prevent form resubmission
    header("Location: comments.php?id=$id");
    exit();
  }

  // Handle comment deletion
  if (
    $_SERVER['REQUEST_METHOD'] === 'GET' &&
    isset($_GET['action']) &&
    $_GET['action'] === 'delete' &&
    isset($_GET['commentId']) && // Use commentId instead of id
    isset($id) &&
    isset($user)
  ) {
    // Delete the comment based on ID and username
    $stmt = $db->prepare('DELETE FROM comments WHERE id = ? AND username = ?');
    $stmt->execute([$_GET['commentId'], $user['username']]);

    // Redirect to prevent form resubmission
    header("Location: comments.php?id=$id");
    exit();
  }
}

// Get comments for the current page, ordered by id in descending order
$query = "SELECT * FROM comments WHERE page_id='$id' ORDER BY id DESC";
$comments = $db->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="transitions.css"/>
    <script type="module" src="swup.js"></script>
  </head>
  <body>
    <div class="container mt-3 mb-5">
      <!-- Comment form, show only if the user is logged in -->
      <?php if ($user): ?>
        <form method="post" action="comments.php?id=<?php echo $id; ?>">
          <div class="mb-3">
            <h5 for="comment" class="form-label fw-bold">Add a comment:</h5>
            <textarea id="comment" name="comment" class="form-control" rows="4" onkeydown="if(event.keyCode == 13) { document.execCommand('insertHTML', false, '<br><br>'); return false; }"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      <?php endif; ?>

      <!-- Display comments -->
      <h5 class="mt-5 mb-2 fw-bold">Comments:</h5>
      <?php foreach ($comments as $comment): ?>
        <div class="card mt-2">
          <div class="card-body">
            <?php
            $displayUsername = isset($comment['username']) ? htmlspecialchars($comment['username']) : 'Unknown';
            $messageText = isset($comment['comment']) ? $comment['comment'] : 'No comment available';
            $messageTextWithoutTags = strip_tags($messageText);
            $pattern = '/\bhttps?:\/\/\S+/i';

            $formattedText = preg_replace_callback($pattern, function ($matches) {
              $url = htmlspecialchars($matches[0]);
              return '<a href="' . $url . '">' . $url . '</a>';
            }, $messageTextWithoutTags);

            $formattedTextWithLineBreaks = nl2br($formattedText);

            $displayComment = $formattedTextWithLineBreaks;
            $displayDate = isset($comment['date']) ? htmlspecialchars($comment['date']) : 'No date available';
            ?>
            <div class="d-flex">
              <p class="fw-bold me-auto">User: <?php echo $displayUsername; ?> | (<small><?php echo $displayDate; ?></small>)</p>
              <?php if ($user && $comment['username'] == $user['username']): ?>
                <a href="comments.php?action=delete&commentId=<?php echo $comment['id']; ?>&id=<?php echo $id; ?>" style="max-height: 30px;" class="btn btn-danger btn-sm ms-auto">Delete</a>
              <?php endif; ?>
            </div>
            <p><?php echo $displayComment; ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <style>
      body::-webkit-scrollbar {
        display: none;
      }

      body {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
      }
    </style>
  </body>
</html>
