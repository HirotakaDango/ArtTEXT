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
            <a class="btn btn-primary btn-md rounded-pill fw-bold position-fixed top-50 start-0 ms-2" href="view.php?id=<?php echo $next_post['id'] ?>"><i class="bi bi-chevron-left" style="-webkit-text-stroke: 3px;"></i></a>
          <?php endif; ?> 
          <?php if ($previous_post && isset($previous_post['id'])): ?>
            <a class="btn btn-primary btn-md rounded-pill fw-bold position-fixed top-50 end-0 me-2" href="view.php?id=<?php echo $previous_post['id'] ?>"><i class="bi bi-chevron-right" style="-webkit-text-stroke: 3px;"></i></a>
          <?php endif; ?>
        </div>
      </div>
      <br>
    </div>
    </main>
    <?php include('bootstrapjs.php'); ?>
  </body>
</html>
