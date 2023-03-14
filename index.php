<?php

require_once("./db/config.php");


$isLogin = false;
$userName = "";
$password = "";
$isError = false;
$errorText = "";
$allWorkers = [];
$statusLogin = true;

if (isset($_POST["changeStatus"]) && !empty($_POST["changeStatus"])) {
    $statusLogin = $_POST["changeStatus"] === "login";
} else if (isset($_POST["name"], $_POST["password"]) && !empty($_POST["name"]) && !empty($_POST["password"])) {

    // USE OOP
    $connection = new mysqli($config["host"], $config["username"], $config["password"], $config["dbname"]);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
        exit();
    }
    $userName = $_POST["name"];
    $password = hash("md4", $_POST["password"]);

    if (isset($_POST["register"])) {
        $statusLogin = false;
        if (isset($_POST["passwordConfirm"]) && !empty($_POST["passwordConfirm"]) && $_POST["password"] === $_POST["passwordConfirm"]) {
            $sql = "SELECT id FROM Users
            WHERE name='" . $userName . "'";
            $result = $connection->query($sql);
            if ($result->num_rows > 0) {
                $isError = true;
                $errorText = "This user already exists";
            } else {
                $sql = "INSERT INTO Users(name, password) 
                    VALUES ('" . $userName . "','" . $password . "')";
                if ($connection->query($sql) === false) {
                    $isError = true;
                    $errorText = "Server Error";
                }
            }
        } else {
            $isError = true;
            $errorText = "Password and password confirmation are different";
        }
    }

    if (!$isError) {
        $statusLogin = true;
        $sql = "SELECT id FROM Users
            WHERE name='" . $userName . "' AND password='" . $password . "'";
        $result = $connection->query($sql);
        if ($result->num_rows > 0) {
            $isLogin = true;
        } else {
            $isError = true;
            $errorText = "Login or password error";
        }
        if ($isLogin) {
            $sql = "SELECT * FROM Workers";
            $result = $connection->query($sql);

            while ($row = mysqli_fetch_assoc($result)) {
                array_push($allWorkers, $row);
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
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <style>
        .error {
            color: red;
        }
        .link {
            background-color: transparent;
            border: none;
            outline: none;
            text-decoration: underline;
            color: blue;
        }
    </style>
</head>

<body>
    <?php
    if ($isLogin) {
        echo '<h1>Hello ' . $userName . ' !</h1>
            <form>
                <button type="submit" class="btn btn-danger mx-2">Exit</button>
            </form>';
    } else {
        echo '<div class="container mt-5">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label w-100">Login
                    <input name="name" type="text" class="form-control" placeholder="Login" />
                </label>
            </div>
            <div class="mb-3">
                <label class="form-label w-100">Password
                    <input name="password" type="password" class="form-control" placeholder="Password">
                </label>
            </div>'
            . ($statusLogin ? "" : '
                    <div class="mb-3">
                        <label class="form-label w-100">Confirm Password
                            <input name="passwordConfirm" type="password" class="form-control" placeholder="Confirm password">
                        </label>
                    </div>
                ') .
            '<div class="col-auto mb-3">
                <div class="d-flex justify-content-between">';
        if ($statusLogin) {
            echo '<button type="submit" class="btn btn-warning mx-2">Login</button>
                        <button class="link" type="submit" name="changeStatus" value="register">Register</button>';
        } else {
            echo '<button type="submit" name="register" class="btn btn-warning mx-2">Register</button>
                        <button class="link" type="submit" name="changeStatus" value="login">Login</button>';
        }
        echo '</div>
            </div>
            <div class="col-auto">';
        if ($isError) {
            echo '<span class="error">' . $errorText . '</span>';
        }
        echo '</div>
        </form>
    </div>';
    }
    if ($isLogin) {
        echo  '<div class="container mt-5">
        <div class="row">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Name</th>
                        <th scope="col">Age</th>
                        <th scope="col">Salary</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>';
        foreach ($allWorkers as $row) {
            echo '<tr>';
            echo '<th scope="row">' . $row["id"] . '</th>';
            echo '<td>' . $row["name"] . '</td>';
            echo '<td>' . $row["age"] . '</td>';
            echo '<td>' . $row["salary"] . ' ₼</td>';
            echo '<tr>';
        }
        echo '</tbody>
            </table>
        </div>
        </div>';
    }
    ?>

</body>

</html>