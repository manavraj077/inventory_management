<?php

include 'db.php';


// Total Products
$sql = "SELECT COUNT(*) AS total FROM products";
$result = mysqli_query($data, $sql);
$row = mysqli_fetch_assoc($result);
$total_products = $row['total'];


// Total Stock
$sql = "SELECT SUM(quantity) AS total_stock FROM products";
$result = mysqli_query($data, $sql);
$row = mysqli_fetch_assoc($result);

$total_stock = $row['total_stock'];

if ($total_stock == NULL)
{
    $total_stock = 0;
}


// Categories
$sql = "SELECT COUNT(DISTINCT category) AS total_categories FROM products";
$result = mysqli_query($data, $sql);
$row = mysqli_fetch_assoc($result);

$total_categories = $row['total_categories'];

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Inventory Management System</title>


    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family: Arial, sans-serif;

            color: white;

            background: linear-gradient(
                -45deg,
                #0f2027,
                #203a43,
                #2c5364,
                #0f2027
            );

            background-size: 400% 400%;

            animation: backgroundMove 12s ease infinite;

            min-height: 100vh;

        }


        /* =========================
           BACKGROUND
        ========================= */

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


        /* =========================
           NAVBAR
        ========================= */

        .navbar {

            background: rgba(0,0,0,0.65);

            border: none;

            border-radius: 0;

            margin-bottom: 0;

            box-shadow: 0 5px 20px rgba(0,0,0,0.3);

        }


        .navbar-brand {

            color: #00ffff !important;

            font-size: 22px;

            font-weight: bold;

        }


        .navbar-nav > li > a {

            color: white !important;

            transition: 0.3s;

        }


        .navbar-nav > li > a:hover {

            color: #00ffff !important;

            background: rgba(0,255,255,0.1) !important;

        }


        .login-button {

            border: 1px solid #00ffff;

            border-radius: 25px;

            margin-top: 10px;

            padding: 8px 20px !important;

            margin-right: 10px;

        }


        .login-button:hover {

            background: #00ffff !important;

            color: #000 !important;

            box-shadow: 0 0 15px #00ffff;

        }


        /* =========================
           HERO SECTION
        ========================= */

        .hero {

            min-height: 550px;

            display: flex;

            align-items: center;

            justify-content: center;

            text-align: center;

            padding: 80px 20px;

        }


        .hero-content {

            animation: heroAppear 1.2s ease;

        }


        @keyframes heroAppear {

            from {

                opacity: 0;

                transform: translateY(50px);

            }

            to {

                opacity: 1;

                transform: translateY(0);

            }

        }


        .hero-icon {

            font-size: 70px;

            margin-bottom: 20px;

            animation: floating 3s ease-in-out infinite;

        }


        @keyframes floating {

            0%, 100% {

                transform: translateY(0);

            }

            50% {

                transform: translateY(-15px);

            }

        }


        .hero h1 {

            font-size: 55px;

            font-weight: bold;

            color: white;

            margin-bottom: 20px;

        }


        .hero h1 span {

            color: #00ffff;

        }


        .hero p {

            font-size: 20px;

            color: #bdefff;

            max-width: 700px;

            margin: auto;

            line-height: 1.7;

        }


        .hero-button {

            display: inline-block;

            margin-top: 35px;

            padding: 14px 35px;

            border: 1px solid #00ffff;

            border-radius: 30px;

            color: #00ffff;

            text-decoration: none;

            font-size: 17px;

            transition: 0.3s;

        }


        .hero-button:hover {

            background: #00ffff;

            color: #000;

            text-decoration: none;

            box-shadow: 0 0 25px #00ffff;

            transform: translateY(-3px);

        }


        /* =========================
           SECTION
        ========================= */

        .section {

            padding: 70px 20px;

        }


        .section-title {

            text-align: center;

            font-size: 35px;

            font-weight: bold;

            color: #00ffff;

            margin-bottom: 15px;

        }


        .section-subtitle {

            text-align: center;

            color: #bdefff;

            margin-bottom: 45px;

            font-size: 17px;

        }


        /* =========================
           STAT CARDS
        ========================= */

        .stat-card {

            text-align: center;

            padding: 30px 20px;

            margin-bottom: 20px;

            background: rgba(255,255,255,0.10);

            border: 1px solid rgba(255,255,255,0.2);

            border-radius: 18px;

            backdrop-filter: blur(10px);

            box-shadow: 0 10px 30px rgba(0,0,0,0.3);

            transition: 0.4s;

        }


        .stat-card:hover {

            transform: translateY(-8px);

            border-color: #00ffff;

            box-shadow: 0 0 25px rgba(0,255,255,0.25);

        }


        .stat-icon {

            font-size: 40px;

            margin-bottom: 15px;

        }


        .stat-number {

            font-size: 40px;

            font-weight: bold;

            color: white;

        }


        .stat-name {

            color: #bdefff;

            font-size: 16px;

        }


        /* =========================
           ABOUT
        ========================= */

        .about-box {

            max-width: 900px;

            margin: auto;

            padding: 35px;

            text-align: center;

            background: rgba(255,255,255,0.08);

            border: 1px solid rgba(255,255,255,0.2);

            border-radius: 20px;

            backdrop-filter: blur(10px);

        }


        .about-box p {

            color: #d8f7ff;

            font-size: 17px;

            line-height: 1.8;

        }


        /* =========================
           FEATURES
        ========================= */

        .feature-card {

            text-align: center;

            padding: 30px 20px;

            min-height: 210px;

            background: rgba(255,255,255,0.08);

            border-radius: 18px;

            border: 1px solid rgba(255,255,255,0.15);

            transition: 0.4s;

        }


        .feature-card:hover {

            transform: translateY(-8px);

            border-color: #00ffff;

        }


        .feature-icon {

            font-size: 45px;

            margin-bottom: 15px;

        }


        .feature-card h3 {

            color: #00ffff;

        }


        .feature-card p {

            color: #bdefff;

            line-height: 1.6;

        }


        /* =========================
           LOGIN SECTION
        ========================= */

        .login-section {

            text-align: center;

            padding: 80px 20px;

        }


        .login-section h2 {

            font-size: 35px;

            color: white;

        }


        .login-section p {

            color: #bdefff;

            font-size: 17px;

        }


        /* =========================
           FOOTER
        ========================= */

        footer {

            text-align: center;

            padding: 25px;

            background: rgba(0,0,0,0.6);

            color: #bdefff;

        }


        footer span {

            color: #00ffff;

        }


        /* =========================
           MOBILE
        ========================= */

        @media(max-width: 768px) {

            .hero h1 {

                font-size: 38px;

            }

            .hero p {

                font-size: 17px;

            }

        }

    </style>

