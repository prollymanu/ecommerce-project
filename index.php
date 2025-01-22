<?php include('layouts/header.php'); ?>

<!--home page-->
    <section id="home">
        <div class="container">
            <h5>Epitome of Sophisticated Elegance</h5>
            <h1>New Exciting Arrivals!</h1>
            <p>Embody the essence of luxury and class</p>
           <a href="shop.php"> <button >Shop Now!</button></a>
        </div>
    </section>
    <!--New-->
    <section id="new" class="w-100 my-1 py-1">
        <div class="row p-o m-0">
            <!--one-->
            <?php include('server/get_new.php'); ?>
            <?php if ($new_products && $new_products->num_rows > 0) { ?>
            <?php while($row= $new_products->fetch_assoc()){ ?>

            <div class="one col-lg-4 col-md-12 col-sm-12 p-o">
                <img class="img-fluid" src="assets/imgs/<?php echo $row['product_image'];?>"/>
                <div class="details">
                 <h2><?php echo $row['product_name'];?></h2>
                 <a href="<?php echo "single_product.php?product_id=". $row['product_id'];?>"><button class="buy-btn">Shop Now!</button></a>
                </div>
            </div> 

            <?php } ?>
            <?php } else { ?>
                <p>No featured products available at the moment.</p>
            <?php } ?>
        </div>
    </section> 
    <!--Featured productss-->
    <section id="featured" class="my-5 pd-5">
        <div class="container text-center mt-5 py-5">
            <h3>Our featured</h3>
            <hr class="mx-auto">
            <p>Here you can check out our featured products</p>
        </div>
        <div class="row mx-auto container-fluid">

            <?php include('server/get_featured_products.php'); ?>
            <?php if ($featured_products && $featured_products->num_rows > 0) { ?>
            <?php while($row= $featured_products->fetch_assoc()){ ?>

            <div class="product text-center col-lg-3 col-md-4 col- col-sm-12">
                <img class="img-fluid mb-3" src="assets/imgs/<?php echo $row['product_image'];?>"/>
                <h5 class="p-name"><?php echo $row['product_name'];?></h5>
                <h4 class="p-price"><?php echo $row['product_price'];?></h4>
                <a href="<?php echo "single_product.php?product_id=". $row['product_id'];?>"><button class="buy-btn">Buy Now!</button></a>
            </div>

            <?php } ?>
            <?php } else { ?>
                <p>No featured products available at the moment.</p>
            <?php } ?>
        </div>
    </section>  
 
 <?php include('layouts/footer.php'); ?>