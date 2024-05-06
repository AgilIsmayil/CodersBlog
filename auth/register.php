<?php

include "../parts/index.php";

$db = sqlConnetion();

function registerValidate()
{
    if (post('fullname') == null) {
        return [
            'success' => false,
            'message' => 'Fill name & surname'
        ];
    } elseif (post('email') == null) {
        return [
            'success' => false,
            'message' => 'Fill the email!'
        ];
    } elseif (strlen(post('email')) > 50) {
        return [
            'success' => false,
            'message' => 'Email cannot be greater than 50 symbol!'
        ];
    } elseif (post('password') == null) {
        return [
            'success' => false,
            'message' => 'Fill the password!'
        ];
    } else {
        return [
            'success' => true,
            'message' => 'Registered successfully'
        ];
    }
}

if (post('registerSubmit')) {
    $validate = registerValidate();
    $response =  register($db);

    if ($validate['success'] == false) {
        echo "<div class = 'alert alert-danger' role = 'alert'> {$validate['message']} </div>";
    } else {
        if ($response['success'] == true) {
            header("location:login.php");
            echo "<div class = 'alert alert-primary' role = 'alert'> {$validate['message']} </div>";
        }
    }
}

function register($db)
{
    // Seklin harda save edeceyimizi gostermek ucun
    $dir = "uploads/";

    //Basename-Seklin adini kodlayazmaq ucun 
    $filenameWithDir = $dir . uniqid() . basename($_FILES['register_img']['name']) ?? '';

    $isPhotoExists = isset($_FILES['register_img']['error']) && $_FILES['register_img']['error'] == 0;

    if ($isPhotoExists == false) {
        return [
            'success' => false,
            'message' => 'Register Image is required'
        ];
    }

    // getimagesize - sekil olub olmadigini yoxlamaq ucun istifade olunan hazir kod
    $check = getimagesize($_FILES['register_img']['tmp_name']);
    if ($check == false) {
        return [
            'success' => false,
            'message' => 'Photo input is not image!'
        ];
    }

    // File-in yaddasinin neqeder olmasi
    if ($_FILES['register_img']['size'] > 5000000) {
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
    $upload = move_uploaded_file($_FILES['register_img']['tmp_name'], '../' . $filenameWithDir);
    if (!$upload) {
        return [
            'success' => false,
            'message' => 'Photo can not upload'
        ];
    }

    $query = $db->prepare("INSERT INTO users(fullname, birth, gender, email, password, register_img) VALUES(?, ?, ?, ?, ?, ?)");
    $query->execute([
        post("fullname"),
        post("birth"),
        post("cins"),
        post("email"),
        password_hash(post("password"), PASSWORD_BCRYPT),
        $filenameWithDir
    ]);

    return
        [
            'success' => true,
            'message' => 'Registered successfully'
        ];
}
?>

<div class="container">
    <form action="" method="POST" enctype="multipart/form-data">
        <h3>Register</h3>

        <div class="form-group">
            <label for="fullname">Name Surname</label>
            <input type="text" id="fullname" name="fullname" class="form-control">
        </div>

        <div class="form-group">
            <label for="birth">Birth</label>
            <input type="date" id="birth" name="birth" class="form-control">
        </div>

        <label for="cins">Cins</label>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="cins" id="man" value="1">
            <label class="form-check-label" for="man">
                Male
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="cins" id="woman" value="2">
            <label class="form-check-label" for="woman">
                Female
            </label>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= post('email') ?>" class="form-control">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" value="<?= post('password') ?>" class="form-control">
        </div>

        <div class="form-group">
            <label for="password_verify">Password Verify</label>
            <input type="password" id="password_verify" name="password_verify" class="form-control">
        </div>

        <label for="cover_img">Cover Image</label>
        <div class="form-group">
            <input type="file" name="register_img" id="register_img" class="form-control">
        </div>
        <br>

        <input type="submit" class="btn btn-primary" name="registerSubmit" value="Register">
    </form>
</div>