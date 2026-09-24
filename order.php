<?php

session_start();


// ADMIN LOGIN CHECK
if (!isset($_SESSION['username']) || !isset($_SESSION['usertype']))
{
    header("Location: login.php");
    exit();
}


// ONLY ADMIN CAN ACCESS
if ($_SESSION['usertype'] != 'admin')
{
    header("Location: login.php");
    exit();
}


include 'db.php';


// UPDATE ORDER STATUS
if (isset($_POST['update_status']))
{
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];

    $allowed_status = array(
        "Pending",
        "Confirmed",
        "Shipped",
        "Delivered",
        "Cancelled"
    );

    if (in_array($status, $allowed_status))
    {
        $update_sql = "UPDATE orders
                       SET status = ?
                       WHERE id = ?";

        $stmt = mysqli_prepare($data, $update_sql);

        mysqli_stmt_bind_param(
            $stmt,
            "si",
            $status,
            $order_id
        );

        mysqli_stmt_execute($stmt);
    }

    header("Location: order.php");
    exit();
}


// GET ALL ORDERS
$sql = "SELECT
            orders.id AS order_id,
            orders.quantity,
            orders.total_price,
            orders.status,
            orders.order_date,

            users.username,
            users.email,

            products.product_name,
            products.category

        FROM orders

        INNER JOIN users
            ON orders.customer_id = users.id

        INNER JOIN products
            ON orders.product_id = products.id

        ORDER BY orders.id DESC";


$result = mysqli_query($data, $sql);


if (!$result)
{
    die("Query Failed: " . mysqli_error($data));
}

?>


<!DOCTYPE html>
<html>

<head>

    <title>Orders - Inventory System</title>

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

            padding-bottom: 50px;
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

        .top-navbar {

            width: 100%;

            min-height: 75px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 8px 25px;

            background:
                rgba(0,0,0,0.55);

            border-bottom:
                1px solid
                rgba(255,255,255,0.1);
        }


        .brand-section {

            display: flex;

            flex-direction: column;

            justify-content: center;
        }


        .brand {

            color: #00ffff;

            font-size: 28px;

            font-weight: bold;

            line-height: 32px;
        }


        .welcome {

            color: #bdefff;

            font-size: 13px;

            margin-top: 2px;
        }


        .menu {

            display: flex;

            align-items: center;

            gap: 25px;
        }


        .menu a {

            color: white;

            text-decoration: none;

            font-size: 15px;

            font-weight: bold;

            transition: 0.3s;
        }


        .menu a:hover {

            color: #00ffff;
        }


        .logout-btn {

            color: #00ffff !important;

            border:
                1px solid #00ffff;

            padding: 8px 16px;

            border-radius: 20px;

            transition: 0.3s;
        }


        .logout-btn:hover {

            background: #00ffff;

            color: #000 !important;

            box-shadow:
                0 0 15px #00ffff;
        }


        /* PAGE TITLE */

        .page-header {

            text-align: center;

            padding: 40px 20px 25px;
        }


        .page-header h1 {

            margin: 0;

            color: #00ffff;

            font-size: 36px;

            text-shadow:
                0 0 15px
                rgba(0,255,255,0.4);
        }


        .page-header p {

            color: #bdefff;

            margin-top: 10px;
        }


        /* TABLE CONTAINER */

        .table-container {

            width: 95%;

            max-width: 1400px;

            margin: auto;

            overflow-x: auto;

            background:
                rgba(255,255,255,0.08);

            backdrop-filter: blur(15px);

            border:
                1px solid
                rgba(255,255,255,0.15);

            border-radius: 18px;

            padding: 20px;

            box-shadow:
                0 20px 45px
                rgba(0,0,0,0.3);

            animation: tableAppear 0.8s ease;
        }


        @keyframes tableAppear {

            from {

                opacity: 0;

                transform: translateY(30px);
            }

            to {

                opacity: 1;

                transform: translateY(0);
            }

        }


        table {

            width: 100%;

            border-collapse: collapse;

            min-width: 1000px;
        }


        th {

            background:
                rgba(0,255,255,0.12);

            color: #00ffff;

            padding: 16px 12px;

            text-align: left;

            font-size: 14px;

            border-bottom:
                1px solid
                rgba(0,255,255,0.25);
        }


        td {

            padding: 15px 12px;

            color: #e8fbff;

            border-bottom:
                1px solid
                rgba(255,255,255,0.08);

            vertical-align: middle;
        }


        tr {

            transition: 0.3s;
        }


        tr:hover {

            background:
                rgba(0,255,255,0.06);
        }


        .order-id {

            color: #00ffff;

            font-weight: bold;
        }


        .product-name {

            color: white;

            font-weight: bold;
        }


        .price {

            color: #7fffd4;

            font-weight: bold;
        }


        /* STATUS */

        .status-form {

            display: flex;

            align-items: center;

            gap: 7px;
        }


        .status-select {

            padding: 8px;

            border-radius: 7px;

            border: 1px solid
                rgba(255,255,255,0.2);

            background: #203a43;

            color: white;

            outline: none;

            cursor: pointer;
        }


        .status-select:focus {

            border-color: #00ffff;

            box-shadow:
                0 0 8px
                rgba(0,255,255,0.4);
        }


        .update-btn {

            border: 1px solid #00ffff;

            background:
                rgba(0,255,255,0.12);

            color: #00ffff;

            padding: 8px 12px;

            border-radius: 7px;

            cursor: pointer;

            transition: 0.3s;
        }


        .update-btn:hover {

            background: #00ffff;

            color: #000;

            box-shadow:
                0 0 12px #00ffff;
        }


        /* STATUS COLORS */

        .status {

            display: inline-block;

            padding: 6px 10px;

            border-radius: 15px;

            font-size: 12px;

            font-weight: bold;
        }


        .pending {

            color: #ffd166;

            background:
                rgba(255,209,102,0.12);
        }


        .confirmed {

            color: #00ffff;

            background:
                rgba(0,255,255,0.12);
        }


        .shipped {

            color: #7dd3fc;

            background:
                rgba(125,211,252,0.12);
        }


        .delivered {

            color: #7fffd4;

            background:
                rgba(127,255,212,0.12);
        }


        .cancelled {

            color: #ff8888;

            background:
                rgba(255,80,80,0.12);
        }


        /* NO ORDERS */

        .no-orders {

            text-align: center;

            padding: 60px 20px;

            color: #bdefff;

            font-size: 18px;
        }


        /* MOBILE */

        @media (max-width: 850px) {

            .top-navbar {

                flex-direction: column;

                align-items: flex-start;

                gap: 15px;

            }


            .menu {

                width: 100%;

                flex-wrap: wrap;

                gap: 15px;

            }


            .page-header h1 {

                font-size: 30px;
            }

        }

    </style>

