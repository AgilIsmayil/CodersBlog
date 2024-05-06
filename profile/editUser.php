<?php

include "../parts/index.php";

$profileDataQuery = sqlConnetion()->prepare(" SELECT * FROM users WHERE id = ?");
$profileDataQuery->execute([
    $_SESSION['id']
]);
$profileData = $profileDataQuery->fetch(PDO::FETCH_ASSOC);

if (post('updateProfile')) {
    $update = updateProfile(sqlConnetion());

    if ($update['success'] == false) {
        echo "
            <div class = 'alert alert-danger' role = 'alert'> {$update['message']} </div>
        ";
    } else {
        echo "
            <div class = 'alert alert-success' role = 'alert'> {$update['message']} </div>
        ";
        header("location: http://localhost/Coders/project/blog/home.php");
    }
}

function updateProfile($db)
{
    if (post('fullname') == null) {
        return [
            'success' => false,
            'message' => 'Fill the Fullname!'
        ];
    } elseif (post('birth') == null) {
        return [
            'success' => false,
            'message' => 'Fill the Date of Birth!'
        ];
    } elseif (post('email') == null) {
        return [
            'success' => false,
            'message' => 'Fill the Email!'
        ];
    }
    // Seklin harda save edeceyimizi gostermek ucun
    elseif (isset($_FILES['register_img']) && $_FILES['register_img']['error'] == 0) {

        $dir = "uploads/";

        //Basename-Seklin adini kodlayazmaq ucun 
        $filenameWithDir = $dir . uniqid() . basename($_FILES['register_img']['name']) ?? '';
        $isPhotoExists = isset($_FILES['register_img']['error']) && $_FILES['register_img']['error'] == 0;

        if ($isPhotoExists == false) {
            return [
                'success' => false,
                'message' => 'Profile Image is required'
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
    }

    $profileDataQuery = sqlConnetion()->prepare(" SELECT * FROM users WHERE id = ?");
    $profileDataQuery->execute([
        $_SESSION['id']
    ]);
    $profileData = $profileDataQuery->fetch(PDO::FETCH_ASSOC);

    $updateQuery = $db->prepare("
        UPDATE users set 
            fullname = ?,
            birth = ?,
            email = ?,
            register_img = ?,
            update_at = ?
            WHERE id = ?
    ");
    $updateQuery->execute([
        post('fullname'),
        post('birth'),
        post('email'),
        $filenameWithDir ?? $profileData['register_img'],
        date('Y-m-d H:i:s'),
        $_SESSION['id']
    ]);

    $_SESSION['fullname'] = post('fullname');
    $_SESSION['birth'] = post('birth');
    $_SESSION['email'] = post('email');
    $_SESSION['register_img'] = $filenameWithDir ?? $profileData['register_img'];

    return [
        'success' => true,
        'message' => 'Updating is successfully!'
    ];
}
?>

<div class="container pb-5">
    <form action="" method="POST" enctype="multipart/form-data">
        <h3 class="mt-4">Update profile</h3>
        <div class="form-group">
            <label for="fullname">Name </label>
            <input type="text" id="fullname" name="fullname" class="form-control" value="<?= $profileData["fullname"]  ?>">
        </div>
        <div class="form-group">
            <label for="birth">Birth</label>
            <input type="date" id="birth" name="birth" value="<?= $profileData["birth"]  ?>" class=" form-control">
        </div>
        <div class="form-group  mt-2">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?= $profileData["email"]  ?>">
        </div>
        <div class="d-flex mt-2">
            <div class="col-md-9 form-group  ">
                <label for="image">Image</label>
                <input type="file" id="image" name="register_img" class="form-control" value="">
            </div>
            <div class="col-md-2 offset-1">
                <img src="<?= $domain . "/" . $_SESSION["register_img"] ?? '' ?>" alt="" style="object-fit:cover;" height="80px" width="80px">
            </div>
        </div>
        <br>
        <input type="submit" class="btn btn-primary" name="updateProfile" value="Update">
    </form>
</div>