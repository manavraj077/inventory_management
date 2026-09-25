<?php

session_start();

include 'db.php';

$google_client_id = '4260594162-25q32plpgkqodjjujg6tn0m78m7ltbn9.apps.googleusercontent.com';

/* =========================
   NORMAL EMAIL/PASSWORD LOGIN
   ========================= */
if (isset($_POST['login']))
{
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users
            WHERE email='$email'
            AND password='$password'";

    $result = mysqli_query($data, $sql);

    if (mysqli_num_rows($result) == 1)
    {
        $row = mysqli_fetch_assoc($result);

        $_SESSION['username'] = $row['username'];
        $_SESSION['usertype'] = $row['usertype'];
        $_SESSION['user_id'] = $row['id'];

        if ($row['usertype'] == 'admin')
        {
            header("Location: dashboard.php");
            exit();
        }
        elseif ($row['usertype'] == 'customer')
        {
            header("Location: customer.php");
            exit();
        }
    }
    else
    {
        $error = "Invalid email or password";
    }
}


/* =========================
   GOOGLE LOGIN
   ========================= */
if (isset($_POST['credential']))
{
    $csrf_cookie = $_COOKIE['g_csrf_token'] ?? '';
    $csrf_post = $_POST['g_csrf_token'] ?? '';

    if (!$csrf_cookie || !$csrf_post || !hash_equals($csrf_cookie, $csrf_post))
    {
        $error = "Google login security check failed. Please try again.";
    }
    elseif (!file_exists(__DIR__ . '/vendor/autoload.php'))
    {
        $error = "Google login setup is incomplete. Run Composer setup first.";
    }
    else
    {
        require_once __DIR__ . '/vendor/autoload.php';

        $credential = $_POST['credential'];

        try
        {
            $client = new Google_Client([
                'client_id' => $google_client_id
            ]);

            $payload = $client->verifyIdToken($credential);

            if ($payload)
            {
                $google_sub = mysqli_real_escape_string($data, $payload['sub']);
                $google_email = mysqli_real_escape_string($data, $payload['email']);
                $google_name = mysqli_real_escape_string(
                    $data,
                    $payload['name'] ?? explode('@', $payload['email'])[0]
                );

                $sql = "SELECT * FROM users
                        WHERE google_sub='$google_sub'
                        LIMIT 1";

                $result = mysqli_query($data, $sql);

                if (mysqli_num_rows($result) == 1)
                {
                    $row = mysqli_fetch_assoc($result);

                    $_SESSION['username'] = $row['username'];
                    $_SESSION['usertype'] = $row['usertype'];
                    $_SESSION['user_id'] = $row['id'];

                    header("Location: customer.php");
                    exit();
                }

                $email_check = mysqli_query(
                    $data,
                    "SELECT * FROM users
                     WHERE email='$google_email'
                     LIMIT 1"
                );

                if (mysqli_num_rows($email_check) == 1)
                {
                    $error = "An account with this email already exists. Please use your email and password.";
                }
                else
                {
                    $insert = "INSERT INTO users
                               (username, email, password, usertype, google_sub)
                               VALUES
                               ('$google_name', '$google_email', NULL, 'customer', '$google_sub')";

                    if (mysqli_query($data, $insert))
                    {
                        $new_id = mysqli_insert_id($data);

                        $_SESSION['username'] = $google_name;
                        $_SESSION['usertype'] = 'customer';
                        $_SESSION['user_id'] = $new_id;

                        header("Location: customer.php");
                        exit();
                    }
                    else
                    {
                        $error = "Could not create Google account: " . mysqli_error($data);
                    }
                }
            }
            else
            {
                $error = "Invalid Google account token.";
            }
        }
        catch (Exception $e)
        {
            $error = "Google login failed. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>Inventory Login</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>

        * {
            box-sizing: border-box;
        }

        body {

            margin: 0;

            font-family: Arial, sans-serif;

            min-height: 100vh;

            display: flex;

            justify-content: center;

            align-items: center;

            background: linear-gradient(
                -45deg,
                #0f2027,
                #203a43,
                #2c5364,
                #0f2027
            );

            background-size: 400% 400%;

            animation: backgroundMove 12s ease infinite;

        }


        @keyframes backgroundMove {

            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }

        }


        .login-box {

            width: 380px;

            padding: 40px;

            background: rgba(255,255,255,0.10);

            backdrop-filter: blur(15px);

            border: 1px solid rgba(255,255,255,0.2);

            border-radius: 20px;

            box-shadow: 0 20px 50px rgba(0,0,0,0.4);

            animation: boxAppear 1s ease;

        }


        @keyframes boxAppear {

            from {

                opacity: 0;

                transform: translateY(50px) scale(0.9);

            }

            to {

                opacity: 1;

                transform: translateY(0) scale(1);

            }

        }


        h1 {

            text-align: center;

            color: #00ffff;

            margin-bottom: 10px;

        }


        .subtitle {

            text-align: center;

            color: #bdefff;

            margin-bottom: 30px;

        }


        label {

            display: block;

            color: white;

            margin-bottom: 7px;

        }


        input {

            width: 100%;

            padding: 13px;

            margin-bottom: 20px;

            border: none;

            border-radius: 8px;

            outline: none;

        }


        input:focus {

            box-shadow: 0 0 10px #00ffff;

        }


        .login-button {

            width: 100%;

            padding: 13px;

            border: 1px solid #00ffff;

            border-radius: 25px;

            background: rgba(0,255,255,0.15);

            color: #00ffff;

            font-size: 16px;

            cursor: pointer;

            transition: 0.3s;

        }


        .login-button:hover {

            background: #00ffff;

            color: #000;

            box-shadow: 0 0 20px #00ffff;

        }


        .error {

            color: #ff7777;

            text-align: center;

            margin-bottom: 15px;

        }

        .password-container {
        position: relative;
        width: 100%;
        margin-bottom: 20px;
    }

    .password-container input {
        width: 100%;
        padding: 13px 70px 13px 13px;
        margin-bottom: 0;
        border: none;
        border-radius: 8px;
        outline: none;
    }

    #showPassword {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);

        border: none;
        background: transparent;

        color: #008b8b;
        font-size: 13px;
        font-weight: bold;

        cursor: pointer;
        padding: 8px;

        z-index: 999;
    }

    #showPassword:hover {
        color: #000;
    }


        .google-login {
            margin-top: 20px;
            text-align: center;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
            color: #bdefff;
            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.3);
        }

    </style>

    <script src="https://accounts.google.com/gsi/client" async defer></script>

