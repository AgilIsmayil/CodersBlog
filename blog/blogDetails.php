<?php

include "../parts/index.php";

$query = sqlConnetion()->prepare("
    SELECT b.*, u.fullname as userName, c.name as categoryName
    FROM blog as b 
    LEFT JOIN categories as c on b.category_id = c.id
    LEFT JOIN users as u on u.id = b.created_by 
    WHERE b.id = ?
");

$query->execute([
    $_GET['id']
]);
$data = $query->fetch(PDO::FETCH_ASSOC);

$query = sqlConnetion()->prepare("UPDATE blog SET view = ? WHERE id = ?");
$query->execute([$data['view'] + 1, $_GET['id']]);

?>

<div class="container">
    <table class="table">
        <thead>
            <tr>
                <th>Blog Name</th>
                <th>Creator Name</th>
                <th>Category Name</th>
                <th>Description</th>
                <th>Image</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $data['title'] ?></td>
                <td><?= $data['userName'] ?></td>
                <td><?= $data['categoryName'] ?></td>
                <td><?= $data['body'] ?></td>
                <td><img width="70px" height="70px" style="object-fit: cover;"
                        src="<?= $domain . "/" . $data['cover_img'] ?>" alt=""></td>
                <td><?= $data['created_at'] ?></td>
            </tr>
        </tbody>
    </table>
</div>