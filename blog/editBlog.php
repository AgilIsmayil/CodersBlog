<?php
include_once '../parts/index.php';

include 'blogHelper.php';

$blogQuery = sqlConnetion()->prepare(
    "SELECT * FROM blog WHERE created_by = ? and id = ?"
);

$blogQuery->execute([
    $_SESSION['id'],
    $_GET['id']
]);

$blog = $blogQuery->fetch(PDO::FETCH_ASSOC);
if ($blog == null) {
    header('location: http://localhost/Coders/project/blog/home.php');
}

if (post('insertButton')) {
    $update = updateBlog(sqlConnetion());

    if ($update['success'] == false) {
        echo "
            <div class = 'alert alert-danger' role = 'alert'> {$update['message']} </div>
        ";
    } else {
        echo "
            <div class = 'alert alert-success' role = 'alert'> {$update['message']} </div>
        ";
    }
}

function updateBlog($db)
{
    if (post('title') == null) {
        return [
            'success' => false,
            'message' => 'Title is required!'
        ];
    } elseif (post('id') == null) {
        return [
            'success' => false,
            'message' => '!!! Fatal Error !!!'
        ];
    } elseif (post('body') == null) {
        return [
            'success' => false,
            'message' => 'Body is required!'
        ];
    } elseif (post('category_id') == null) {
        return [
            'success' => false,
            'message' => 'Category is required!!'
        ];
    }

    $blogQuery = sqlConnetion()->prepare(
        "SELECT * FROM blog WHERE created_by = ? and id = ?"
    );

    $blogQuery->execute([
        $_SESSION['id'],
        $_POST['id']
    ]);

    $blog = $blogQuery->fetch(PDO::FETCH_ASSOC);

    if ($blog == null) {
        header('location: http://localhost/Coders/project/blog/home.php');
    }

    if (isset($_FILES['cover_img']['error']) && $_FILES['cover_img']['error'] == 0) {

        $dir = "uploads/";

        $filenameWithDir = $dir . uniqid() . basename($_FILES['cover_img']['name']) ?? '';
        $isPhotoExists = isset($_FILES['cover_img']['error']) && $_FILES['cover_img']['error'] == 0;

        if ($isPhotoExists == false) {
            return [
                'success' => false,
                'message' => 'Cover Image is required'
            ];
        }

        $check = getimagesize($_FILES['cover_img']['tmp_name']);
        if ($check == false) {
            return [
                'success' => false,
                'message' => 'Photo input is not image!'
            ];
        }

        if ($_FILES['cover_img']['size'] > 5000000) {
            return [
                'success' => false,
                'message' => 'File is too large'
            ];
        }

        $imageFileType = strtolower(pathinfo($filenameWithDir, PATHINFO_EXTENSION));
        if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg') {
            return [
                'success' => false,
                'message' => 'Only JPG, PNG, JPEG files are allowed!'
            ];
        }

        $upload = move_uploaded_file($_FILES['cover_img']['tmp_name'], '../' . $filenameWithDir);
        if (!$upload) {
            return [
                'success' => false,
                'message' => 'Photo can not upload'
            ];
        }
    }

    $updateQuery = $db->prepare("
        UPDATE blog set 
            title = ?,
            body = ?,
            category_id = ?,
            updated_at = ?,
            cover_img = ?
            WHERE id = ?
    ");
    $updateQuery->execute([
        post('title'),
        post('body'),
        post('category_id'),
        date('Y-m-d H:i:s'),
        $filenameWithDir ?? $blog['cover_img'],
        post('id'),
    ]);

    return [
        'success' => true,
        'message' => 'Updated successfully!'
    ];
}

$category = getCategoriesForSelect(sqlConnetion());

?>

<div class="container">
    <form action="" method="POST" enctype="multipart/form-data">
        <h3>Edit Blog</h3>

        <label for="title">Title</label>
        <div class="form-group">
            <input type="text" name="title" id="title" value="<?= $blog['title'] ?>" class="form-control">
        </div>

        <label for="body">Body</label>
        <div class="form-group">
            <textarea name="body" id="body" class="form-control"><?= $blog['body'] ?></textarea>
        </div>

        <label for="category">Category</label>
        <div class="form-group">
            <select name="category_id" id="category" class="form-control">
                <?php
                foreach ($category as $cat) :
                    $selectedCat = '';
                    if ($cat['id'] == $blog['category_id']) {
                        $selectedCat = 'selected';
                    }
                ?>
                    <option value="<?= $cat['id'] ?>" <?= $selectedCat ?>><?= $cat['name'] ?></option>'
                <?php
                endforeach;
                ?>
            </select>
        </div>

        <div class="row">
            <div class="col-md-6">
                <label for="cover_img">Cover Image</label>
                <div class="form-group">
                    <input type="file" name="cover_img" id="cover_img" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <img width="200px" src="<?= $domain . '/' . $blog['cover_img'] ?>" alt="">
            </div>
        </div>

        <input type="hidden" name="id" value="<?= $blog['id'] ?>">

        <input type="submit" name="insertButton" class="btn btn-primary">

    </form>
</div>