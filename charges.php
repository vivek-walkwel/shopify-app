<?php
ob_start();
session_start();
error_reporting(E_ALL);
require_once("config.php");
require 'shopify.php';
global $conn;

?>

<script src="https://cdn.shopify.com/s/assets/external/app.js"></script>
 <script type="text/javascript">
        ShopifyApp.init({
            apiKey: '<?php echo SHOPIFY_API_KEY;  ?>',
            shopOrigin: 'https://<?php echo $_SESSION["shop"]; ?>',
            debug: true,
            forceRedirect: true
        });        
        ShopifyApp.ready(function(){
           /* ShopifyApp.Bar.initialize({
                icon: '',
                title: 'PixiFlex Dashboard',
                buttons: {
                   primary: {
                        label: 'Save',
                        message: 'save',
                        callback: function(){
               
                      }
                  }
              }
          });*/
            ShopifyApp.Bar.loadingOff();
        });
         
   </script>

<?php

$sc = new ShopifyClient($_SESSION['shop'], $_SESSION['token'], SHOPIFY_API_KEY, SHOPIFY_SECRET);


//If they have accepted there should be a charge_id

if (isset($_GET['charge_id'])){
      $charge_id = $_GET['charge_id'];
      $theCharge = $sc->call('GET', '/admin/recurring_application_charges/'.$_GET['charge_id'].'.json', array());
      if ($theCharge['status'] == 'accepted'){
          $activate = $sc->call('POST', '/admin/recurring_application_charges/'.$_GET['charge_id'].'/activate.json', array());
          if(isset($activate['id'])){
            $query = mysqli_query($conn, "UPDATE app_payments SET status = '".$activate['status']."', trial_ends_on = '".$activate['trial_ends_on']."' WHERE pay_id = ".$activate['id']."") or die(mysqli_error($query));
            header("location: dashboard.php");
          }
      }
      else{
        $query = mysqli_query($conn, "UPDATE app_payments SET status = '".$theCharge['status']."', trial_ends_on = '".$theCharge['trial_ends_on']."' WHERE pay_id = ".$theCharge['id']."") or die(mysqli_error($query));
        header("location: dashboard.php");
      }
}
else{
    $pquery = mysqli_query($conn, "SELECT * FROM app_payments WHERE store_name = '".$_SESSION['shop']."' ORDER BY id DESC LIMIT 1") or die(mysqli_error($pquery));
  if(mysqli_num_rows($pquery) >= 1) {
    while($prow = mysqli_fetch_array($pquery)) {
      $status = $prow['status'];
      $pay_id = $prow['pay_id'];
      $trial_ends_on = date('Y-m-d', strtotime($prow['trial_ends_on']));
      $billed_date = date('Y-m-d', strtotime($prow['created_at']));
      $next_billing_date = date('Y-m-d', strtotime($billed_date.' +30 days'));
      $current_date = date('Y-m-d');
      if(($status == 'accepted' || $status == 'active')) {
        header('location: dashboard.php');
      }
      else if($status == 'pending' || $status == 'expired') {
        $theCharge = $sc->call('GET', '/admin/recurring_application_charges/'.$pay_id.'.json', array());
        echo "<h3>You can't view this page. Your payment is still pending. <a href='".$theCharge['confirmation_url']."' target='_blank'>Click here</a> to make payment!!</h3>";
        die;
      }
      else if($status == 'declined') {
        makepayment($conn);
      }
    }
  }
  else {
    makepayment($conn);
  }
      
}

function makepayment($conn) {

  $sc = new ShopifyClient($_SESSION['shop'], $_SESSION['token'], SHOPIFY_API_KEY, SHOPIFY_SECRET);
  $fields = array(
        'recurring_application_charge' => array(
          'name' => 'Application Charge for PixiFlex',
          'price' => '6.00',
          'return_url' => SITE_URL.'/charges.php',
          'trial_days' => 0,
          'test' => true,
      ));

    
      $recurring_pay = $sc->call('POST', '/admin/recurring_application_charges.json', $fields);

      if(!empty($recurring_pay['id'])) {
        $query = mysqli_query($conn, "INSERT INTO app_payments(store_id, store_name, pay_id, plan_name, price, trial_days, status, created_at, updated_at) VALUES('".$_SESSION['store_id']."', '".$_SESSION['shop']."', '".$recurring_pay['id']."', '".$recurring_pay['name']."', '".$recurring_pay['price']."', '".$recurring_pay['trial_days']."', '".$recurring_pay['status']."', '".$recurring_pay['created_at']."', '".$recurring_pay['updated_at']."')") or die(mysqli_error($query));
          header("Location: " . $recurring_pay['confirmation_url']);
      }
}