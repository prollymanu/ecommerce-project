<?php

include('server/connection.php');

if(isset($_GET['product_id'])){

  $product_id = $_GET['product_id'];
  $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
  $stmt->bind_param("i",$product_id);

  $stmt->execute();
  $product = $stmt->get_result();//[]

//no id was given
}else{
  header('location: index.php');
}


?>

<?php include('layouts/header.php'); ?>
     

     <!--Single product--> 
<section class="container single-product my-5 pt-5">
    <div class="row mt-5">
      <!-- Left Column: Product Images -->
      <?php while($row= $product->fetch_assoc()){ ?>

      
      <div class="col-lg-6 col-md-6 col-sm-12">
        <img class="img-fluid w-100 pb-1" src="assets/imgs/<?php echo $row['product_image'];?>" id="mainImg"/>
        <div class="small-img-group d-flex">
          <div class="small-img-col">
            <img src="assets/imgs/<?php echo $row['product_image'];?>" width="100%" class="small-img"/>
          </div>
          <div class="small-img-col">
            <img src="assets/imgs/<?php echo $row['product_image2'];?>" width="100%" class="small-img"/>
          </div>
          <div class="small-img-col">
            <img src="assets/imgs/<?php echo $row['product_image3'];?>" width="100%" class="small-img"/>
          </div>
          <div class="small-img-col">
            <img src="assets/imgs/<?php echo $row['product_image4'];?>" width="100%" class="small-img"/>
          </div>
        </div>
      </div>
      
  
      <!-- Right Column: Product Details -->
      <div class="col-lg-6 col-md-12 col-sm-12">
        <h6>SHAKI</h6>
        <h3 class="py-4"><?php echo $row['product_name'];?></h3>
        <h2>Kes<?php echo $row['product_price'];?></h2>
        <form method="post" action="cart.php">
        <input type="hidden" name="product_id" value="<?php echo $row['product_id'];?>"/>
        <input type="hidden" name="product_image" value="<?php echo $row['product_image'];?>"/>
        <input type="hidden" name="product_name" value="<?php echo $row['product_name'];?>"/>
        <input type="hidden" name="product_price" value="<?php echo $row['product_price'];?>"/>

        <input type="number" name="product_quantity" value="1" min="1" style="width: 60px;"/>
        <button class="buy-btn" type="submit" name="add_to_cart">Add To Cart</button>
        </form>

        <h4 class="mt-5 mb-5">Product details</h4>
        <span><?php echo $row['product_description'];?>

        </span>
      </div>.
      <?php } ?>
    </div>
  </section>
  

      <!--Related product-->
    <section id="related-products" class="my-5 pd-5">
        <div class="container text-center mt-5 py-5">
            <h3>Related Products</h3>
            <hr class="mx-auto">
        </div>
        <div class="row mx-auto container-fluid">
            <!--Related one-->
            <?php include('server/get_new.php'); ?>
            <?php if ($new_products && $new_products->num_rows > 0) { ?>
            <?php while($row= $new_products->fetch_assoc()){ ?>
            <div class="product text-center col-lg-3 col-md-4 col- col-sm-12">
                <img class="img-fluid mb-3" src="assets/imgs/<?php echo $row['product_image'];?>"/>
                <h5 class="p-name"><?php echo $row['product_name'];?></h5>
                <h4 class="p-price">Kes<?php echo $row['product_price'];?></h4>
                <a href="<?php echo "single_product.php?product_id=". $row['product_id'];?>"><button class="buy-btn">Buy Now!</button></a>
            </div>
            <?php } ?>
            <?php } else { ?>
                <p>No Related products available at the moment.</p>
            <?php } ?>
            
        </div>
    </section> 



    <?php include('layouts/footer.php'); ?>