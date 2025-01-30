<?php

session_start();


    $servername = 'localhost';
    $username   = 'root';
    $pass       = '';
    $dbname     = 'to_do_list_api';

    try {
        $conn = new PDO("mysql:host=$servername; dbname=$dbname", $username, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $email    = $_POST['email'];
            $password = $_POST['password'];
            
            $error    = [];

            if (empty($email)) {
                $error['email'] = "email must not be empty";
            }
            elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error['email'] = 'email  must be in email form';

            }
            if (empty($password)) {
                $error['password'] = "password must not be empty";
            } 
            else if(strlen($password) !== 6){
                $error['password'] = "password must be of  6 digit ";

            }
            else {
                if (empty($error)) {
                    $sql    = "select * from tbl_login where email='$email' and password=$password";  
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $user=$stmt->fetch(PDO::FETCH_ASSOC);
                    if($user){
                        $_SESSION['user_id']=$user['user_id'];
                        header("Location:to_do_list_done.php");
                    } else {
                    ?>
            <script>
                alert("invalid user or password");
            </script>
            <?php
                }
             }
         }
     }
    } catch (PDOException $e) {
            echo "error $e";
    }

            ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        h2 {
            color: #e74c3c;
            text-align: center;
        }
        .form-container .form-label {
            font-size: larger;
        }
    </style>
</head>
<body class="bg-secondary">

<div class="container">
    <div class="form-container">
        <h2>Log In</h2>
        <form action="" method="POST">

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" value="<?php if (isset($_POST['email'])) {echo htmlspecialchars($_POST['email']);}?>" placeholder="Enter your email" >
                 <span class="text-danger"><?php echo isset($error['email']) ? $error['email'] : '' ?></span>
            </div>


            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password" value="<?php if (isset($_POST['password'])) {echo htmlspecialchars($_POST['password']);}?>" placeholder="Enter your password" >
                 <span class="text-danger"><?php echo isset($error['password']) ? $error['password'] : '' ?></span>
            </div>

            <button type="submit" class="btn btn-primary w-100">Log In</button>

            <div class="mt-3 text-center">
                <span>Don't have an account? <a href="index.php">Register here</a></span>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
