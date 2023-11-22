<?php
session_start();
$db = new PDO('sqlite:database.db');
if (!isset($_SESSION['user_id'])) {
  header('Location: session.php');
}
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id='$user_id'";
$user = $db->query($query)->fetch();

$per_page = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start_from = ($page - 1) * $per_page;

$query = "SELECT * FROM posts WHERE user_id='$user_id' ORDER BY id DESC LIMIT $start_from, $per_page";
$posts = $db->query($query)->fetchAll();

$query = "SELECT COUNT(*) as total FROM posts WHERE user_id='$user_id'";
$total_posts = $db->query($query)->fetchColumn();
$total_pages = ceil($total_posts / $per_page);
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
  <head>
    <title><?php echo $user['username'] ?></title>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php include('bootstrapcss.php'); ?>
  </head>
  <body>
    <main id="swup" class="transition-main">
    <?php include('header.php'); ?>
    <div class="mt-3">
      <div class="container-fluid">
        <div class="card border-4 p-3 gap-3 rounded-4">
          <h3 class="fw-bold">Name: <?php echo $user['username']; ?></h3>
          <h3 class="fw-bold">Posts: <?php echo $total_posts; ?></h3>
          <button class="btn btn-outline-light border-2 rounded fw-medium" style="width: 100px;" type="button" data-bs-toggle="modal" data-bs-target="#logOut">log out</button>
        </div>
        <h3 class="fw-bold my-3">Your Works</h3>
      </div>
      <div class="modal fade" id="logOut" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content rounded-3 shadow">
            <div class="modal-body p-4 text-center">
              <h5 class="mb-0">Are you sure want to log out?</h5>
            </div>
            <div class="modal-footer flex-nowrap p-0">
              <a href="logout.php" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0 border-end"><strong>Yes, I want!</strong></a>
              <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 py-3 m-0 rounded-0" data-bs-dismiss="modal">No, keep it!</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid my-4">
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-md-4 g-2">
        <?php foreach ($posts as $post): ?>
          <div class="col">
            <a class="content text-decoration-none" href="view.php?id=<?php echo $post['id'] ?>">
              <div class="card shadow-sm h-100 position-relative">
                <div class="d-flex justify-content-center align-items-center text-center">
                  <i class="bi bi-book-half display-1 p-5 text-secondary border-bottom w-100"></i>
                </div>
                <h5 class="border-bottom text-center w-100 p-3"><?php echo $post['title']; ?></h5>
                <div class="card-body">
                  <?php
                    // Get the full description
                    $fullDesc = $post['synopsis'];

                    // Limit the description to 120 characters (words)
                    $limitedDesc = substr($post['synopsis'], 0, 120);

                    // Find the position of the last word within the first 120 characters
                    $lastSpacePos = strrpos($limitedDesc, ' ');

                    // Check if the full description is longer than the limited description
                    if (strlen($post['synopsis']) > strlen($limitedDesc)) {
                      // If it is, add a "full view" link
                      $limitedDesc = substr($limitedDesc, 0, $lastSpacePos).'...';
                    }
                  ?>
                  <p class="card-text text-start"><?php echo $limitedDesc; ?></p>
                  <br><br>
                  <div class="">
                    <div class="btn-group position-absolute bottom-0 start-0 m-2">
                      <button onclick="location.href='view.php?id=<?php echo $post['id'] ?>'" class="btn btn-sm btn-outline-secondary fw-medium">View</button>
                      <button onclick="location.href='edit.php?id=<?php echo $post['id'] ?>'" class="btn btn-sm btn-outline-secondary fw-medium">Edit</button>
                    </div>
                    <small class="text-body-secondary position-absolute bottom-0 end-0 m-2 fw-medium"><?php echo $post['date']; ?></small>
                  </div>
                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="pagination my-4 justify-content-center gap-2">
      <?php if ($page > 1): ?>
        <a class="btn btn-sm fw-bold btn-primary" href="?page=<?php echo $page - 1 ?>">Prev</a>
      <?php endif ?>

      <?php
      $start_page = max(1, $page - 2);
      $end_page = min($total_pages, $page + 2);

      for ($i = $start_page; $i <= $end_page; $i++):
      ?>
        <a class="btn btn-sm fw-bold btn-primary <?php echo ($i == $page) ? 'active' : ''; ?>" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
      <?php endfor ?>

      <?php if ($page < $total_pages): ?>
        <a class="btn btn-sm fw-bold btn-primary" href="?page=<?php echo $page + 1 ?>">Next</a>
      <?php endif ?>
    </div>
    </main>
    <?php include('bootstrapjs.php'); ?>
  </body>
</html>