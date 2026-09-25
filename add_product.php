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



if (isset($_POST['add_product']))
{
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $supplier = $_POST['supplier'];
    $description = $_POST['description'];

    // Image
    $image_name = $_FILES['image']['name'];
    $tmp_name = $_FILES['image']['tmp_name'];

    $dst = "./image/" . $image_name;
    $dst_db = "image/" . $image_name;

    if (move_uploaded_file($tmp_name, $dst))
    {
        $sql = "INSERT INTO products
                (product_name, category, price, quantity, supplier, description, image)
                VALUES
                ('$product_name', '$category', '$price', '$quantity',
                 '$supplier', '$description', '$dst_db')";

        $result = mysqli_query($data, $sql);

        if ($result)
        {
            echo "<script>
                    alert('Product added successfully');
                    window.location.href='view_product.php';
                  </script>";
        }
        else
        {
            echo "Database Error: " . mysqli_error($data);
        }
    }
    else
    {
        echo "Image upload failed";
    }
}

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>Add Product</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="style.css">

</head>

<body>

<nav class="navbar navbar-inverse">

    <div class="container-fluid">

        <div class="navbar-header">

            <a class="navbar-brand" href="dashboard.php">
                Inventory Management
            </a>

        </div>

        <ul class="nav navbar-nav">

            <li>
                <a href="dashboard.php">Dashboard</a>
            </li>

            <li class="active">
                <a href="add_product.php">Add Product</a>
            </li>

            <li>
                <a href="view_product.php">Products</a>
            </li>

        </ul>

    </div>

</nav>


<div class="container">

    <h1 class="text-center">Add Product</h1>

    <br>

    <div class="col-md-6 col-md-offset-3">

        <form action="#" method="POST" enctype="multipart/form-data">

            <div class="form-group">

                <label>Product Name</label>

                <input type="text"
                       name="product_name"
                       class="form-control"
                       required>

            </div>


            <div class="form-group">

                <label>Category</label>

                <input type="text"
                       name="category"
                       class="form-control"
                       placeholder="Example: Electronics"
                       required>

            </div>


            <div class="form-group">

                <label>Price</label>

                <input type="number"
                       name="price"
                       class="form-control"
                       step="0.01"
                       required>

            </div>


            <div class="form-group">

                <label>Quantity</label>

                <input type="number"
                       name="quantity"
                       class="form-control"
                       required>

            </div>


            <div class="form-group">

                <label>Supplier</label>

                <input type="text"
                       name="supplier"
                       class="form-control">

            </div>


            <div class="form-group">

                <label>Description</label>

                <textarea name="description"
                          class="form-control"
                          rows="4"></textarea>

            </div>


            <div class="form-group">

                <label>Product Image</label>

                <input type="file"
                       name="image"
                       class="form-control"
                       accept="image/*"
                       required>

            </div>


            <button type="submit"
                    name="add_product"
                    class="btn btn-primary">

                Add Product

            </button>

            <a href="dashboard.php"
               class="btn btn-default">

                Cancel

            </a>

        </form>

    </div>

</div>

</body>

</html>