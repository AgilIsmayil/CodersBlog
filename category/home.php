<?php

include '../parts/index.php';

$isFilter = false;
$sqlWhere = ' WHERE ';
$executeArray = [];

if ($_GET['Name'] ?? null != null) {
    $sqlWhere .= "c.name like ?";
    $isFilter = true;
    $executeArray[] = '%' . $_GET['Name'] . '%';
}

if ($_GET['creator'] ?? null != null) {
    if ($isFilter) {
        $sqlWhere .= ' and ';
    }
    $sqlWhere .= "u.fullname like ? ";
    $isFilter = true;
    $executeArray[] = '%' . $_GET['creator'] . '%';
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
    SELECT c.id, c.name, c.created_at, u.fullname as creator, c.created_by
    FROM categories as c 
    LEFT JOIN users as u on u.id = c.created_by
    $sqlWhere
    ORDER BY c.id desc
    LIMIT $limit OFFSET $offset
";

$categoriesQuerry = sqlConnetion()->prepare($sql);
$categoriesQuerry->execute($executeArray);
$categories = $categoriesQuerry->fetchAll(PDO::FETCH_ASSOC);

$totalQuerry = sqlConnetion()->prepare("
    SELECT COUNT(*) as count 
    FROM categories as c
    LEFT JOIN users as u on u.id = c.created_by
    $sqlWhere
");

$totalQuerry->execute($executeArray);
$total = $totalQuerry->fetch(PDO::FETCH_ASSOC);

$pageCount = ceil($total['count'] / $limit);

?>

<div class="container">
    <div class="d-flex justify-content-between align-item-center py-3">
        <h3>Categories</h3>
        <a href="http://localhost/Coders/project/category/create.php" class="btn btn-primary">Add New</a>
    </div>
    <form action="" method="GET">
        <div class="row my-4">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Name</label>
                    <input type="text" name="Name" value="<?= $_GET['Name'] ?? null ?>" class="form-control">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Creator</label>
                    <input type="text" name="creator" value="<?= $_GET['creator'] ?? null ?>" class="form-control">
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
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Creator</th>
                <th>Created at</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            foreach ($categories as $cat) :
            ?>
            <tr>
                <td><?= $offset + $i++ ?></td>
                <td><?= $cat['name'] ?></td>
                <td><?= $cat['creator'] ?></td>
                <td><?= $cat['created_at'] ?></td>
                <td>

                    <?php
                        $editColor = 'primary';
                        $deleteColor = 'danger';
                        $editHref = 'edit.php?id=' . $cat['id'];
                        $deleteHref = 'delete.php?id=' . $cat['id'];

                        if ($_SESSION['id'] != $cat['created_by']) {
                            $editColor = 'secondary';
                            $deleteColor = 'secondary';
                            $editHref = '#';
                            $deleteHref = '#';
                        }
                        ?>

                    <a href="<?= $editHref ?>" class="btn btn-sm btn-<?= $editColor ?>">Edit</a>
                    <a href="<?= $deleteHref ?>" class="btn btn-sm btn-<?= $deleteColor ?>">Delete</a>
                </td>
            </tr>

            <?php
            endforeach;
            ?>

        </tbody>
    </table>
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <?php
            if ($page != 1) :
            ?>
            <li class="page-item">
                <a class="page-link"
                    href="http://localhost/Coders/project/category/home.php?page=<?= $page - 1 ?>">Previous</a>
            </li>

            <?php
            endif;

            for ($i = 1; $i <= $pageCount; $i++) :
                $name = $_GET['name'] ?? '';
                $creator = $_GET['creator'] ?? '';
                $link = "http://localhost/Coders/project/category/home.php?page=$i&creator=$creator&name=$name";
                $activeString = "";

                if ($i == $page)
                    $activeString = 'active';
            ?>

            <li class="page-item <?= $activeString ?>">
                <a class="page-link" href="<?= $link ?>"><?= $i ?></a>
            </li>

            <?php
            endfor;
            if ($page != $pageCount) :
            ?>

            <li class="page-item">
                <a class="page-link"
                    href="http://localhost/Coders/project/category/home.php?page=<?= $page + 1 ?>">Next</a>
            </li>

            <?php
            endif;
            ?>

        </ul>
    </nav>
</div>