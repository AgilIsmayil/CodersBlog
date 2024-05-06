<?php

include "../parts/index.php";

$categoryCountQuery = sqlConnetion()->prepare(
    'SELECT COUNT(id) as count FROM categories'
);
$categoryCountQuery->execute([]);
$categoryCount = $categoryCountQuery->fetch(PDO::FETCH_ASSOC);

$blogCountQuery = sqlConnetion()->prepare(
    'SELECT COUNT(id) as count FROM blog'
);
$blogCountQuery->execute([]);
$blogCount = $blogCountQuery->fetch(PDO::FETCH_ASSOC);

$viewCountQuery = sqlConnetion()->prepare(
    'SELECT sum(view) as sumView FROM blog'
);
$viewCountQuery->execute([]);
$viewCount = $viewCountQuery->fetch(PDO::FETCH_ASSOC);

$blogViewOwnQuery = sqlConnetion()->prepare(
    'SELECT sum(view) as sumView FROM blog WHERE created_by = ?'
);
$blogViewOwnQuery->execute([
    $_SESSION['id']
]);
$viewOwnCount = $blogViewOwnQuery->fetch(PDO::FETCH_ASSOC);

$blogCountMonthQuery = sqlConnetion()->prepare(
    'SELECT COUNT(id) as count FROM blog WHERE MONTH(created_at) = ? '
);
$blogCountMonthQuery->execute([
    date('m')
]);
$blogCountMonth = $blogCountMonthQuery->fetch(PDO::FETCH_ASSOC);

$topThreeBlogQuery = sqlConnetion()->prepare(
    'SELECT title FROM blog ORDER BY view DESC LIMIT 3'
);
$topThreeBlogQuery->execute([]);
$topThreeBlogs = $topThreeBlogQuery->fetchAll(PDO::FETCH_ASSOC);

$monthlyTopThreeBlogQuery = sqlConnetion()->prepare(
    'SELECT title FROM blog WHERE MONTH(created_at) = ?  ORDER BY view DESC LIMIT 3'
);
$monthlyTopThreeBlogQuery->execute([
    date('m')
]);
$monthlyThreeBlogs = $monthlyTopThreeBlogQuery->fetchAll(PDO::FETCH_ASSOC);

$blogViewUsersQuery = sqlConnetion()->prepare(
    'SELECT u.fullname, sum(view) as sumView 
     FROM blog as b
     LEFT JOIN users as u on b.created_by = u.id
     GROUP BY  created_by  
     ORDER BY sumView DESC'
);
$blogViewUsersQuery->execute([]);
$usersBlogViewCount = $blogViewUsersQuery->fetchAll(PDO::FETCH_ASSOC);

$blogCountByCategoriesQuery = sqlConnetion()->prepare(
   'SELECT c.name as name, COUNT(b.id) as count 
    FROM categories as c
    LEFT JOIN blog as b on b.category_id = c.id
    GROUP BY c.name
    Order BY b.id desc
    LIMIT 3
   '
);
$blogCountByCategoriesQuery->execute([]);
$blogCountByCategories = $blogCountByCategoriesQuery->fetchAll(PDO::FETCH_ASSOC);

$blogCountByCreatorQuery = sqlConnetion()->prepare(
    'SELECT u.fullname as name, COUNT(b.created_by) as count 
     FROM users as u
     LEFT JOIN blog as b on b.created_by = u.id
     GROUP BY u.fullname
     Order BY count desc
     LIMIT 3
    '
);
$blogCountByCreatorQuery->execute([]);
$blogCountbyCreators = $blogCountByCreatorQuery->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card text-dark bg-light mb-3" style="max-width: 18rem;">
                <div class="card-header">
                    Category count
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= $categoryCount['count'] ?? 0 ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-dark bg-light mb-3" style="max-width: 18rem;">
                <div class="card-header">
                    Blog Count
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= $blogCount['count'] ?? 0 ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-dark bg-light mb-3" style="max-width: 18rem;">
                <div class="card-header">
                    View Count
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= $viewCount['sumView'] ?? 0 ?></h5>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card text-dark bg-light mb-3" style="max-width: 18rem;">
                <div class="card-header">
                    My blogs View Count
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= $viewOwnCount['sumView'] ?? 0 ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-dark bg-light mb-3" style="max-width: 18rem;">
                <div class="card-header">
                    My Deactive Blog Count
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= $deActiveBlogCount ?? 0 ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-dark bg-light mb-3" style="max-width: 18rem;">
                <div class="card-header">
                    This month blog count
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= $blogCountMonth['count'] ?? 0 ?></h5>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card text-dark bg-light mb-3" style="max-width: 18rem;">
                <div class="card-header">
                    TOP 3 Blog
                </div>
                <div class="card-body">
                    <?php
                    foreach ($topThreeBlogs ?? [] as $topThreeBlog) :
                    ?>
                    <h5 class="card-title"><?= $topThreeBlog['title'] ?></h5>
                    <?php
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-dark bg-light mb-3" style="max-width: 18rem;">
                <div class="card-header">
                    TOP 3 Blog in this month
                </div>
                <div class="card-body">
                    <?php
                    foreach ($monthlyThreeBlogs ?? [] as $monthlyThreeBlog) :
                    ?>
                    <h5 class="card-title"><?= $monthlyThreeBlog['title'] ?></h5>
                    <?php
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-dark bg-light mb-3" style="max-width: 18rem;">
                <div class="card-header">
                    Users blog view count
                </div>
                <div class="card-body">
                    <?php
                    foreach ($usersBlogViewCount ?? [] as $usersBlog) :
                    ?>
                    <h5 class="card-title"><?= $usersBlog['fullname'] . " : " . $usersBlog['sumView'] ?></h5>
                    <?php
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card text-dark bg-light mb-3" style="max-width: 18rem;">
                <div class="card-header">
                    Blog count by Categories
                </div>
                <div class="card-body">
                    <?php
                    foreach ($blogCountByCategories ?? [] as $blogCountByCategory) :
                    ?>
                    <h5 class="card-title">
                        <?= ($blogCountByCategory['name']  ?:  "Category yoxdur") . " : " . $blogCountByCategory['count'] ?>
                    </h5>
                    <?php
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-dark bg-light mb-3" style="max-width: 18rem;">
                <div class="card-header">
                    Blog count by Creator
                </div>
                <div class="card-body">
                    <?php
                    foreach ($blogCountbyCreators ?? [] as $blogCountbyCreator) :
                    ?>
                    <h5 class="card-title">
                        <?= ($blogCountbyCreator['name'] ?: "Category yoxdur") . " : " . $blogCountbyCreator['count'] ?>
                    </h5>
                    <?php
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
        </div>
    </div>
</div>