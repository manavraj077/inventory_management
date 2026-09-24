<?php

session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['usertype']))
{
    header("Location: login.php");
    exit();
}

if ($_SESSION['usertype'] != 'admin')
{
    header("Location: login.php");
    exit();
}

include 'db.php';


// =========================
// TOTAL PRODUCTS
// =========================

$sql_products = "SELECT COUNT(*) AS total FROM products";

$result_products = mysqli_query($data, $sql_products);

if (!$result_products)
{
    die("Product Query Error: " . mysqli_error($data));
}

$row_products = mysqli_fetch_assoc($result_products);

$total_products = $row_products['total'];


// =========================
// TOTAL STOCK
// =========================

$sql_stock = "SELECT SUM(quantity) AS total_stock FROM products";

$result_stock = mysqli_query($data, $sql_stock);

if (!$result_stock)
{
    die("Stock Query Error: " . mysqli_error($data));
}

$row_stock = mysqli_fetch_assoc($result_stock);

$total_stock = $row_stock['total_stock'];


// If there are no products
if ($total_stock == NULL)
{
    $total_stock = 0;
}


// =========================
// LOW STOCK
// =========================

$sql_low = "SELECT COUNT(*) AS low_stock
            FROM products
            WHERE quantity < 10";

$result_low = mysqli_query($data, $sql_low);

if (!$result_low)
{
    die("Low Stock Query Error: " . mysqli_error($data));
}

$row_low = mysqli_fetch_assoc($result_low);

$low_stock = $row_low['low_stock'];

?>



<!DOCTYPE html>
<html>

<head>

    <title>Inventory Dashboard</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <style>

        /* =========================
           BODY
        ========================= */

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(-45deg,
                        #0f2027,
                        #203a43,
                        #2c5364,
                        #0f2027);

            background-size: 400% 400%;

            animation: gradientBG 12s ease infinite;

            min-height: 100vh;

            overflow-x: hidden;
        }


        /* =========================
           BACKGROUND ANIMATION
        ========================= */

        @keyframes gradientBG {

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


        /* Floating circles */

        .circle {
            position: fixed;

            border-radius: 50%;

            background: rgba(255,255,255,0.08);

            animation: float 8s infinite ease-in-out;

            z-index: 0;
        }


        .circle1 {
            width: 180px;
            height: 180px;

            top: 100px;
            left: 5%;

            animation-delay: 0s;
        }


        .circle2 {
            width: 250px;
            height: 250px;

            bottom: 50px;
            right: 5%;

            animation-delay: 2s;
        }


        .circle3 {
            width: 100px;
            height: 100px;

            top: 50%;
            right: 30%;

            animation-delay: 4s;
        }


        @keyframes float {

            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-30px);
            }

            100% {
                transform: translateY(0px);
            }

        }


        /* =========================
           NAVBAR
        ========================= */

        .top-navbar {
            width: 100%;
            min-height: 75px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 8px 20px;

            background: rgba(0, 0, 0, 0.55);

            border-bottom: 1px solid rgba(255,255,255,0.1);

            box-sizing: border-box;
        }


        /* LEFT SIDE */

        .brand-section {
            display: flex;
            flex-direction: column;

            justify-content: center;
        }


        .brand {
            color: #00ffff;

            font-size: 24px;

            font-weight: bold;

            line-height: 32px;
        }


        .welcome {
            color: #bdefff;

            font-size: 17px;

            margin-top: 2px;
        }


        /* RIGHT SIDE */

        .menu {
            display: flex;

            align-items: center;

            gap: 28px;
        }


        .menu a {
            color: white;

            text-decoration: none;

            font-size: 16px;

            font-weight: bold;

            transition: 0.3s;
        }


        .menu a:hover {
            color: #00ffff;
        }


        /* LOGOUT */

        .logout-btn {
            color: #00ffff !important;

            border: 1px solid #00ffff;

            padding: 8px 16px;

            border-radius: 20px;

            white-space: nowrap;

            transition: 0.3s;
        }


        .logout-btn:hover {
            background: #00ffff;

            color: #000 !important;

            box-shadow: 0 0 15px #00ffff;
        }


        /* =========================
           DASHBOARD
        ========================= */

        .dashboard {

            position: relative;

            z-index: 2;

            padding-top: 60px;

        }


        .dashboard-title {

            color: white;

            text-align: center;

            font-size: 40px;

            font-weight: bold;

            margin-bottom: 10px;

            animation: titleAnimation 1.2s ease;

        }


        .dashboard-subtitle {

            color: #bdefff;

            text-align: center;

            font-size: 17px;

            margin-bottom: 50px;

            animation: titleAnimation 1.6s ease;

        }


        @keyframes titleAnimation {

            from {

                opacity: 0;

                transform: translateY(-30px);

            }

            to {

                opacity: 1;

                transform: translateY(0);

            }

        }


        /* =========================
           CARDS
        ========================= */

        .dashboard-card {

            position: relative;

            padding: 35px 20px;

            text-align: center;

            color: white;

            background: rgba(255,255,255,0.10);

            backdrop-filter: blur(15px);

            -webkit-backdrop-filter: blur(15px);

            border: 1px solid rgba(255,255,255,0.2);

            border-radius: 20px;

            box-shadow: 0 15px 35px rgba(0,0,0,0.35);

            overflow: hidden;

            transition: all 0.4s ease;

            opacity: 0;

            animation: cardAppear 1s forwards;

        }


        .card1 {
            animation-delay: 0.3s;
        }


        .card2 {
            animation-delay: 0.6s;
        }


        .card3 {
            animation-delay: 0.9s;
        }


        @keyframes cardAppear {

            from {

                opacity: 0;

                transform: translateY(60px) scale(0.9);

            }

            to {

                opacity: 1;

                transform: translateY(0) scale(1);

            }

        }


        /* Hover animation */

        .dashboard-card:hover {

            transform: translateY(-12px) scale(1.03);

            box-shadow:
                0 20px 50px rgba(0,0,0,0.5),
                0 0 25px rgba(0,255,255,0.3);

            border-color: #00ffff;

        }


        /* Card shine */

        .dashboard-card::before {

            content: "";

            position: absolute;

            top: -100%;

            left: -100%;

            width: 50%;

            height: 300%;

            background: rgba(255,255,255,0.15);

            transform: rotate(25deg);

            transition: 0.7s;

        }


        .dashboard-card:hover::before {

            left: 150%;

        }


        /* =========================
           ICON
        ========================= */

        .card-icon {

            font-size: 45px;

            margin-bottom: 15px;

            color: #00ffff;

            animation: iconFloat 3s infinite ease-in-out;

        }


        @keyframes iconFloat {

            0%,100% {

                transform: translateY(0);

            }

            50% {

                transform: translateY(-8px);

            }

        }


        /* =========================
           NUMBER
        ========================= */

        .number {

            font-size: 48px;

            font-weight: bold;

            color: white;

            margin: 10px 0;

        }


        .card-title {

            font-size: 18px;

            color: #bdefff;

        }


        /* =========================
           LOW STOCK
        ========================= */

        .low-stock {

            color: #ff7070;

        }


        /* =========================
           BUTTON
        ========================= */

        .dashboard-button {

            margin-top: 45px;

            text-align: center;

            animation: titleAnimation 2s ease;

        }


        .dashboard-button a {

            padding: 12px 25px;

            border-radius: 30px;

            background: rgba(0,255,255,0.15);

            border: 1px solid #00ffff;

            color: #00ffff;

            text-decoration: none;

            transition: 0.3s;

        }


        .dashboard-button a:hover {

            background: #00ffff;

            color: #000;

            box-shadow: 0 0 25px #00ffff;

        }


    </style>

