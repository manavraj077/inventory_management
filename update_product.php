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

if (!isset($_GET['product_id']))
{
    header("Location: view_product.php");
    exit();
}

$id = $_GET['product_id'];

$sql = "SELECT * FROM products WHERE id='$id'";

$result = mysqli_query($data, $sql);

$info = mysqli_fetch_assoc($result);


if (isset($_POST['update']))
{
    $product_name = $_POST['product_name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $supplier = $_POST['supplier'];
    $description = $_POST['description'];


    // Check if new image is selected
    if (!empty($_FILES['image']['name']))
    {
        $image_name = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];

        $dst = "./image/" . $image_name;
        $dst_db = "image/" . $image_name;

        if (move_uploaded_file($tmp_name, $dst))
        {
            $sql = "UPDATE products SET
                    product_name='$product_name',
                    category='$category',
                    price='$price',
                    quantity='$quantity',
                    supplier='$supplier',
                    description='$description',
                    image='$dst_db'
                    WHERE id='$id'";

            $result2 = mysqli_query($data, $sql);
        }
        else
        {
            echo "Image upload failed";
            exit();
        }
    }

    // If no new image is selected
    else
    {
        $sql = "UPDATE products SET
                product_name='$product_name',
                category='$category',
                price='$price',
                quantity='$quantity',
                supplier='$supplier',
                description='$description'
                WHERE id='$id'";

        $result2 = mysqli_query($data, $sql);
    }


    if ($result2)
    {
        echo "<script>
                alert('Product updated successfully');
                window.location.href='view_product.php';
              </script>";
    }
    else
    {
        echo "Update failed: " . mysqli_error($data);
    }
}

?>


<!DOCTYPE html>
<html>

<head>

    <title>Update Product</title>

    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

          <link rel="stylesheet" href="style.css">

</head>


<body>

<div class="container">

    <h2 class="text-center">Update Product</h2>

    <br>

    <form action="#" method="POST" enctype="multipart/form-data">

        <div class="form-group">

            <label>Product Name</label>

            <input type="text"
                   name="product_name"
                   class="form-control"
                   value="<?php echo $info['product_name']; ?>"
                   required>

        </div>


        <div class="form-group">

            <label>Category</label>

            <input type="text"
                   name="category"
                   class="form-control"
                   value="<?php echo $info['category']; ?>"
                   required>

        </div>


        <div class="form-group">

            <label>Price</label>

            <input type="number"
                   step="0.01"
                   name="price"
                   class="form-control"
                   value="<?php echo $info['price']; ?>"
                   required>

        </div>


        <div class="form-group">

            <label>Quantity</label>

            <input type="number"
                   name="quantity"
                   class="form-control"
                   value="<?php echo $info['quantity']; ?>"
                   required>

        </div>


        <div class="form-group">

            <label>Supplier</label>

            <input type="text"
                   name="supplier"
                   class="form-control"
                   value="<?php echo $info['supplier']; ?>">

        </div>


        <div class="form-group">

            <label>Description</label>

            <textarea name="description"
                      class="form-control"
                      rows="4"><?php echo $info['description']; ?></textarea>

        </div>


        <div class="form-group">

            <label>Current Image</label>

            <br>

            <img src="<?php echo $info['image']; ?>"
                 width="150"
                 height="150"
                 style="object-fit: cover;">

        </div>


        <div class="form-group">

            <label>New Image</label>

            <input type="file"
                   name="image"
                   class="form-control"
                   accept="image/*">

        </div>


        <br>

        <input type="submit"
               name="update"
               value="Update Product"
               class="btn btn-success">

        <a href="view_product.php"
           class="btn btn-danger">
            Cancel
        </a>

    </form>

</div>

</body>

</html>