<?php
$db = new PDO('sqlite:database.db');

$id = $_GET['id'];
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
        <p class="mt-5 small">Author: <?php echo isset($post['username']) ? $post['username'] : '' ?></p>
        <p class="mt-2 small">Published: <?php echo isset($post['date']) ? $post['date'] : '' ?></p>
        <p class="mt-2 small">Genre:
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
      <h5 class="mt-5 fw-bold">Synopsis</h5> 
      <div class="text-white fw-medium">
        <p class="small" style="white-space: break-spaces; line-height: 1.8;">
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
                echo '<p class="small" style="white-space: break-spaces; line-height: 1.8;">' . $paragraph . '</p>';
              }
            } else {
              echo "No text.";
            }
          ?>
        </p>
        <hr class="border-4 rounded-pill">
        <p class="mt-3 small" style="white-space: break-spaces; overflow: hidden;">
          <?php
            $novelText = isset($post['content']) ? $post['content'] : '';

            if (!empty($novelText)) {
              $paragraphs = explode("\n", $novelText);

              foreach ($paragraphs as $index => $paragraph) {
                $messageTextWithoutTags = strip_tags($paragraph);
                $pattern = '/\bhttps?:\/\/\S+/i';

                $formattedText = preg_replace_callback($pattern, function ($matches) {
                  $url = htmlspecialchars($matches[0]);

                  // Check if the URL ends with .png, .jpg, or .webp
                  if (preg_match('/\.(png|jpg|jpeg|webp)$/i', $url)) {
                    return '<img class="img-fluid rounded" loading="lazy" src="' . $url . '" alt="Image">';
                  } else {
                    return '<a href="' . $url . '">' . $url . '</a>';
                  }
                }, $messageTextWithoutTags);

                echo "<p class='small' style=\"white-space: break-spaces; overflow: hidden;\">$formattedText</p>";
              }
            } else {
              echo "Sorry, no text...";
            }
          ?>
        </p>
        </br>
        <div class="mb-5"></div>
        <div id="scrollButton">
          <?php if ($next_post && isset($next_post['id'])): ?>
            <a class="btn btn-primary btn-md rounded-pill fw-bold position-fixed top-50 start-0 rounded-start-0" href="view.php?id=<?php echo $next_post['id'] ?>"><i class="bi bi-chevron-left" style="-webkit-text-stroke: 3px;"></i></a>
          <?php endif; ?> 
          <?php if ($previous_post && isset($previous_post['id'])): ?>
            <a class="btn btn-primary btn-md rounded-pill fw-bold position-fixed top-50 end-0 rounded-end-0" href="view.php?id=<?php echo $previous_post['id'] ?>"><i class="bi bi-chevron-right" style="-webkit-text-stroke: 3px;"></i></a>
          <?php endif; ?>
        </div>
      </div>
      <br>
    </div>
    </main>
    <?php include('bootstrapjs.php'); ?>
  </body>
</html>