</head>


<body>


<!-- Floating Background -->

<div class="circle circle1"></div>

<div class="circle circle2"></div>

<div class="circle circle3"></div>


<!-- NAVBAR -->

<nav class="top-navbar">

    <div class="brand-section">

        <div class="brand">
            Inventory System
        </div>

        <div class="welcome">
            Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
        </div>

    </div>


    <div class="menu">

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="add_product.php">
            Add Product
        </a>

        <a href="view_product.php">
            Products
        </a>

        <a href="order.php">
            Orders
        </a>

        <a href="logout.php" class="logout-btn">
            Logout
        </a>

    </div>

</nav>


<!-- DASHBOARD -->

<div class="container dashboard">


    <h1 class="dashboard-title">
        Inventory Dashboard
    </h1>


  <br>
  

    <div class="row">


        <!-- TOTAL PRODUCTS -->

        <div class="col-md-4">

            <div class="dashboard-card card1">

                <div class="card-icon">
                    📦
                </div>

                <div class="number" id="products">
                    0
                </div>

                <div class="card-title">
                    Total Products
                </div>

            </div>

        </div>


        <!-- TOTAL STOCK -->

        <div class="col-md-4">

            <div class="dashboard-card card2">

                <div class="card-icon">
                    📊
                </div>

                <div class="number" id="stock">
                    0
                </div>

                <div class="card-title">
                    Total Stock
                </div>

            </div>

        </div>


        <!-- LOW STOCK -->

        <div class="col-md-4">

            <div class="dashboard-card card3">

                <div class="card-icon">
                    ⚠️
                </div>

                <div class="number low-stock" id="lowstock">
                    0
                </div>

                <div class="card-title">
                    Low Stock Products
                </div>

            </div>

        </div>


    </div>


    <div class="dashboard-button">

        <a href="add_product.php">
            + Add New Product
        </a>

        &nbsp;&nbsp;

        <a href="view_product.php">
            View Products
        </a>

    </div>


</div>


<!-- COUNTING ANIMATION -->

<script>

function animateNumber(elementId, target) {

    let element = document.getElementById(elementId);

    let current = 0;

    let increment = Math.ceil(target / 50);

    let timer = setInterval(function() {

        current += increment;

        if (current >= target) {

            current = target;

            clearInterval(timer);

        }

        element.innerText = current;

    }, 30);

}


// Start animations

animateNumber("products", <?php echo $total_products; ?>);

animateNumber("stock", <?php echo $total_stock; ?>);

animateNumber("lowstock", <?php echo $low_stock; ?>);

</script>


</body>

</html>