</head>


<body>


<!-- =========================
     NAVBAR
========================= -->

<nav class="navbar navbar-inverse">

    <div class="container-fluid">


        <div class="navbar-header">

            <a class="navbar-brand" href="index.php">

                📦 Inventory System

            </a>

        </div>


        <ul class="nav navbar-nav navbar-right">

            <li>
                <a href="#about">
                    About
                </a>
            </li>

            <li>
                <a href="#features">
                    Features
                </a>
            </li>

            <li>
                <a href="login.php" class="login-button">
                    🔐 Login
                </a>
            </li>

        </ul>


    </div>

</nav>


<!-- =========================
     HERO
========================= -->

<section class="hero">

    <div class="hero-content">

        <div class="hero-icon">
            📦
        </div>


        <h1>

            Smart <span>Inventory</span>

            <br>

            Management System

        </h1>


        <p>

            Manage your products, monitor stock levels,
            and keep your inventory organized from one
            simple and powerful system.

        </p>


        <a href="login.php" class="hero-button">

            🔐 Login to System

        </a>

    </div>

</section>


<!-- =========================
     INVENTORY STATISTICS
========================= -->

<section class="section">

    <h2 class="section-title">
        Our Inventory
    </h2>


    <p class="section-subtitle">

        A quick look at the current inventory

    </p>


    <div class="container">

        <div class="row">


            <!-- PRODUCTS -->

            <div class="col-md-4">

                <div class="stat-card">

                    <div class="stat-icon">
                        📦
                    </div>

                    <div class="stat-number">

                        <?php echo $total_products; ?>

                    </div>

                    <div class="stat-name">
                        Total Products
                    </div>

                </div>

            </div>


            <!-- STOCK -->

            <div class="col-md-4">

                <div class="stat-card">

                    <div class="stat-icon">
                        📊
                    </div>

                    <div class="stat-number">

                        <?php echo $total_stock; ?>

                    </div>

                    <div class="stat-name">
                        Items in Stock
                    </div>

                </div>

            </div>


            <!-- CATEGORIES -->

            <div class="col-md-4">

                <div class="stat-card">

                    <div class="stat-icon">
                        🗂️
                    </div>

                    <div class="stat-number">

                        <?php echo $total_categories; ?>

                    </div>

                    <div class="stat-name">
                        Product Categories
                    </div>

                </div>

            </div>


        </div>

    </div>

</section>


<!-- =========================
     ABOUT
========================= -->

<section class="section" id="about">

    <h2 class="section-title">
        About Our System
    </h2>


    <p class="section-subtitle">
        Simple inventory management for better organization
    </p>


    <div class="about-box">

        <p>

            Our Inventory Management System helps businesses
            organize and manage their products efficiently.
            It provides a central place to keep track of
            products, categories, prices, suppliers and
            available stock.

        </p>


        <p>

            Administrators can add, update and remove products,
            while customers can access the system through
            their own accounts.

        </p>

    </div>

</section>


<!-- =========================
     FEATURES
========================= -->

<section class="section" id="features">

    <h2 class="section-title">
        System Features
    </h2>


    <p class="section-subtitle">
        Everything you need to manage your inventory
    </p>


    <div class="container">

        <div class="row">


            <div class="col-md-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        📦
                    </div>

                    <h3>
                        Product Management
                    </h3>

                    <p>
                        Add, update and delete products
                        from your inventory easily.
                    </p>

                </div>

            </div>


            <div class="col-md-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        📊
                    </div>

                    <h3>
                        Stock Tracking
                    </h3>

                    <p>
                        Monitor product quantities and
                        keep track of available stock.
                    </p>

                </div>

            </div>


            <div class="col-md-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        🔐
                    </div>

                    <h3>
                        Secure Login
                    </h3>

                    <p>
                        Separate access for administrators
                        and customers.
                    </p>

                </div>

            </div>


        </div>

    </div>

</section>


<!-- =========================
     LOGIN
========================= -->

<section class="login-section">

    <h2>
        Ready to manage your inventory?
    </h2>


    <p>
        Login to access your Inventory Management System.
    </p>


    <a href="login.php" class="hero-button">

        🔐 Login Now

    </a>

</section>


<!-- =========================
     FOOTER
========================= -->

<footer>

    © 2026

    <span>
        Inventory Management System
    </span>

    — All Rights Reserved

</footer>


</body>

</html>