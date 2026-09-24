<?php

session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['usertype']))
{
    header("Location: login.php");
    exit();
}

if ($_SESSION['usertype'] != 'customer')
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

    <title>Customer Dashboard</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>

        * {
            box-sizing: border-box;
        }

        body {

            margin: 0;

            font-family: Arial, sans-serif;

            min-height: 100vh;

            background:
                linear-gradient(
                    -45deg,
                    #0f2027,
                    #203a43,
                    #2c5364,
                    #0f2027
                );

            background-size: 400% 400%;

            animation: backgroundMove 12s ease infinite;

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


        /* NAVBAR */

        .navbar {

            display: flex;

            justify-content: space-between;

            align-items: center;

            padding: 18px 40px;

            background: rgba(0, 0, 0, 0.35);

            backdrop-filter: blur(12px);

            border-bottom: 1px solid rgba(255,255,255,0.15);

            position: sticky;

            top: 0;

            z-index: 100;
        }


        .logo {

            color: #00ffff;

            font-size: 24px;

            font-weight: bold;
        }


        .nav-right {

            display: flex;

            align-items: center;

            gap: 20px;
        }


        .welcome {

            color: #bdefff;

            font-size: 15px;
        }


        .logout {

            text-decoration: none;

            color: #00ffff;

            border: 1px solid #00ffff;

            padding: 9px 18px;

            border-radius: 20px;

            transition: 0.3s;
        }


        .logout:hover {

            background: #00ffff;

            color: #000;

            box-shadow: 0 0 15px #00ffff;
        }


        /* HEADER */

        .header {

            text-align: center;

            padding: 45px 20px 25px;
        }


        .header h1 {

            margin: 0;

            font-size: 38px;

            color: #00ffff;

            text-shadow: 0 0 15px rgba(0,255,255,0.4);
        }


        .header p {

            color: #c8f7ff;

            margin-top: 10px;

            font-size: 17px;
        }


        /* PRODUCTS */

        .products-container {

            width: 90%;

            max-width: 1200px;

            margin: auto;

            display: grid;

            grid-template-columns:
                repeat(auto-fit, minmax(260px, 1fr));

            gap: 25px;

            padding-bottom: 50px;
        }


        .product-card {

            background: rgba(255,255,255,0.10);

            backdrop-filter: blur(15px);

            border: 1px solid rgba(255,255,255,0.15);

            border-radius: 18px;

            overflow: hidden;

            box-shadow: 0 15px 35px rgba(0,0,0,0.3);

            transition: 0.4s;

            animation: cardAppear 0.7s ease;
        }


        .product-card:hover {

            transform: translateY(-8px);

            box-shadow:
                0 20px 40px rgba(0,0,0,0.4),
                0 0 20px rgba(0,255,255,0.2);
        }


        @keyframes cardAppear {

            from {

                opacity: 0;

                transform: translateY(30px);
            }

            to {

                opacity: 1;

                transform: translateY(0);
            }

        }


        .product-image {

            width: 100%;

            height: 220px;

            object-fit: cover;

            background: #123;
        }


        .product-info {

            padding: 20px;
        }


        .product-name {

            color: #00ffff;

            font-size: 22px;

            font-weight: bold;

            margin-bottom: 8px;
        }


        .category {

            display: inline-block;

            background: rgba(0,255,255,0.15);

            color: #bdefff;

            padding: 5px 10px;

            border-radius: 15px;

            font-size: 12px;

            margin-bottom: 12px;
        }


        .price {

            font-size: 21px;

            font-weight: bold;

            color: white;

            margin: 8px 0;
        }


        .stock {

            color: #bdefff;

            font-size: 14px;

            margin-bottom: 10px;
        }


        .description {

            color: #d8faff;

            font-size: 14px;

            line-height: 1.5;

            min-height: 45px;

            margin-bottom: 18px;
        }


        .order-button {

            display: block;

            width: 100%;

            text-align: center;

            text-decoration: none;

            padding: 12px;

            border-radius: 25px;

            border: 1px solid #00ffff;

            background: rgba(0,255,255,0.12);

            color: #00ffff;

            font-size: 15px;

            font-weight: bold;

            transition: 0.3s;
        }


        .order-button:hover {

            background: #00ffff;

            color: #000;

            box-shadow: 0 0 20px #00ffff;
        }


        .out-of-stock {

            display: block;

            text-align: center;

            padding: 12px;

            border-radius: 25px;

            background: rgba(255,80,80,0.15);

            color: #ff8888;

            border: 1px solid #ff6666;

            font-weight: bold;
        }


        /* NO PRODUCTS */

        .no-products {

            grid-column: 1 / -1;

            text-align: center;

            padding: 50px;

            background: rgba(255,255,255,0.08);

            border-radius: 15px;

            color: #bdefff;

        }


        @media (max-width: 600px) {

            .navbar {

                padding: 15px 20px;

            }

            .logo {

                font-size: 18px;

            }

            .welcome {

                display: none;

            }

            .header h1 {

                font-size: 30px;

            }

        }

    </style>

</head>


<body>


<!-- NAVBAR -->

<nav class="navbar">

    <div class="logo">
        Inventory System
    </div>

    <div class="nav-right">

        <span class="welcome">
            Welcome,
            <?php echo htmlspecialchars($_SESSION['username']); ?>
        </span>

        <a href="logout.php" class="logout">
            Logout
        </a>

    </div>

</nav>


<!-- HEADER -->

<div class="header">

    <h1>Customer Dashboard</h1>

    <p>Browse our products and place your order</p>

</div>


<!-- PRODUCTS -->

<div class="products-container">


<?php

if (mysqli_num_rows($result) > 0)
{

    while ($info = mysqli_fetch_assoc($result))
    {

?>

        <div class="product-card">

            <?php

            if (!empty($info['image']) &&
                file_exists($info['image']))
            {

            ?>

                <img
                    src="<?php echo htmlspecialchars($info['image']); ?>"
                    class="product-image"
                    alt="Product Image"
                >

            <?php

            }
            else
            {

            ?>

                <div
                    class="product-image"
                    style="
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        color:#8eeeff;
                    "
                >
                    No Image
                </div>

            <?php

            }

            ?>


            <div class="product-info">

                <div class="product-name">

                    <?php
                    echo htmlspecialchars($info['product_name']);
                    ?>

                </div>


                <span class="category">

                    <?php
                    echo htmlspecialchars($info['category']);
                    ?>

                </span>


                <div class="price">

                    ₹<?php
                    echo number_format($info['price'], 2);
                    ?>

                </div>


                <div class="stock">

                    Available:
                    <?php
                    echo $info['quantity'];
                    ?>
                    units

                </div>


                <div class="description">

                    <?php

                    if (!empty($info['description']))
                    {
                        echo htmlspecialchars($info['description']);
                    }
                    else
                    {
                        echo "No description available.";
                    }

                    ?>

                </div>


                <?php

                if ($info['quantity'] > 0)
                {

                ?>

                    <a
                        href="order_product.php?product_id=<?php echo $info['id']; ?>"
                        class="order-button"
                    >
                        Order Now
                    </a>

                <?php

                }
                else
                {

                ?>

                    <div class="out-of-stock">

                        Out of Stock

                    </div>

                <?php

                }

                ?>

            </div>

        </div>


<?php

    }

}
else
{

?>

    <div class="no-products">

        <h2>No Products Available</h2>

        <p>Please check again later.</p>

    </div>

<?php

}

?>


</div>


</body>

</html>