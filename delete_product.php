<?php

session_start();

include 'db.php';

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

if (isset($_GET['product_id']))
{
    $product_id = $_GET['product_id'];

    $sql = "DELETE FROM products WHERE id='$product_id'";

    $result = mysqli_query($data, $sql);

    if ($result)
    {
        $_SESSION['message'] = "Product deleted successfully";

        header("Location: view_product.php");
        exit();
    }
    else
    {
        echo "Delete failed: " . mysqli_error($data);
    }
}

?>





