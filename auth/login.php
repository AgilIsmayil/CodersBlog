<?php
include "../parts/index.php";

if (isset($_POST['logSub'])) {
    $login = login(sqlConnetion());

    if ($login['success'] == false) {
        echo "
            <div class = 'alert alert-danger' role = 'alert'> {$login['message']} </div>
        ";
    } else {
        header("location: ../client/blog.php");
        echo "<div class = 'alert alert-primary' role = 'alert'> {$login['message']} </div>";
    }
}

function login($db)
{
    if (post('email') == null) {
        return [
            'success' => false,
            'message' => 'Email is empty. Fill the email field!'
        ];
    } elseif (post('password') == null) {
        return [
            'success' => false,
            'message' => 'Password is empty. Fill the password field!'
        ];
    }

    $usersCheckQuerry = "SELECT * FROM users WHERE email = ?";
    $userCheck = $db->prepare($usersCheckQuerry);
    $userCheck->execute([
        post('email')
    ]);
    $user = $userCheck->fetch(PDO::FETCH_ASSOC);

    if ($user == null) {
        return [
            'success' => false,
            'message' => 'This email is not registered'
        ];
    } elseif (password_verify(post('password'), $user['password']) == false) {
        return [
            'success' => false,
            'message' => 'Password is not correct!'
        ];
    }

    $_SESSION['id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['register_img'] = $user['register_img'];

    return [
        'success' => true,
        'message' => 'Logined is correctly!'
    ];
}

?>

<div class="container">
    <h3 class="pt-4">Login</h3>
    <form action="" method="POST">
        <div class="form-group">
            <label for="mail">Email</label>
            <input type="email" id="mail" name="email" class="form-control">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control">
        </div>

        <div class="mt-4">
            <button type="submit" name="logSub" class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>