</head>


<body>


<!-- NAVBAR -->

<nav class="top-navbar">


    <div class="brand-section">

        <div class="brand">
            Inventory System
        </div>

        <div class="welcome">

            Welcome,
            <?php
            echo htmlspecialchars(
                $_SESSION['username']
            );
            ?>

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

        <a href="logout.php"
           class="logout-btn">
            Logout
        </a>

    </div>


</nav>


<!-- PAGE HEADER -->

<div class="page-header">

    <h1>Customer Orders</h1>

    

</div>


<!-- ORDERS TABLE -->

<div class="table-container">


<?php

if (mysqli_num_rows($result) > 0)
{

?>


<table>


    <thead>

        <tr>

            <th>Order ID</th>

            <th>Customer</th>

            <th>Email</th>

            <th>Product</th>

            <th>Category</th>

            <th>Quantity</th>

            <th>Total Price</th>

            <th>Order Date</th>

            <th>Status</th>

            <th>Update</th>

        </tr>

    </thead>


    <tbody>


<?php

while ($order = mysqli_fetch_assoc($result))
{

    $status_class =
        strtolower(
            $order['status']
        );

?>


        <tr>


            <td>

                <span class="order-id">

                    #<?php
                    echo $order['order_id'];
                    ?>

                </span>

            </td>


            <td>

                <?php
                echo htmlspecialchars(
                    $order['username']
                );
                ?>

            </td>


            <td>

                <?php
                echo htmlspecialchars(
                    $order['email']
                );
                ?>

            </td>


            <td>

                <span class="product-name">

                    <?php
                    echo htmlspecialchars(
                        $order['product_name']
                    );
                    ?>

                </span>

            </td>


            <td>

                <?php
                echo htmlspecialchars(
                    $order['category']
                );
                ?>

            </td>


            <td>

                <?php
                echo $order['quantity'];
                ?>

            </td>


            <td>

                <span class="price">

                    ₹<?php
                    echo number_format(
                        $order['total_price'],
                        2
                    );
                    ?>

                </span>

            </td>


            <td>

                <?php
                echo date(
                    "d M Y, h:i A",
                    strtotime(
                        $order['order_date']
                    )
                );
                ?>

            </td>


            <td>

                <span class="status <?php
                    echo $status_class;
                ?>">

                    <?php
                    echo htmlspecialchars(
                        $order['status']
                    );
                    ?>

                </span>

            </td>


            <td>

                <form
                    method="POST"
                    class="status-form"
                >


                    <input
                        type="hidden"
                        name="order_id"
                        value="<?php
                            echo $order['order_id'];
                        ?>"
                    >


                    <select
                        name="status"
                        class="status-select"
                    >

                        <option value="Pending"
                            <?php
                            if ($order['status']
                                == 'Pending')
                            {
                                echo 'selected';
                            }
                            ?>>
                            Pending
                        </option>


                        <option value="Confirmed"
                            <?php
                            if ($order['status']
                                == 'Confirmed')
                            {
                                echo 'selected';
                            }
                            ?>>
                            Confirmed
                        </option>


                        <option value="Shipped"
                            <?php
                            if ($order['status']
                                == 'Shipped')
                            {
                                echo 'selected';
                            }
                            ?>>
                            Shipped
                        </option>


                        <option value="Delivered"
                            <?php
                            if ($order['status']
                                == 'Delivered')
                            {
                                echo 'selected';
                            }
                            ?>>
                            Delivered
                        </option>


                        <option value="Cancelled"
                            <?php
                            if ($order['status']
                                == 'Cancelled')
                            {
                                echo 'selected';
                            }
                            ?>>
                            Cancelled
                        </option>

                    </select>


                    <button
                        type="submit"
                        name="update_status"
                        class="update-btn">

                        Update

                    </button>


                </form>

            </td>


        </tr>


<?php

}

?>


    </tbody>

</table>


<?php

}
else
{

?>


    <div class="no-orders">

        <h2>No Orders Yet</h2>

        <p>
            Customer orders will appear here
            after they place an order.
        </p>

    </div>


<?php

}

?>


</div>


</body>

</html>