<?php

include '../parts/index.php';

$isFilter = false;
$sqlWhere = ' WHERE ';
$executeArray = [];

if ($_GET['title'] ?? null != null) {
    $sqlWhere .= "b.title like ?";
    $isFilter = true;
    $executeArray[] = '%' . $_GET['title'] . '%';
}

if ($_GET['creator'] ?? null != null) {

    if ($isFilter) {
        $sqlWhere .= ' and ';
    }
    $sqlWhere .= "u.fullname like ? ";
    $isFilter = true;
    $executeArray[] = '%' . $_GET['creator'] . '%';
}

if ($_GET['category'] ?? null != null) {

    if ($isFilter) {
        $sqlWhere .= ' and ';
    }

    $sqlWhere .= "b.category_id = ? ";
    $isFilter = true;
    $executeArray[] = $_GET['category'];
}

if (!$isFilter) {
    $sqlWhere = ' ';
}

$page = 1;

if (is_numeric(($_GET['page'] ?? 0)) && ($_GET['page'] ?? 0) >= 1) {
    $page = $_GET['page'];
}

$limit = 5;
$offset = ($page - 1) * $limit;

$sql = "
  SELECT
     b.id,
     b.body,    
     b.title,
     b.created_at,
     b.created_by,
     b.cover_img,
     b.view,
     u.fullname as creator,
     c.name as category
  FROM blog as b
  LEFT JOIN categories as c on c.id = b.category_id
  LEFT JOIN users as u on u.id = b.created_by
  $sqlWhere
  ORDER BY b.id desc
  LIMIT $limit OFFSET $offset
";

$blogQuerry = sqlConnetion()->prepare($sql);
$blogQuerry->execute($executeArray);

$blogs = $blogQuerry->fetchAll(PDO::FETCH_ASSOC);

$totalQuerry = sqlConnetion()->prepare("
    SELECT COUNT(*) as count 
    FROM blog as b
    LEFT JOIN categories as c on c.id = b.category_id
    LEFT JOIN users as u on u.id = b.created_by
    $sqlWhere
");

$totalQuerry->execute($executeArray);
$total = $totalQuerry->fetch(PDO::FETCH_ASSOC);

$pageCount = ceil($total['count'] / $limit);

$categoriesQuery = sqlConnetion()->prepare(
    "SELECT id, name FROM categories"
);

$categoriesQuery->execute([]);

$category = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <div class="d-flex justify-content-between align-item-center py-3">
        <h3>Blogs</h3>
        <a href="http://localhost/Coders/project/blog/create.php" class="btn btn-primary">Add New</a>
    </div>
    <form action="" method="GET">
        <div class="row my-4">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" value="<?= $_GET['title'] ?? null ?>" class="form-control">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Creator</label>
                    <input type="text" name="creator" value="<?= $_GET['creator'] ?? null ?>" class="form-control">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Category</label>
                    <select name="category" class="form-control" id="">
                        <option value="">-------</option>
                        <?php
                        foreach ($category as $cat) :
                            $selected = '';
                            if ($cat['id'] == $_GET['category'] ?? 0) {
                                $selected = 'selected';
                            } else {
                                $selected = '';
                            }
                        ?>
                        <option <?= $selected ?> value="<?= $cat['id'] ?> "><?= $cat['name'] ?></option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div style="visibility: hidden">a</div>
                <button class="btn btn-sm btn-info" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </div>
    </form>

    <link rel="stylesheet" href="../css/blog.css">

    <div class="container">
        <div class="row">
            <?php
            foreach ($blogs as $blog) :
                $img = $domain . '/' . $blog['cover_img'];
            ?>
            <div class="col-3">
                <div class="card my-3" style="width: 18rem;">
                    <div class="card_img">
                        <img width="" src="<?= $img ?>" class="card-img-top" alt="...">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><a href="blogDetails.php?id=<?= $blog['id'] ?>   "
                                style="text-decoration:none"><?= $blog['title'] ?></a></h5>
                        <p class="card-text"><?= $blog['body'] ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="#" class="btn btn-primary">Details</a>
                            <div class="eyeIcon">
                                <i class="fa-solid fa-eye"></i> <?= $blog['view'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            endforeach;
            ?>
        </div>
    </div>

    <nav aria-label="Page navigation example" class="mt-4">
        <ul class="pagination">
            <?php
            if ($page != 1) :
            ?>
            <li class="page-item"><a class="page-link"
                    href="http://localhost/Coders/project/blog/home.php?page=<?= $page - 1 ?>">Previous</a></li>
            <?php
            endif;
            for ($i = 1; $i <= $pageCount; $i++) :
                $name = $_GET['name'] ?? '';
                $creator = $_GET['creator'] ?? '';
                $link = "http://localhost/Coders/project/blog/home.php?page=$i&creator=$creator&name=$name";
                $activeString = "";

                if ($i == $page)
                    $activeString = 'active';
            ?>
            <li class="page-item <?= $activeString ?>"><a class="page-link" href="<?= $link ?>"><?= $i ?></a></li>
            <?php
            endfor;
            if ($page != $pageCount) :
            ?>
            <li class="page-item"><a class="page-link"
                    href="http://localhost/Coders/project/blog/home.php?page=<?= $page + 1 ?>">Next</a></li>
            <?php
            endif;
            ?>
        </ul>
    </nav>
</div>