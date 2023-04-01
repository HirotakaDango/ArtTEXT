<?php
session_start();
$db = new PDO('sqlite:database.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->exec("CREATE TABLE IF NOT EXISTS users ( id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT NOT NULL, password TEXT NOT NULL)");
$db->exec("CREATE TABLE IF NOT EXISTS posts ( id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT NOT NULL, synopsis TEXT NOT NULL, content TEXT NOT NULL, user_id INTEGER NOT NULL, cover BLOB NOT NULL, tags TEXT NOT NULL, FOREIGN KEY (user_id) REFERENCES users(id))");

if (isset($_POST['login'])) {
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  if (empty($username) || empty($password)) {
    echo 'Please enter username and password';
    exit;
  }

  $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
  $user = $db->query($query)->fetch();

  if ($user) {
    $_SESSION['user_id'] = $user['id'];
    header('Location: index.php');
    exit;
  } else {
    echo 'Invalid username or password';
    exit;
  }
} elseif (isset($_POST['register'])) {
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  if (empty($username) || empty($password)) {
    echo 'Please enter username and password';
    exit;
  }

  $query = "SELECT * FROM users WHERE username='$username'";
  $user = $db->query($query)->fetch();

  if ($user) {
    echo 'Username already taken';
    exit;
  }

  $query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
  $db->exec($query);

  $_SESSION['user_id'] = $db->lastInsertId();
  header('Location: index.php');
  exit;
}
?>

<!DOCTYPE html>
<html>
  <head>
    <title>ArtTEXT</title>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  </head>
  <body>
    <div class="container mt-5">
      <h1 class="mb-5 text-center fw-bold">Login or Register</h1>
      <form class="container-md container-lg" method="post">
        <div class="form-floating mb-3">
          <input type="text" name="username" class="form-control rounded-3" id="floatingInput" placeholder="Username" required>
          <label for="floatingInput">Username</label>
        </div>
        <div class="form-floating mb-3">
          <input type="password" name="password" class="form-control rounded-3" id="floatingPassword" placeholder="Password" required>
          <label for="floatingPassword">Password</label>
        </div>
        <center>
          <button class="btn btn-primary fw-bold" type="submit" name="login">Login</button>
          <button class="btn btn-primary fw-bold" type="submit" name="register">Register</button>
        </center>
      </form>
    </div>
  </body>
</html>