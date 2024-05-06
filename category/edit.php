<?php
include_once '../parts/index.php';

$categoryQuery = sqlConnetion()->prepare("SELECT id, name FROM categories WHERE id = ? and created_by = ?");
$categoryQuery->execute([
    $_GET['id'],
    $_SESSION['id']
]);
$category = $categoryQuery->fetch(PDO::FETCH_ASSOC);

if ($category == null) {
    header('location: home.php');
}

if (post('insertBtn')) {
    $update = update(sqlConnetion(), $category);
    if (!$update['success']) {
        echo "
        <div class = 'alert alert-danger' role = 'alert'> {$update['message']} </div>
        ";
    } else {
        echo "
        <div class = 'alert alert-success' role = 'alert'> {$update['message']} </div>
        ";
    }
}

function update($db, $category)
{
    if (post('name') == null) {
        return [
            'success' => false,
            'message' => 'Fill the input!'
        ];
    } elseif (post('name') == $category['name']) {
        return [
            'success' => false,
            'message' => 'Category already exists!'
        ];
    }

    $updateQuery = sqlConnetion()->prepare("UPDATE categories set name = ? WHERE id = ? and created_by = ?");
    $updateQuery->execute([
        post('name'),
        post('id'),
        $_SESSION['id']
    ]);

    return [
        'success' => true,
        'message' => 'Updating is successfully!'
    ];
}

?>

<div class="container">
    <h3>Edit Category</h3>
    <form action="" method="POST">
        <label for="name">Name</label>
        <div class="form-group">
            <input type="text" name="name" id="name" class="form-control" value="<?= $category['name'] ?>">
        </div>
        <input type="hidden" name="id" value="<?= $category['id'] ?>">
        <br>
        <input type="submit" name="insertBtn" class="btn btn-primary">
    </form>
</div>