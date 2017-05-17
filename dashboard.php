<?php
session_start();
error_reporting(0);

require_once("config.php");
require 'shopify.php';
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
	if(!isset($_SESSION['shop']) || empty($_SESSION['shop']) || !isset($_SESSION['token']) || empty($_SESSION['token']) ){
		header("Location:install.php?shopname=".urlencode($_GET['shop'])."");
		die;
	}
	

	$query = mysqli_query($conn, "SELECT * FROM tbl_usersettings WHERE store_name = '".$_SESSION['shop']."'");
	if(mysqli_num_rows($query) == 1) {
		while($row = mysqli_fetch_array($query)){
			$script_id = $row['settings'];
			if($script_id == "" || $script_id == "E")  {
				$pageURL = 'https';
		        if ($_SERVER["HTTPS"] == "on") { $pageURL .= "s"; }
		        $pageURL .= "://";
		        if ($_SERVER["SERVER_PORT"] != "80") {
		            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
		        } else {
		            $pageURL .= $_SERVER["SERVER_NAME"];
		        }

		        $sc = new ShopifyClient($_SESSION['shop'], $_SESSION['token'], SHOPIFY_API_KEY, SHOPIFY_SECRET);

		        $script = $sc->call('GET', '/admin/script_tags.json', array());


				foreach($script as $val) {
					$delete = $sc->call('DELETE', '/admin/script_tags/'.$val['id'].'.json');
				}
				

				$inject_script = $sc->call('POST', '/admin/script_tags.json', array("script_tag" => array("event"=> "onload", "src" => SITE_URL."/scripts/pixel-script.php?storeid=".$_SESSION['store_id'])));


				//print_r($inject_script);
				if(isset($inject_script['script_tag']['id'])) {
					$script_new_id = $inject_script['script_tag']['id'];
				}
				else {
					$script_new_id = $inject_script['id'];
				}
				$uquery = mysqli_query($conn, "UPDATE tbl_usersettings SET settings = '".$script_new_id."' WHERE store_name = '".$_SESSION['shop']."'");

				//include('webhooks.php');
			}
		}
	}
	else {
		header("Location:install.php?shopname=".urlencode($_GET['shop'])."");
	}

	$pquery = mysqli_query($conn, "SELECT * FROM app_payments WHERE store_name = '".$_SESSION['shop']."' order by id DESC LIMIT 1") or die(mysqli_error($pquery));
	if(mysqli_num_rows($pquery) >= 1) {
		while($prow = mysqli_fetch_array($pquery)) {
			$status = $prow['status'];
			if($status == 'pending') {
				header('location: charges.php');
				die;
			}
			else if($status == 'declined') {
				echo "<h3>You can't view this page. First complete the payment first. <a href='charges.php' target='_blank'>Click here</a> to make payment again</h3>";
				die;
			}
		}
	}
	else {
		header('location: charges.php');
		die;
	}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PIXIFLEX SHOPIFY APP</title>
  <!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<link rel="stylesheet" href="css/style.css">
<!-- Optional theme 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css"> -->

<!-- Latest compiled and minified Jquery Library -->
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" ></script>

</head>

<body>
<div class="container">
<div class="row">
    <div class="border-box">
			<form method="post" class="fb-pixel-form" action="">
				<div class="col-md-12">
				     <h2>Enter your Facebook Pixel ID</h2>
					<input type="text" name="pixelid" class="form-control input-sm" maxlength="64" placeholder="Facebook Pixel ID">
					<button type="submit" name="submit" class="btn btn-primary btn-sm">ADD</button>
				</div>
			</form>
</div>
<?php
			if(isset($_POST['submit'])) {
				$pixelid = $_POST['pixelid'];

				$query = mysqli_query($conn, "SELECT * FROM tbl_facebook_pixel_accounts WHERE facebook_pixel_id = ".$pixelid." AND store_id = '".$_SESSION['store_id']."'");
				if(mysqli_num_rows($query) == 1) {
					echo "<p>Pixel Id already existing</p>";
				}
				else {
					$now = date('Y-m-d');
			$query = mysqli_query($conn, "INSERT INTO tbl_facebook_pixel_accounts(id, store_id, facebook_pixel_id, created_at) VALUES('', '".$_SESSION['store_id']."', '".$pixelid."', '".$now."')");

				}
			}

			if(isset($_POST['delete'])) {
	    		$pix_id = $_POST['pix_id'];
	    		$query = mysqli_query($conn, "DELETE FROM tbl_facebook_pixel_accounts WHERE id = '".$pix_id."'");
	    	}
		?>
						<script>
				ShopifyApp.Bar.loadingOff();
			</script>
		</div>
	<div class="row">
	<table class="table table-responsive table-bordered table-striped" style="margin-top: 20px;">
	  <thead>
	    <tr>
	    
	      <th>Pixel ID</th>
	      <th>Created At</th>
	      <th>Action</th>
	    </tr>
	  </thead>
	  <tbody>
	  	<?php
	  		$query = mysqli_query($conn, "SELECT * FROM tbl_facebook_pixel_accounts WHERE store_id = '".$_SESSION['store_id'] ."'");
	  		while($row = mysqli_fetch_array($query)) {
	  	?>
		    <tr>
		      <td><?php echo $row['facebook_pixel_id']; ?></td>
		      <td><?php echo $row['created_at']; ?></td>
		      <td><form action="" method="post">
		      		<input type="hidden" name="pix_id" value="<?php echo $row['id']; ?>" />
		      		<button type="submit" class="btn btn-primary" name="delete">Delete</button>
		      		</form></td>
		    </tr>
	    <?php
	    }
	  	?>
	  </tbody>
	</table>
</div>
</div>
</body>
</html>