<?php
$db = new PDO('sqlite:database.db');
$db->exec("CREATE TABLE IF NOT EXISTS users ( id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT NOT NULL, password TEXT NOT NULL)");
$db->exec("CREATE TABLE IF NOT EXISTS posts ( id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT NOT NULL, synopsis TEXT NOT NULL, content TEXT NOT NULL, user_id INTEGER NOT NULL, tags TEXT NOT NULL, date DATETIME, FOREIGN KEY (user_id) REFERENCES users(id))");

$posts_per_page = 100;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_index = ($page - 1) * $posts_per_page;

$query = "SELECT * FROM posts ORDER BY id DESC LIMIT $start_index, $posts_per_page";
$posts = $db->query($query)->fetchAll();

$count_query = "SELECT COUNT(*) FROM posts";
$total_posts = $db->query($count_query)->fetchColumn();
$total_pages = ceil($total_posts / $posts_per_page);
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
  <head>
    <title>ArtTEXT</title>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include('bootstrapcss.php'); ?>
  </head>
  <body>
    <main id="swup" class="transition-main">
    <?php include('header.php'); ?>
    <div class="container-fluid my-4">
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-md-4 g-3">
        <?php foreach ($posts as $post): ?>
          <div class="col">
            <a class="content text-decoration-none" href="view.php?id=<?php echo $post['id'] ?>">
              <div class="card shadow-sm h-100 d-flex justify-content-center align-items-center text-center position-relative">
                <i class="bi bi-book-half display-1 p-5 text-secondary border-bottom w-100"></i>
                <h5 class="border-bottom w-100 p-3"><?php echo $post['title']; ?></h5>
                <div class="card-body">
                  <?php
                    // Get the full description
                    $fullDesc = $post['content'];

                    // Limit the description to 120 characters (words)
                    $limitedDesc = substr($post['content'], 0, 120);

                    // Find the position of the last word within the first 120 characters
                    $lastSpacePos = strrpos($limitedDesc, ' ');

                    // Check if the full description is longer than the limited description
                    if (strlen($post['content']) > strlen($limitedDesc)) {
                      // If it is, add a "full view" link
                      $limitedDesc = substr($limitedDesc, 0, $lastSpacePos).'...';
                    }
                  ?>
                  <p class="card-text text-start"><?php echo $limitedDesc; ?></p>
                  <br>
                  <div class="">
                    <small class="text-body-secondary position-absolute bottom-0 end-0 m-2 fw-medium"><?php echo $post['date']; ?></small>
                  </div>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="pagination mt-4 justify-content-center">
      <?php if ($page > 1): ?>
        <a class="btn btn-sm fw-bold btn-primary me-1" href="?page=<?php echo $page - 1 ?>">Prev</a>
      <?php endif ?>
      <?php if ($page < $total_pages): ?>
        <a class="btn btn-sm fw-bold btn-primary ms-1" href="?page=<?php echo $page + 1 ?>">Next</a>
      <?php endif ?>
    </div>
    <style>
      .contents {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)) /* Two columns with a minimum width of 300px */;
        grid-gap: 10px;
        justify-content: center;
        margin-right: 1px;
        margin-left: 1px;
      }
    </style>
    </main>
    <?php include('bootstrapjs.php'); ?>
  </body>
</html>