<?php
include_once '../parts/index.php';

if (post('insertButton')) {
    $insert = storeBlog(sqlConnetion());
    if ($insert['success'] == false) {
        echo "
            <div class = 'alert alert-danger' role = 'alert'> {$insert['message']} </div>
        ";
    } else {
        echo "
            <div class = 'alert alert-success' role = 'alert'> {$insert['message']} </div>
        ";
    }
}

function storeBlog($db)
{
    if (post('title') == null) {
        return [
            'success' => false,
            'message' => 'Fill the Title input!'
        ];
    } elseif (post('body') == null) {
        return [
            'success' => false,
            'message' => 'Fill the Body input!'
        ];
    } elseif (post('category_id') == null) {
        return [
            'success' => false,
            'message' => 'Fill the Category input!'
        ];
    }

    // Seklin harda save edeceyimizi gostermek ucun
    $dir = "uploads/";

    //Basename-Seklin adini kodlayazmaq ucun 
    $filenameWithDir = $dir . uniqid() . basename($_FILES['cover_img']['name']) ?? '';
    $isPhotoExists = isset($_FILES['cover_img']['error']) && $_FILES['cover_img']['error'] == 0;

    if ($isPhotoExists == false) {
        return [
            'success' => false,
            'message' => 'Cover Image is required'
        ];
    }

    // getimagesize - sekil olub olmadigini yoxlamaq ucun istifade olunan hazir kod
    $check = getimagesize($_FILES['cover_img']['tmp_name']);
    if ($check == false) {
        return [
            'success' => false,
            'message' => 'Photo input is not image!'
        ];
    }

    // File-in yaddasinin neqeder olmasi
    if ($_FILES['cover_img']['size'] > 5000000) {
        return [
            'success' => false,
            'message' => 'File is too large'
        ];
    }

    //strtolower-seklin adindaki herfleri kicikle gostermek ucundur
    // pathinfo- funksiyada hazir koddur,seklin hansi formatda oldugunu yoxlamaq ucun istifade edilir 
    $imageFileType = strtolower(pathinfo($filenameWithDir, PATHINFO_EXTENSION));
    if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg') {
        return [
            'success' => false,
            'message' => 'Only JPG, PNG, JPEG files are allowed!'
        ];
    }

    // move_uploaded_file - hazir funksiya kodudur, seklin hara yuklenmeli oldugunu gosterir asagidaki kodlardan oxumaq olar.
    $upload = move_uploaded_file($_FILES['cover_img']['tmp_name'], '../' . $filenameWithDir);
    if (!$upload) {
        return [
            'success' => false,
            'message' => 'Photo can not upload'
        ];
    }

    $insertQuery = $db->prepare(
        "INSERT INTO blog(title, body, category_id, created_by, cover_img)
        VALUES (?, ?, ?, ?, ?)"
    );

    $insertQuery->execute([
        post('title'),
        post('body'),
        post('category_id'),
        $_SESSION['id'],
        $filenameWithDir
    ]);

    return [
        'success' => true,
        'message' => 'Stored successfully!'
    ];
}

$categoriesQuery = sqlConnetion()->prepare(
    "SELECT id, name FROM categories"
);
$categoriesQuery->execute([]);

$category = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <form action="" method="POST" enctype="multipart/form-data">
        <h3>Create Blog</h3>

        <label for="title">Title</label>
        <div class="form-group">
            <input type="text" name="title" id="title" value="<?= post('title') ?>" class="form-control">
        </div>

        <label for="body">Body</label>
        <div class="form-group">
            <textarea name="body" id="body" class="form-control"><?= post('body') ?></textarea>
        </div>

        <label for="category">Category</label>
        <div class="form-group">
            <select name="category_id" id="category" class="form-control">
                <?php
                foreach ($category as $cat) :
                ?>
                <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>'
                <?php
                endforeach;
                ?>
            </select>
        </div>

        <label for="cover_img">Cover Image</label>
        <div class="form-group">
            <input type="file" name="cover_img" id="cover_img" class="form-control">
        </div>

        <br>
        <input type="submit" name="insertButton" class="btn btn-primary">
    </form>
</div>