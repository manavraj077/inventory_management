<?php

session_start();


// CUSTOMER LOGIN CHECK
if (!isset($_SESSION['username']) || !isset($_SESSION['usertype']))
{
    header("Location: login.php");
    exit();
}


// ONLY CUSTOMER CAN ORDER
if ($_SESSION['usertype'] != 'customer')
{
    header("Location: login.php");
    exit();
}


include 'db.php';


// CHECK PRODUCT ID
if (!isset($_GET['product_id']))
{
    header("Location: customer.php");
    exit();
}


$product_id = intval($_GET['product_id']);


// GET PRODUCT
$sql = "SELECT * FROM products WHERE id = ?";

$stmt = mysqli_prepare($data, $sql);

mysqli_stmt_bind_param($stmt, "i", $product_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$product = mysqli_fetch_assoc($result);


if (!$product)
{
    die("Product not found.");
}


// PLACE ORDER
if (isset($_POST['place_order']))
{
    $quantity = intval($_POST['quantity']);

    // Check quantity
    if ($quantity <= 0)
    {
        $error = "Please enter a valid quantity.";
    }

    // Check stock
    elseif ($quantity > $product['quantity'])
    {
        $error = "Only " . $product['quantity'] . " units are available.";
    }

    else
    {
        /*
        Start transaction so that stock and order
        are updated together.
        */

        mysqli_begin_transaction($data);

        try
        {
            // Get latest stock and lock the product row
            $stock_sql = "SELECT price, quantity
                          FROM products
                          WHERE id = ?
                          FOR UPDATE";

            $stock_stmt = mysqli_prepare($data, $stock_sql);

            mysqli_stmt_bind_param(
                $stock_stmt,
                "i",
                $product_id
            );

            mysqli_stmt_execute($stock_stmt);

            $stock_result = mysqli_stmt_get_result($stock_stmt);

            $latest_product = mysqli_fetch_assoc($stock_result);


            if (!$latest_product)
            {
                throw new Exception("Product not found.");
            }


            // Check latest stock
            if ($quantity > $latest_product['quantity'])
            {
                throw new Exception(
                    "Only " .
                    $latest_product['quantity'] .
                    " units are available."
                );
            }


            // Calculate total
            $total_price =
                $latest_product['price'] * $quantity;


            // Customer ID from session
            $customer_id = $_SESSION['user_id'];


            // INSERT ORDER
            $order_sql = "INSERT INTO orders
                          (customer_id, product_id, quantity,
                           total_price, status)
                          VALUES (?, ?, ?, ?, 'Pending')";

            $order_stmt = mysqli_prepare(
                $data,
                $order_sql
            );

            mysqli_stmt_bind_param(
                $order_stmt,
                "iiid",
                $customer_id,
                $product_id,
                $quantity,
                $total_price
            );

            if (!mysqli_stmt_execute($order_stmt))
            {
                throw new Exception(
                    "Order could not be placed."
                );
            }


            // REDUCE PRODUCT STOCK
            $new_quantity =
                $latest_product['quantity'] - $quantity;


            $update_sql = "UPDATE products
                           SET quantity = ?
                           WHERE id = ?";

            $update_stmt = mysqli_prepare(
                $data,
                $update_sql
            );

            mysqli_stmt_bind_param(
                $update_stmt,
                "ii",
                $new_quantity,
                $product_id
            );


            if (!mysqli_stmt_execute($update_stmt))
            {
                throw new Exception(
                    "Stock could not be updated."
                );
            }


            // Everything successful
            mysqli_commit($data);


            echo "<script>

                    alert('Order placed successfully!');

                    window.location.href='customer.php';

                  </script>";

            exit();
        }

        catch (Exception $e)
        {
            mysqli_rollback($data);

            $error = $e->getMessage();
        }
    }
}

?>


<!DOCTYPE html>
<html>

<head>

    <title>Order Product</title>

    <meta name="viewport"
          content="width=device-width, initial-scale=1">


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

            display: flex;

            justify-content: center;

            align-items: center;

            padding: 30px;
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


        .order-box {

            width: 500px;

            max-width: 100%;

            padding: 35px;

            background:
                rgba(255,255,255,0.10);

            backdrop-filter: blur(15px);

            border:
                1px solid
                rgba(255,255,255,0.2);

            border-radius: 20px;

            box-shadow:
                0 20px 50px
                rgba(0,0,0,0.4);

            animation: boxAppear 0.8s ease;
        }


        @keyframes boxAppear {

            from {

                opacity: 0;

                transform:
                    translateY(40px)
                    scale(0.95);
            }

            to {

                opacity: 1;

                transform:
                    translateY(0)
                    scale(1);
            }

        }


        h1 {

            text-align: center;

            color: #00ffff;

            margin-top: 0;

            margin-bottom: 25px;
        }


        .product-image {

            width: 100%;

            height: 230px;

            object-fit: cover;

            border-radius: 12px;

            margin-bottom: 20px;
        }


        .no-image {

            width: 100%;

            height: 230px;

            display: flex;

            justify-content: center;

            align-items: center;

            background: rgba(0,0,0,0.25);

            border-radius: 12px;

            color: #bdefff;

            margin-bottom: 20px;
        }


        .product-name {

            color: #00ffff;

            font-size: 27px;

            font-weight: bold;

            margin-bottom: 8px;
        }


        .category {

            display: inline-block;

            color: #bdefff;

            background:
                rgba(0,255,255,0.12);

            padding: 6px 12px;

            border-radius: 20px;

            font-size: 13px;

            margin-bottom: 15px;
        }


        .price {

            font-size: 22px;

            font-weight: bold;

            margin-bottom: 8px;
        }


        .stock {

            color: #bdefff;

            margin-bottom: 20px;
        }


        label {

            display: block;

            margin-bottom: 8px;

            font-weight: bold;
        }


        input {

            width: 100%;

            padding: 13px;

            border: none;

            outline: none;

            border-radius: 8px;

            font-size: 16px;

            margin-bottom: 20px;
        }


        input:focus {

            box-shadow:
                0 0 10px #00ffff;
        }


        .total {

            padding: 15px;

            background:
                rgba(0,255,255,0.10);

            border:
                1px solid
                rgba(0,255,255,0.3);

            border-radius: 10px;

            margin-bottom: 20px;

            text-align: center;

            font-size: 20px;

            color: #00ffff;
        }


        .order-button {

            width: 100%;

            padding: 13px;

            border:
                1px solid #00ffff;

            border-radius: 25px;

            background:
                rgba(0,255,255,0.15);

            color: #00ffff;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;

            transition: 0.3s;
        }


        .order-button:hover {

            background: #00ffff;

            color: #000;

            box-shadow:
                0 0 20px #00ffff;
        }


        .back-button {

            display: block;

            text-align: center;

            margin-top: 15px;

            color: #bdefff;

            text-decoration: none;

            font-size: 14px;
        }


        .back-button:hover {

            color: #00ffff;
        }


        .error {

            background:
                rgba(255,80,80,0.15);

            border:
                1px solid #ff6666;

            color: #ff9999;

            padding: 12px;

            border-radius: 8px;

            text-align: center;

            margin-bottom: 20px;
        }

    </style>

</head>


<body>


<div class="order-box">


    <h1>Order Product</h1>


    <?php

    if (isset($error))
    {

        echo "<div class='error'>"
             . htmlspecialchars($error)
             . "</div>";

    }

    ?>


    <!-- PRODUCT IMAGE -->

    <?php

    if (!empty($product['image']) &&
        file_exists($product['image']))
    {

    ?>

        <img
            src="<?php
                echo htmlspecialchars($product['image']);
            ?>"
            class="product-image"
            alt="Product Image"
        >

    <?php

    }
    else
    {

    ?>

        <div class="no-image">
            No Image Available
        </div>

    <?php

    }

    ?>


    <!-- PRODUCT DETAILS -->

    <div class="product-name">

        <?php
        echo htmlspecialchars(
            $product['product_name']
        );
        ?>

    </div>


    <div class="category">

        <?php
        echo htmlspecialchars(
            $product['category']
        );
        ?>

    </div>


    <div class="price">

        Price:
        ₹<?php
        echo number_format(
            $product['price'],
            2
        );
        ?>

    </div>


    <div class="stock">

        Available Stock:
        <?php
        echo $product['quantity'];
        ?>

        units

    </div>


    <?php

    if ($product['quantity'] > 0)
    {

    ?>

        <form method="POST">


            <label>
                Enter Quantity
            </label>


            <input
                type="number"
                name="quantity"
                id="quantity"
                min="1"
                max="<?php
                    echo $product['quantity'];
                ?>"
                value="1"
                required
            >


            <div class="total">

                Total:
                ₹<span id="totalPrice">
                    <?php
                    echo number_format(
                        $product['price'],
                        2
                    );
                    ?>
                </span>

            </div>


            <button
                type="submit"
                name="place_order"
                class="order-button">

                Place Order

            </button>


        </form>


    <?php

    }
    else
    {

    ?>

        <div class="error">

            This product is currently
            out of stock.

        </div>

    <?php

    }

    ?>


    <a
        href="customer.php"
        class="back-button">

        ← Back to Customer Dashboard

    </a>


</div>


<script>

var quantityInput =
    document.getElementById("quantity");

var totalPrice =
    document.getElementById("totalPrice");

var price =
    <?php echo $product['price']; ?>;


if (quantityInput)
{

    quantityInput.addEventListener(
        "input",
        function()
        {

            var quantity =
                parseInt(this.value) || 0;

            var total =
                price * quantity;

            totalPrice.innerText =
                total.toFixed(2);

        }
    );

}

</script>


</body>

</html>