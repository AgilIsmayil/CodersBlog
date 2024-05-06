<?php

include_once '../parts/index.php';

if (post('insertButton')) {
    $insertCat = insertCategory(sqlConnetion());

    if ($insertCat['success'] == false) {
        echo "
            <div class='alert alert-danger'> {$insertCat['message']}</div>
        ";
    } else {
        header('Location: http://localhost/Coders/project/category/home.php');
    }
}

function insertCategory($db)
{
    if (post('name') == null) {
        return [
            'success' => false,
            'message' => "Fill the input!"
        ];
    }

    $categoryCheckQuery = $db->prepare("SELECT id FROM categories WHERE name = ?");
    $categoryCheckQuery->execute([
        post('name')
    ]);
    $category = $categoryCheckQuery->fetch(PDO::FETCH_ASSOC);

    if ($category != null) {
        return [
            'success' => false,
            'message' => "This category has been used.Fill the other category!"
        ];
    }

    $insertQuery = $db->prepare("INSERT INTO categories(name, created_by) VALUES (?, ?)");
    $insertQuery->execute([
        post('name'),
        $_SESSION['id']
    ]);

    return [
        'success' => true,
        'message' => 'Created successfully'
    ];
}

?>

<div class="container">
    <form action="" method="POST">
        <h3>Create Category</h3>
        <label for="name">Name</label>
        <div class="form-group">
            <input type="text" name="name" class="form-control">
        </div>
        <br>
        <input type="submit" name="insertButton" class="btn btn-primary">
    </form>
</div>