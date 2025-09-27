<?php
require_once '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT Librarian_id FROM librarian WHERE Email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Email already registered!";
    } else {
        $sql = "INSERT INTO librarian (Name, Email, Password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $hashedPassword);

        if ($stmt->execute()) {
            header("Location: login.php?success=1");
            exit();
        } else {
            $error = "Something went wrong. Please try again.";
        }
        $stmt->close();
    }

    $check->close();
    $conn->close();
}
if (!empty($error)) {
    echo "<div style='color:red; text-align:center;'>$error</div>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- FAVICON -->
    <link rel="shortcut icon" href="/img/loa_logo.png" type="image/x-icon">
    <!-- BS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!-- FA -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
        integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- ADMIN CSS -->
    <link rel="stylesheet" href="admin.css">
    <title>SIGNUP</title>
    <style>
        .left-side {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url(../img/library.jpg);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .form-section {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 2rem;
        }

        .login-form {
            max-width: 350px;
            width: 100%;
        }

        .form-control {
            border-radius: 10px;
        }

        .signin-btn {
            background-color: oklch(44.6% 0.03 256.802);
            border-radius: 10px;
            color: #ffffff;
        }

        .signin-btn:hover {
            background-color: oklch(27.8% 0.033 256.848);
            border-radius: 10px;
            color: #ffffff;
        }

        .back {
            position: absolute;
            top: 5%;
            left: 5%;
            border-radius: 10px;
            padding: 8px;
            background-color: #ffffff;
        }

        .back i {
            font-size: 2rem;
            vertical-align: middle;
            color: black;
        }
    </style>
</head>

<body>
    <div class="login-page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 left-side"></div>

                <div class="col-lg-6 form-section">
                    <div class="login-form mx-auto">
                        <div class="text-center mb-4">
                            <a href="../index.html" class="logo"><img src="../img/loa_logo.png" class="w-25" alt=""></a>
                        </div>
                        <form action="signup.php" method="POST">
                            <div class="mb-3">
                                <label for="name">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn signin-btn">Sign up</button>
                            </div>
                            <p class="mt-2 text-center">Already have an account? <a href="login.php">Log in now</a></p><br>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- BACK TO INDEX BUTTON -->
        <div class="back">
            <a href="../index.html"><i class="fa-solid fa-arrow-left"></i></a>
        </div>
</body>

</html>