<?php

session_start();



if (!isset($_SESSION['username']) ||
    !isset($_SESSION['usertype']))
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

$sql = "SELECT * FROM products ORDER BY id DESC";

$result = mysqli_query($data, $sql);

if (!$result)
{
    die("Query Failed: " . mysqli_error($data));
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>View Products</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">


    <style>

        /* =========================
           BODY
        ========================= */

        body {

            margin: 0;

            font-family: Arial, sans-serif;

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

            color: white;

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


        /* =========================
           NAVBAR
        ========================= */

        .navbar {

            background: rgba(0,0,0,0.65);

            border: none;

            border-radius: 0;

            box-shadow: 0 5px 20px rgba(0,0,0,0.4);

        }


        .navbar-brand {

            color: #00ffff !important;

            font-weight: bold;

            font-size: 22px;

        }


        .navbar-nav > li > a {

            color: white !important;

            transition: 0.3s;

        }


        .navbar-nav > li > a:hover {

            color: #00ffff !important;

            background: rgba(0,255,255,0.1) !important;

        }


        /* =========================
           PAGE TITLE
        ========================= */

        .page-title {

            text-align: center;

            margin-top: 50px;

            margin-bottom: 10px;

            font-size: 38px;

            font-weight: bold;

            color: white;

            animation: titleAnimation 1s ease;

        }


        .page-subtitle {

            text-align: center;

            color: #bdefff;

            margin-bottom: 35px;

            animation: titleAnimation 1.4s ease;

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
           TABLE BOX
        ========================= */

        .table-box {

            background: rgba(255,255,255,0.10);

            backdrop-filter: blur(15px);

            -webkit-backdrop-filter: blur(15px);

            border: 1px solid rgba(255,255,255,0.2);

            border-radius: 20px;

            padding: 25px;

            box-shadow: 0 15px 40px rgba(0,0,0,0.4);

            animation: tableAppear 1.2s ease;

        }


        @keyframes tableAppear {

            from {

                opacity: 0;

                transform: translateY(50px);

            }

            to {

                opacity: 1;

                transform: translateY(0);

            }

        }


        /* =========================
           TABLE
        ========================= */

        .table {

            margin-bottom: 0;

            color: white;

        }


        .table thead th {

            background: rgba(0,0,0,0.55);

            color: #00ffff;

            border: none !important;

            padding: 18px !important;

            font-size: 15px;

        }


        .table tbody td {

            vertical-align: middle !important;

            border-color: rgba(255,255,255,0.12) !important;

            padding: 15px !important;

        }


        /* Row animation */

        .table tbody tr {

            transition: all 0.3s ease;

        }


        .table tbody tr:hover {

            background: rgba(0,255,255,0.08);

            transform: scale(1.01);

        }


        /* =========================
           PRODUCT IMAGE
        ========================= */

        .product-image {

            width: 75px;

            height: 75px;

            object-fit: cover;

            border-radius: 12px;

            border: 2px solid rgba(0,255,255,0.6);

            transition: all 0.4s ease;

        }


        .product-image:hover {

            transform: scale(1.2) rotate(2deg);

            border-color: #00ffff;

            box-shadow: 0 0 20px rgba(0,255,255,0.6);

        }


        /* =========================
           PRODUCT NAME
        ========================= */

        .product-name {

            color: #00ffff;

            font-weight: bold;

            font-size: 16px;

        }


        /* =========================
           LOW STOCK
        ========================= */

        .low-stock {

            display: inline-block;

            margin-top: 5px;

            padding: 4px 9px;

            border-radius: 20px;

            background: rgba(255,60,60,0.15);

            border: 1px solid #ff5555;

            color: #ff7777;

            font-size: 11px;

            font-weight: bold;

            animation: warningPulse 1.5s infinite;

        }


        @keyframes warningPulse {

            0% {

                box-shadow: 0 0 0 rgba(255,0,0,0);

            }

            50% {

                box-shadow: 0 0 15px rgba(255,0,0,0.4);

            }

            100% {

                box-shadow: 0 0 0 rgba(255,0,0,0);

            }

        }


        /* =========================
           BUTTONS
        ========================= */

        .btn {

            border-radius: 20px;

            padding: 7px 15px;

            transition: all 0.3s ease;

        }


        .btn-success {

            background: rgba(0,200,120,0.2);

            border: 1px solid #00ff99;

            color: #00ff99;

        }


        .btn-success:hover {

            background: #00ff99;

            color: #000;

            box-shadow: 0 0 15px #00ff99;

            transform: translateY(-2px);

        }


        .btn-danger {

            background: rgba(255,50,50,0.2);

            border: 1px solid #ff5555;

            color: #ff7777;

        }


        .btn-danger:hover {

            background: #ff5555;

            color: white;

            box-shadow: 0 0 15px #ff5555;

            transform: translateY(-2px);

        }


        /* =========================
           BACK BUTTON
        ========================= */

        .back-button {

            margin-top: 30px;

            margin-bottom: 50px;

        }


        .back-button a {

            display: inline-block;

            padding: 11px 25px;

            border-radius: 30px;

            border: 1px solid #00ffff;

            color: #00ffff;

            text-decoration: none;

            transition: 0.3s;

        }


        .back-button a:hover {

            background: #00ffff;

            color: #000;

            box-shadow: 0 0 20px #00ffff;

        }


        /* =========================
           RESPONSIVE
        ========================= */

        @media(max-width: 768px) {

            .table-box {

                overflow-x: auto;

            }

            .page-title {

                font-size: 30px;

            }

        }

    </style>

</head>


<body>


<!-- NAVBAR -->

<nav class="navbar navbar-inverse">

    <div class="container-fluid">

        <div class="navbar-header">

            <a class="navbar-brand" href="dashboard.php">
                Inventory System
            </a>

        </div>


        <ul class="nav navbar-nav navbar-right">

            <li>
                <a href="dashboard.php">
                    Dashboard
                </a>
            </li>

            <li>
                <a href="add_product.php">
                    Add Product
                </a>
            </li>

            <li>
                <a href="view_product.php">
                    Products
                </a>
            </li>

        </ul>

    </div>

</nav>


<!-- MAIN -->

<div class="container">


    <h1 class="page-title">
        Product Inventory
    </h1>


   <br>


    <div class="table-box">

        <div class="table-responsive">

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Image</th>

                        <th>Product Name</th>

                        <th>Category</th>

                        <th>Price</th>

                        <th>Quantity</th>

                        <th>Supplier</th>

                        <th>Description</th>

                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>


                <?php

                while ($info = mysqli_fetch_assoc($result))

                {

                ?>

                    <tr>

                        <td>
                            <?php echo $info['id']; ?>
                        </td>


                        <td>

                            <img
                                src="<?php echo $info['image']; ?>"
                                class="product-image">

                        </td>


                        <td>

                            <span class="product-name">

                                <?php
                                echo $info['product_name'];
                                ?>

                            </span>

                        </td>


                        <td>

                            <?php
                            echo $info['category'];
                            ?>

                        </td>


                        <td>

                            ₹<?php
                            echo number_format($info['price'], 2);
                            ?>

                        </td>


                        <td>

                            <?php
                            echo $info['quantity'];
                            ?>


                            <?php

                            if ($info['quantity'] < 10)

                            {

                                echo '<br>';

                                echo '<span class="low-stock">
                                      LOW STOCK
                                      </span>';

                            }

                            ?>

                        </td>


                        <td>

                            <?php
                            echo $info['supplier'];
                            ?>

                        </td>


                        <td>

                            <?php
                            echo $info['description'];
                            ?>

                        </td>


                        <td>

                            <a
                                href="update_product.php?product_id=<?php echo $info['id']; ?>"
                                class="btn btn-success btn-sm">

                                Update

                            </a>


                            <br><br>


                            <a
                                href="delete_product.php?product_id=<?php echo $info['id']; ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this product?');">

                                Delete

                            </a>

                        </td>

                    </tr>


                <?php

                }

                ?>


                </tbody>

            </table>

        </div>

    </div>


    <!-- BACK BUTTON -->

    <div class="text-center back-button">

        <a href="dashboard.php">

            ← Back to Dashboard

        </a>

    </div>


</div>


</body>

</html>