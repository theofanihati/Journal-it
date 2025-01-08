<?php
include "service/database.php";
    session_start();

    $register_massage = "";
    $show_registered_popup = false;

    if(isset($_SESSION["is_login"])){
        header("location: home.php");
    }

    if(isset($_POST['register'])){
        $email      = $_POST["email"];
        $username   = $_POST["username"];
        $password   = password_hash($_POST['password'], PASSWORD_BCRYPT);

        if (empty($email) || empty($username) || empty($password)) {
            $register_massage = "Semua kolom harus diisi!";
        } else {
            try { 
                $stmt = $db->prepare("INSERT INTO users(user_email, user_name, user_password) VALUES (?, ?, ?)"); 
                $stmt->bind_param("sss", $email, $username, $password); 
                if ($stmt->execute()) {   
                $show_registered_popup = true;
                } else { 
                    $register_massage = "YOUR REGISTER doesn't run successfully, try again"; 
                } 
            } catch (mysqli_sql_exception $e) { 
                $register_massage = $e->getMessage();
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal It/register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="flex">
        <div class="w-1/2 bg-cover" style="background-image: url('img/cover.jpeg')"></div>
        <div class="w-1/2 p-8">
            <div class="img-fluid" style="height:116px; width: 720px;"></div>
            <div class="flex items-center justify-center">
                <img src="img/logo_Journalit_landscape.png" class="w-36"/>
            </div>
            <h3 class="text-4xl font-bold mb-4">Register</h3>

            <i><?= $register_massage ?></i>

            <form action="register.php" method="POST">
                <div class="mb-4">
                    <label class="block text-sm " for="email">Email</label>
                    <input name="email" class="shadow-md appearance-none border-none rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" placeholder="Type your email here">
                </div>
                <div class="mb-4">
                    <label class="block text-sm" for="username">User Name</label>
                    <input name="username" class="shadow-md appearance-none border-none rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" type="text" placeholder="Type your username here">
                </div>
                <div class="mb-6">
                    <label class="block text-sm" for="password">Password</label>
                    <input name="password" class="shadow-md appearance-none border-none rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" placeholder="Type your password here">
                </div>
                <div class="flex items-center justify-center">
                    <button type="submit" name="register" class="bg-customPink2 hover:bg-customPink3 text-white w-80 py-2 px-4 rounded-full focus:outline-none focus:shadow-outline">
                        Register
                    </button>
                </div>
                <p class="text-center text-gray-500 text-xs mt-4 ">
                    Already have an account? <a href="login.php" class="text-customPurple1 hover:text-black"><u>Log In</u></a>
                </p>
            </form>
            <div class="img-fluid" style="height:116px; width: 720px;"></div>
        </div>
    </div>
    <div id="popup-registered" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-4 rounded-2xl shadow-lg w-1/2 text-center">
            <p class="text-xl font-semibold">Register Success!</p>
            <p class="text-lg text-customPink3">Please log in</p>
            <button id="ok-button" class="bg-customPink2 hover:bg-customPink3 text-white w-3/4 py-2 px-4 rounded-full focus:outline-none focus:shadow-outline" style="margin-top: 16px">OK</button>
        </div>
    </div>
    <script>
        <?php if($show_registered_popup): ?>
            document.getElementById('popup-registered').classList.remove('hidden');
        <?php endif ?>
        
        document.getElementById('ok-button').addEventListener('click', () => {
            window.location.href = 'home.php';
        });
    </script>
</body>
</html>
