<?php
session_start();

$db = new PDO('sqlite:database.db');

// Check if the user is logged in
$user = null;
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Fetch user information if logged in
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

    // Check if the comment is not empty
    if (!empty(trim($comment))) {
      // Insert the comment with the associated page_id
      $stmt = $db->prepare('INSERT INTO comments (username, comment, date, page_id) VALUES (?, ?, ?, ?)');
      $stmt->execute([$username, $comment, date("Y-m-d H:i:s"), $id]);

      // Redirect to prevent form resubmission
      header("Location: view.php?id=$id");
      exit();
    } else {
      // Handle the case where the comment is empty
      echo "<script>alert('Comment cannot be empty.');</script>";
    }
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
    header("Location: view.php?id=$id");
    exit();
  }
}

// Fetch post information
$query = "SELECT posts.id, posts.title, posts.synopsis, posts.content, posts.user_id, posts.tags, posts.date, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = '$id'";
$post = $db->query($query)->fetch();

// Query for the next and previous posts by the same user
$next_post_query = "SELECT id FROM posts WHERE user_id = '$post[user_id]' AND id > '$id' ORDER BY id ASC LIMIT 1";
$next_post = $db->query($next_post_query)->fetch();
$previous_post_query = "SELECT id FROM posts WHERE user_id = '$post[user_id]' AND id < '$id' ORDER BY id DESC LIMIT 1";
$previous_post = $db->query($previous_post_query)->fetch();

// Query to check if there are more than 1 post by the same user
$user_posts_query = "SELECT id FROM posts WHERE user_id = '$post[user_id]'";
$user_posts = $db->query($user_posts_query)->fetchAll();

