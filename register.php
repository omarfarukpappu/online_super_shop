<?php

include 'config.php';

function validatePassword($pass) {
    return (strlen($pass) >= 6) && preg_match('/[!@#$%^&*(),.?":{}|<>]/', $pass);
}


session_start();

if(isset($_POST['submit'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $cpass = mysqli_real_escape_string($conn, $_POST['cpassword']);
    $user_type = $_POST['user_type'];

    
    $_SESSION['message'] = array();
    $_SESSION['formData'] = array(
        'name' => $name,
        'email' => $email,
        'user_type' => $user_type,
    );

    if (!validatePassword($pass)) {
        $_SESSION['message'][] = 'Password must be at least 6 characters long and contain at least one special character.';
    } else {
        $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email'") or die('query failed');

        if(mysqli_num_rows($select_users) > 0){
            $_SESSION['message'][] = 'User already exists!';
        } else {
            if($pass != $cpass){
                $_SESSION['message'][] = 'Confirm password not matched!';
            } else {
                $hashedPassword = md5($pass);

                mysqli_query($conn, "INSERT INTO `users` (name, email, password, user_type) VALUES ('$name', '$email', '$hashedPassword', '$user_type')") or die('query failed');
                $_SESSION['message'][] = 'Registered successfully!';
                
             
                $_SESSION['formData'] = array();

              
                header('location: login.php');
                exit();  
            }
        }
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register</title>


   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php


if(isset($_SESSION['message'])){
    foreach($_SESSION['message'] as $message){
        echo '
        <div class="message">
            <span>'.$message.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }

}


$formDataName = isset($_SESSION['formData']['name']) ? $_SESSION['formData']['name'] : '';
$formDataEmail = isset($_SESSION['formData']['email']) ? $_SESSION['formData']['email'] : '';
?>

<div class="form-container">
   <form action="" method="post">
      <h3>register now</h3>
      <input type="text" name="name" placeholder="enter your name" value="<?php echo $formDataName; ?>" required class="box">
      <input type="email" name="email" placeholder="enter your email" value="<?php echo $formDataEmail; ?>" required class="box">
      <input type="password" name="password" placeholder="enter your password" required class="box">
      <input type="password" name="cpassword" placeholder="confirm your password" required class="box">
      <input type="user_type" name="user_type" value="user" readonly required class="box">
      <!-- <select name="user_type" class="box">
         <option value="user">user</option>
         <option value="admin">admin</option>
      </select> -->
      <input type="submit" name="submit" value="register now" class="btn">
      <p>already have an account? <a href="login.php">login now</a></p>
   </form>
</div>

</body>
</html>

<?php

unset($_SESSION['formData']);
?>
