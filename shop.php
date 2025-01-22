<?php 
include('server/connection.php');


$stmt = $conn->prepare("SELECT * FROM products");
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->execute();

$products = $stmt->get_result();




?>


<<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF -8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Shop</title>
        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        
        <link rel="stylesheet" href="assets/css/syle.css">

        <script src="https://kit.fontawesome.com/6016383fb9.js" crossorigin="anonymous"></script>
        <style>
          .product img{
            width: 100%;
            height: auto;
            box-sizing: border-box;
            object-fit: cover;

          }
          .pagination a{
            color: coral;
          }
          .pagination li:hover{
            color: #fff;
            background-color: coral;
          }
        </style>

    </head>
    <body>

          <!--Nav bar-->
    <nav class="navbar navbar-expand-lg bg-white py-1.5 fixed-top">
        <div class="container">
          <a class="navbar-brand" href="#">Shaki</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse nav-buttons" id="navbarSupportedContent">
                <!-- Add ms-auto to push navbar items to the right -->
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="index.php">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="shop.php">Shop</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="About.php">About</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="contact.php">Contact us</a>
              </li>
              <li class="nav-item">
                <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i></a>
                <a href="account.php"><i class="fa-solid fa-user"></i></a>
              </li>
            </ul>
          </div>
        </div>
    </nav>
   
      <!--search........add this feature if more clothes are available
    <section id="search" class="my-5 py-5 ms-2"  >
      <div class="container mt-5 py-5">
        <p>Search Products</p>
        <hr>
      </div>
        <form>
          <div class="row mx-auto container">
            <div class="col-lg-12 col-md-12 col-md-sm-12">
              <p>Category</p>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="category" id="category_one">
                  <label class="form-check-label" for="flexRadioDefault1">
                    Dresses
                  </label>
                </div>

                <div class="form-check">
                  <input class="form-check-input" type="radio" name="category" id="category_two">
                  <label class="form-check-label" for="flexRadioDefault1">
                    Skirts
                  </label>
                </div>

                <div class="form-check">
                  <input class="form-check-input" type="radio" name="category" id="category_two">
                  <label class="form-check-label" for="flexRadioDefault1">
                    Blouses
                  </label>
                </div>

                <div class="form-check">
                  <input class="form-check-input" type="radio" name="category" id="category_two">
                  <label class="form-check-label" for="flexRadioDefault1">
                    T-Shirts
                  </label>
                </div>
            </div>
          </div>

          <div class="row mx-auto container mt-5">
            <div class="col-lg-12 col-md-12 col-sm-12">
              <p>Price</p>
              <input type="range" class="form-range w-50" min="1" max="5000" id="customRange2" >
              <div class="w-50">
                <span style="float: left;">1</span>
                <span style="float:right;">5000</span>
              </div>
            </div>
          </div>
          <div class="form-group my-3 mx-3">
            <input type="submit" name="search" value="search" class="btn btn-primary">
          </div>
        </form>
    </section> -->
      <!--Shop Products-->
      <section id="featured" class="my-5 py-5">
  <div class="container mt-5 py-5">
    <h3>Our Products</h3>
    <hr>
    <p>Check out our amazing products</p>
  </div>
  <div class="row mx-auto container">
    <!-- PHP while loop to display all products -->
    <?php while ($row = $products->fetch_assoc()) { ?>
      <div class="product col-lg-3 col-md-4 col-sm-6">
        <img class="img-fluid mb-3" src="assets/imgs/<?php echo $row['product_image']; ?>" alt="<?php echo $row['product_name']; ?>">
        <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
        <h4 class="p-price">Kes<?php echo $row['product_price']; ?></h4>
        <a class="btn buy-btn" href="single_product.php?product_id=<?php echo $row['product_id']; ?>">Buy Now</a>
      </div>
    <?php } ?>
  

         
          
          <!--Only include this when the clothes become so many-->
          <!--
          <nav aria-label="Page navigation">
            <ul class="pagination mt-5">
              <li class="page-item"><a class="page-link" href="#">Previous</a></li>
              <li class="page-item"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">3</a></li>
              <li class="page-item"><a class="page-link" href="#">Next</a></li>
            </ul>
          </nav>
        -->
      </div>
  </section>


  <?php include('layouts/footer.php'); ?>