// Get comments for the current page, ordered by id in descending order
$query = "SELECT * FROM comments WHERE page_id='$id' ORDER BY id DESC LIMIT 10";
$comments = $db->query($query)->fetchAll();
?>
  
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
  <head>
    <title><?php echo $post['title'] ?> by <?php echo isset($post['username']) ? $post['username'] : '' ?></title>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include('bootstrapcss.php'); ?>
  </head>
  <body>
    <main id="swup" class="transition-main">
    <?php include('header.php'); ?>
    <div class="container mt-5">
      <div class="fw-bold">
        <h1 class="text-center fw-bold"><?php echo isset($post['title']) ? $post['title'] : '' ?></h1>
        <p class="mt-5">Author: <?php echo isset($post['username']) ? $post['username'] : '' ?></p>
        <p class="mt-2">Published: <?php echo isset($post['date']) ? $post['date'] : '' ?></p>
        <p class="mt-2">Genre:
          <?php
            if (isset($tag)) {
              echo '<a class="text-decoration-none text-white border-0 btn-sm rounded-pill fw-bold" href="genre.php">All</a> ';
            }

            if (isset($post['tags'])) {
              $tags = explode(',', $post['tags']);
              $totalTags = count($tags);

              foreach ($tags as $index => $tag) {
                $tag = trim($tag);
                $url = 'genre.php?tag=' . urlencode($tag);

                echo '<a class="text-decoration-none text-white border-0 btn-sm rounded-pill fw-bold" href="' . $url . '">' . $tag . '</a>';

                // Add a comma if it's not the last tag
                if ($index < $totalTags - 1) {
                  echo ', ';
                }
              }
            }
          ?>
        </p>
      </div>
      <p class="mt-2 fw-bold">Synopsis:</p> 
      <div class="text-white">
        <p style="white-space: break-spaces; overflow: hidden;">
          <?php
            $novelTextSynopsis = isset($post['synopsis']) ? $post['synopsis'] : ''; // Replace with the desired variable or value

            if (!empty($novelTextSynopsis)) {
              $messageTextSynopsis = $novelTextSynopsis;
              $messageTextWithoutTagsSynopsis = strip_tags($messageTextSynopsis);
              $patternSynopsis = '/\bhttps?:\/\/\S+/i';

              $formattedTextSynopsis = preg_replace_callback($patternSynopsis, function ($matchesSynopsis) {
                $urlSynopsis = htmlspecialchars($matchesSynopsis[0]);
                return '<a href="' . $urlSynopsis . '">' . $urlSynopsis . '</a>';
              }, $messageTextWithoutTagsSynopsis);

              $paragraphs = explode("\n", $formattedTextSynopsis);
    
              foreach ($paragraphs as $paragraph) {
                echo '<p style="white-space: break-spaces; overflow: hidden;">' . $paragraph . '</p>';
              }
            } else {
              echo "No text.";
            }
          ?>
        </p>
        <hr class="border-4 rounded-pill">
        <p style="white-space: break-spaces; overflow: hidden;">
          <?php
            $novelText = isset($post['content']) ? $post['content'] : '';

            if (!empty($novelText)) {
              $paragraphs = explode("\n", $novelText);

              foreach ($paragraphs as $index => $paragraph) {
                $messageTextWithoutTags = strip_tags($paragraph);
                $pattern = '/\bhttps?:\/\/\S+/i';

                $formattedText = preg_replace_callback($pattern, function ($matches) {
                  $url = htmlspecialchars($matches[0]);

                  // Check if the URL ends with .png, .jpg, .jpeg, or .webp
                  if (preg_match('/\.(png|jpg|jpeg|webp)$/i', $url)) {
                    return '<a href="' . $url . '" target="_blank"><img class="img-fluid rounded-4" loading="lazy" src="' . $url . '" alt="Image"></a>';
                  } elseif (strpos($url, 'youtube.com') !== false) {
                    // If the URL is from YouTube, embed it as an iframe with a very low-resolution thumbnail
                    $videoId = getYouTubeVideoId($url);
                    if ($videoId) {
                      $thumbnailUrl = 'https://img.youtube.com/vi/' . $videoId . '/default.jpg';
                      return '<div class="w-100 overflow-hidden position-relative ratio ratio-16x9"><iframe loading="lazy" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" class="rounded-4 position-absolute top-0 bottom-0 start-0 end-0 w-100 h-100 border-0 shadow" src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allowfullscreen></iframe></div>';
                    } else {
                      return '<a href="' . $url . '">' . $url . '</a>';
                    }
                  } else {
                    return '<a href="' . $url . '">' . $url . '</a>';
                  }
                }, $messageTextWithoutTags);

                echo "<p style=\"white-space: break-spaces; overflow: hidden;\">$formattedText</p>";
              }
            } else {
              echo "Sorry, no text...";
            }

            function getYouTubeVideoId($url)
            {
              $videoId = '';
              $pattern = '/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
              if (preg_match($pattern, $url, $matches)) {
                $videoId = $matches[1];
              }
              return $videoId;
            }
          ?>
        </p>
        </br>
        <div class="mb-5"></div>
        <div>
          <?php if ($next_post && isset($next_post['id'])): ?>
            <a class="btn btn-primary btn-md rounded-pill fw-bold position-fixed top-50 start-0 ms-2 z-3" href="view.php?id=<?php echo $next_post['id'] ?>"><i class="bi bi-chevron-left" style="-webkit-text-stroke: 3px;"></i></a>
          <?php endif; ?> 
          <?php if ($previous_post && isset($previous_post['id'])): ?>
            <a class="btn btn-primary btn-md rounded-pill fw-bold position-fixed top-50 end-0 me-2 z-3" href="view.php?id=<?php echo $previous_post['id'] ?>"><i class="bi bi-chevron-right" style="-webkit-text-stroke: 3px;"></i></a>
          <?php endif; ?>
        </div>
      </div>
      <br>
    </div>
    
    <div class="container">
      <hr class="border-4 rounded-pill">
    </div>
    
    <div class="container my-5">
      <!-- Comment form, show only if the user is logged in -->
      <?php if ($user): ?>
        <form method="post" action="view.php?id=<?php echo $id; ?>">
          <div class="mb-3">
            <h5 for="comment" class="form-label fw-bold">Add a comment:</h5>
            <textarea id="comment" name="comment" class="form-control border-top-0 border-start-0 border-end-0 border-4 rounded-0 focus-ring focus-ring-dark" rows="4" onkeydown="if(event.keyCode == 13) { document.execCommand('insertHTML', false, '<br><br>'); return false; }"></textarea>
          </div>
          <button type="submit" class="btn w-100 btn-primary">Submit</button>
        </form>
      <?php else: ?>
        <h5 class="text-center">You must <a href="session.php">login</a> or <a href="session.php">register</a> to send a comment!</h5>
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
                <a href="view.php?action=delete&commentId=<?php echo $comment['id']; ?>&id=<?php echo $id; ?>" style="max-height: 30px;" class="btn btn-danger btn-sm ms-auto">Delete</a>
              <?php endif; ?>
            </div>
            <p><?php echo $displayComment; ?></p>
          </div>
        </div>
      <?php endforeach; ?>
      <a class="btn btn-primary w-100 mt-3" href="comments.php?id=<?php echo $id; ?>">view all comments</a>
    </div>
    </main>
    <?php include('bootstrapjs.php'); ?>
  </body>
</html>
