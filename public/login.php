<?php
    include "service/database.php";
    session_start();

    $login_massage  = "";

    if(isset($_SESSION["is_login"])){
        header("location: home.php");
    }

    if(isset($_POST['login-button'])){
        $username   = $_POST['username'];
        $password   = $_POST['password'];

        $sql = "SELECT * FROM users 
                WHERE user_name='$username'";
        $result = $db->query($sql);

        if($result->num_rows > 0) {
            $data = $result->fetch_assoc(); 

            if(password_verify($password, $data['user_password'])){
                $_SESSION["username"]   = $data["user_name"];
                $_SESSION["is_login"]   = true;
                $sql = "INSERT INTO session_list(user_name) VALUES('$username')";
                if($db->query($sql)) {
                    $login_massage = "LOG IN succeed!";
                    header("location: home.php");
                }else{
                    $login_massage = "LOG IN doesn't run successfully, try again";
                }
            } else {
                $login_massage = "Password doesn't match, try another password";
            }
        } else {
            $login_massage= "Couldn't find your account, try register first";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal It/login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="flex">
        <div class="w-1/2 bg-cover" style="background-image: url('img/cover.jpeg')">
        </div>
        <div class="w-1/2 p-8">
            <div class="img-fluid" style="height:116px; width: 720px;"></div>

            <i><?= $login_massage ?></i>

            <div class="flex items-center justify-center">
                <img src="img/logo_Journalit_landscape.png" class="w-36"/>
            </div>
            <h3 class="text-4xl font-bold mb-4">Login</h3>
            
            <form action="login.php" method="POST">
                <div class="mb-4">
                    <label class="block text-sm" for="username">User Name</label>
                    <input id="username" type="text" name="username" class="shadow-md appearance-none border-none rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"  placeholder="Type your username here">
                </div>
                <div class="mb-6">
                    <label class="block text-sm" for="password">Password</label>
                    <input id="password" type="password" name="password" class="shadow-md appearance-none border-none rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" placeholder="Type your password here">
                </div>
                <div class="flex items-center justify-center">
                    <button type="submit" name="login-button" class="bg-customPink2 hover:bg-customPink3 text-white w-80 py-2 px-4 rounded-full focus:outline-none focus:shadow-outline" >
                        Login
                    </button>
                </div>
                <p class="text-center text-gray-500 text-xs mt-4">
                    Doesn't have an account yet? <a href="register.php" class="text-customPurple1 hover:text-black"><u>Sign Up</u></a>
                </p>
            </form>
            <div class="img-fluid" style="height:188px; width: 720px;"></div>
        </div>
    </div>
</body>
</html>
