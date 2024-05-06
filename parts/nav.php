<link rel="stylesheet" href="../css/nav.css">

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <!-- Container wrapper -->
    <div class="container-fluid">
        <!-- Toggle button -->
        <button class="navbar-toggler" type="button" data-mdb-toggle="collapse" data-mdb-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>
        <!-- Collapsible wrapper -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Navbar brand -->
            <a class="navbar-brand mt-2 mt-lg-0" href="#"></a>
            <!-- Left links -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <img src="https://www.vectorkhazana.com/assets/images/products/Lion-head.jpg" style="width: 30px">
                        Coders Blog
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"></a>
                </li>
                <?php
                if (isset($_SESSION['id'])) :
                ?>
                    <li>
                        <a class="nav-link" href="http://localhost/Coders/project/client/blog.php"><i class="fa-solid fa-house "></i> Home</a>
                    </li>
                    <li class="nav_link nav-item">
                        <a href="" class="nav-link"><i class="fa-brands fa-hive "></i> Blog</a>
                        <ul class="navDropdown">
                            <li class="dropLi">
                                <a href="http://localhost/Coders/project/blog/home.php">Main Info</a>
                            </li>
                            <li class="dropLi">
                                <a href="http://localhost/Coders/project/blog/homeCard.php">Card</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://localhost/Coders/project/category/home.php"><i class="fa-solid fa-bars"></i> Categories</a>
                    </li>

                <?php
                endif;
                ?>
            </ul>
            <!-- Left links -->
        </div>

        <div class="d-flex align-items-center">
            <?php
            if (!isset($_SESSION['id'])) :
            ?>
                <div>
                    <!-- Icon -->
                    <a class="text-reset me-3" href="../auth/register.php" style="text-decoration: none">
                        <i class="fa-solid fa-pen-to-square"></i>
                        Register
                    </a>
                </div>
                <div>
                    <a class="text-reset me-3" href="../auth/login.php" style="text-decoration: none">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Login
                    </a>
                </div>
            <?php
            endif;
            if (isset($_SESSION['id'])) {
            ?>
                <a class="text-reset me-3" href="../profile/editUser.php" style="text-decoration: none">
                    <?php
                    if (!isset($_SESSION['register_img'])) :
                    ?>
                        <i class="fa-solid fa-user"></i>
                    <?php
                    endif;
                    if (isset($_SESSION['register_img'])) :
                    ?>
                        <img src="<?= $domain . "/" . $_SESSION['register_img'] ?>" style="object-fit:cover; border-radius: 50%" width="45px" height="45px" alt="">
                    <?php
                    endif;
                    ?>
                    <?= $_SESSION['fullname'] ?>
                </a>
                <a class="text-reset me-3" href="../auth/logout.php" style="text-decoration: none">

                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout
                </a>
            <?php
            }
            ?>
        </div>
    </div>
</nav>