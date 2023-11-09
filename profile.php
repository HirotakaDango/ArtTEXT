<?php
session_start();
$db = new PDO('sqlite:database.db');
if (!isset($_SESSION['user_id'])) {
    header('Location: session.php');
}
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id='$user_id'";
$user = $db->query($query)->fetch();

$per_page = 100;
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="transitions.css" />
    <script type="module" src="swup.js"></script>
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
      <div class="container-fluid text-center">
        <div class="contents">
          <?php foreach ($posts as $post): ?>
            <div class="content card border border-2 h-100">
              <a class="border-bottom display-1 mt-1" href="view.php?id=<?php echo $post['id'] ?>"><i class="bi bi-book-half text-secondary"></i></a>
              <a class="me-1 ms-1 mt-1 mb-1 text-secondary text-decoration-none fw-bold" href="view.php?id=<?php echo $post['id'] ?>"><?php echo $post['title'] ?></a>
              <header class="d-flex justify-content-center py-3">
                <ul class="nav nav-pills">
                  <li class="nav-item btn-sm"><a class="btn btn-sm btn-secondary me-1" href="delete.php?id=<?php echo $post['id'] ?>" onclick="return confirm('Are you sure?')"><i class="bi bi-trash-fill"></i></a></li>
                  <li class="nav-item"><a class="btn btn-sm btn-secondary ms-1" href="edit.php?id=<?php echo $post['id'] ?>"><i class="bi bi-pencil-fill"></i></a></li>
                </ul>
              </header>
            </div>
          <?php endforeach ?> 
        </div>
      </div>
    </div>
    <div class="pagination mt-4 justify-content-center">
      <?php if ($page > 1): ?>
        <a class="btn btn-sm fw-bold btn-primary" href="?page=<?php echo $page - 1 ?>">Prev</a>
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
  </body>
</html>