</head>


<body>

<script>

function showHidePassword()
{
    var password = document.getElementById("password");
    var button = document.getElementById("showPassword");

    if (password.type === "password")
    {
        password.type = "text";
        button.innerText = "Hide";
    }
    else
    {
        password.type = "password";
        button.innerText = "Show";
    }
}

</script>


<div class="login-box">

    <h1>Inventory System</h1>

    <p class="subtitle">Login to your account</p>


    <?php

    if (isset($error))
    {
        echo "<div class='error'>$error</div>";
    }

    ?>


    <form method="POST" action="">


        <label>Email</label>

        <input
            type="email"
            name="email"
            placeholder="Enter your email"
            required
        >


    <label>Password</label>

    <div class="password-container">

        <input
            type="password"
            name="password"
            id="password"
            placeholder="Enter your password"
            required
        >

        <button
            type="button"
            id="showPassword"
            onclick="showHidePassword()">
            Show
        </button>

    </div>

        <button
            type="submit"
            name="login"
            class="login-button">

            Login

        </button>


    </form>

        <div class="divider">OR</div>

        <div class="google-login">
            <div id="g_id_onload"
                 data-client_id="4260594162-25q32plpgkqodjjujg6tn0m78m7ltbn9.apps.googleusercontent.com"
                 data-login_uri="http://localhost/inventory_management/login.php"
                 data-auto_prompt="false">
            </div>

            <div class="g_id_signin"
                 data-type="standard"
                 data-size="large"
                 data-theme="outline"
                 data-text="continue_with"
                 data-shape="rectangular"
                 data-logo_alignment="left">
            </div>
        </div>

</div>


</body>

</html>