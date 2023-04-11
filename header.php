

    <nav class="navbar fixed-top navbar-expand-md navbar-expand-lg bg-body-tertiary">
      <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">ArtTEXT</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link fw-bold <?php if(basename($_SERVER['PHP_SELF']) == 'index.php') echo 'active' ?>" href="index.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-bold <?php if(basename($_SERVER['PHP_SELF']) == 'upload.php') echo 'active' ?>" href="upload.php">Upload</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-bold <?php if(basename($_SERVER['PHP_SELF']) == 'profile.php') echo 'active' ?>" href="profile.php">Profile</a>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-bold <?php if(basename($_SERVER['PHP_SELF']) == 'setting.php') echo 'active' ?>" href="setting.php">Setting</a>
            </li>
            <li class="nav-item">
              <div class="dropdown">
                <a class="nav-link fw-bold" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Theme
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li class="nav-item">
                    <header class="d-flex justify-content-center py-3">
                      <form method="POST" class="nav-link">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" name="theme" value="dark" <?php if($theme == 'dark') { echo 'checked'; } ?> onchange="this.form.submit()">
                          <label class="form-check-label fw-bold" for="flexSwitchCheckChecked"><i class="bi bi-brightness-high-fill"></i> or <i class="bi bi-moon-stars-fill"></i></label>
                        </div>
                      </form> 
                    </header>
                  </li>
                </ul>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link fw-bold" href="logout.php">Logout</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-mQ93GR66B00ZXjt0YO5KlohRA5SY2XofN4zfuZxLkoj1gXtW8ANNCe9d5Y3eG5eD" crossorigin="anonymous"></script>
    <br><br>