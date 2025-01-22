<?php 

session_start();

// Include the database connection
include('server/connection.php');

//logout logic
if(!isset($_SESSION['logged_in'])){
  header('location: login.php');
  exit;
}

//logout
if(isset($_GET['logout'])){
  if(isset($_SESSION['logged_in'])){
    unset($_SESSION['logged_in']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_name']);
    header('location: login.php');
    exit;
  }
}
// Changing password
if (isset($_POST['change_password'])) {
  $old_password = trim($_POST['oldPassword']);
  $new_password = trim($_POST['newPassword']);
  $confirm_password = trim($_POST['confirmPassword']);
  $user_email = $_SESSION['user_email'];

  // Fetch the hashed password from the database
  $stmt = $conn->prepare("SELECT user_password FROM users WHERE user_email = ? LIMIT 1");
  $stmt->bind_param('s', $user_email);
  $stmt->execute();
  $stmt->bind_result($hashed_password);
  $stmt->fetch();
  $stmt->close();

  // Verify the old password
  if (!password_verify($old_password, $hashed_password)) {
      header('location: account.php?error=Incorrect old password');
      exit;
  }

  // Check new password parameters
  if (strlen($new_password) < 8 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[0-9]/', $new_password) || !preg_match('/[\W]/', $new_password)) {
      header('location: account.php?error=New password must be at least 8 characters long, include an uppercase letter, a number, and a special character.');
      exit;
  }

  // Check if new password and confirm password match
  if ($new_password !== $confirm_password) {
      header('location: account.php?error=New password and confirm password do not match');
      exit;
  }

  // Hash the new password
  $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

  // Update the password in the database
  $update_stmt = $conn->prepare("UPDATE users SET user_password = ? WHERE user_email = ?");
  $update_stmt->bind_param('ss', $new_hashed_password, $user_email);

  if ($update_stmt->execute()) {
      header('location: account.php?message=Password updated successfully');
      exit;
  } else {
      header('location: account.php?error=Failed to update password');
      exit;
  }
}

// get orders
if(isset($_SESSION['logged_in'])){
  $user_id = $_SESSION['user_id'];
  $stmt = $conn->prepare("SELECT * FROM orders where user_id=? ");
  $stmt->bind_param('i',$user_id);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->execute();

$orders = $stmt->get_result();
if (!$orders) {
    die("Query execution failed: " . $stmt->error);
}
}





?>


<?php include('layouts/header.php'); ?>

      <!--Account-->
      
      <section class="my-5 py-5">
        <div class="row container mx-auto">
            <?php if(isset($_GET['payment_message'])){ ?>
                <p class="mt-5 text-center" style="color: green;"><?php echo $_GET['payment_message']; ?>
            <?php } ?>
            <div class="text-center mt-3 pt-5 col-lg-6 col-md-12 col-sm-12">
                <h3 class="font-weight-bold">Account info</h3>
                <hr class="mx-auto">
                <div class="account-info">
                    <p>Name :<span><?php if(isset($_SESSION['user_name'])){ echo $_SESSION['user_name'];} ?></span></p>
                    <p>Email :<span><?php echo $_SESSION['user_email']; ?></span></p>
                    <p><a href="#orders" id="orders-btn">Your Orders</a></p>
                    <p><a href="account.php?logout=1" id="logout-btn">Logout</a></p>
                </div>
            </div>

            <div class="col-lg-6 col-md-12 col-sm-12">
                <form id="account-form" method="POST" action="account.php">
                <p style="color: red;" class="text-center"><?php if(isset($_GET['error'])){ echo $_GET['error'] ;}?></p>
                <p style="color: blue;" class="text-center"><?php if(isset($_GET['message'])){ echo $_GET['message'] ;}?></p>
                    <h3>Change Password</h3>
                    <hr class="mx-auto">
                    <div class="form-group">
                        <label>Old Password</label>
                        <input type="password" class="form-control" id="old-account-password" name="oldPassword" placeholder="Old Password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" class="form-control" id="new-account-password" name="newPassword" placeholder="New Password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" class="form-control" id="new-account-password-confirm" name="confirmPassword" placeholder="Confirm New Password" required>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Change Password" class="btn" name="change_password" id="change-pass-btn">
                    </div>
                </form>
            </div>
        </div>
      </section>


      <!--Orders-->
      <section class="orders container my-5 py-3" id="orders">
        <div class="container mt-2">
            <h2 class="font-weight-bolde text-center">Your Orders</h2>
            <hr class="mx-auto">
        </div>

        <table class="mt-5 pt-5">
            <tr>
                <th>Order Id</th>
                <th>Order Cost</th>
                <th>Order Status</th>
                <th>Order Date</th>
                <th>Order Details</th>
            </tr>
            <?php while($row = $orders->fetch_assoc()){ ?>
               <tr>
                <td>
                       <div class="product-info">
                           <!--<img src="assets/imgs/new1.jpeg"/>
                           <div>
                               <p class="mt-3"><?php echo $row['order_id'];?></p>
                           </div>
                       </div>-->
                       <span><?php echo $row['order_id']; ?></span>
                   </td>
                   <td>
                       <span><?php echo $row['order_cost']; ?></span>
                   </td>
                   <td>
                       <span><?php echo $row['order_status']; ?></span>
                   </td>
                   <td>
                      <span><?php echo $row['order_date']; ?></span>  
                   </td>
                   <td>
                     <form method="POST" action="order_details.php"> 
                        <input type="hidden" value="<?php echo $row['order_status'];?>" name="order_status"/>
                        <input type="hidden" value="<?php echo $row['order_id'];?>" name="order_id"/>
                        <input class="btn order-details-btn" name="order_details_btn" type="submit" value="details"/>
                     </form>
                   </td>
               </tr>
               <?php } ?>
        </table>

        <div class="checkout-container">
            <button class="btn checkout-btn">Checkout</button>
        </div>
      </section>



      <?php include('layouts/footer.php'); ?>