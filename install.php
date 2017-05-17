<?php
session_start();
error_reporting(0);
require_once("config.php");
require 'shopify.php';
global $conn;
$liver = "";


    if (isset($_GET['code'])) { 

        $shopifyClient = new ShopifyClient($_GET['shop'], "", SHOPIFY_API_KEY, SHOPIFY_SECRET);
        

        // Now, request the token and store it in your session.
        $_SESSION['token'] = $shopifyClient->getAccessToken($_GET['code']);
        if ($_SESSION['token'] != '')
            $_SESSION['shop'] = $_GET['shop'];

        $check = "SELECT * FROM `tbl_usersettings` WHERE `store_name` = '".$_GET['shop']."'";
        $rs = mysqli_query($conn, $check);

        if(mysqli_num_rows($rs) >= 1) {
          while($data = mysqli_fetch_array($rs)){
            $_SESSION['store_id'] = $data['id'];
          }
          $sql = mysqli_query($conn, "UPDATE `tbl_usersettings` SET access_token = '".$_SESSION['token']."' WHERE store_name = '".$_GET['shop']."'");
       }
        else{
            $sql = mysqli_query($conn, "INSERT INTO `tbl_usersettings` (access_token, store_name) VALUES ('".$_SESSION['token']."', '".$_SESSION['shop']."')");

            if ($sql) {
                $_SESSION['store_id'] = mysqli_insert_id($conn);
            }
        }
        include('webhooks.php');
        header("Location: charges.php");   
    }
    else if (isset($_GET['shop'])) {
       
        $shop = isset($_POST['shop']) ? $_POST['shop'] : $_GET['shop'];
        $shopifyClient = new ShopifyClient($shop, "", SHOPIFY_API_KEY, SHOPIFY_SECRET);

        // get the URL to the current page
        /*$pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") { $pageURL .= "s"; }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["SCRIPT_NAME"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"];
        }*/
        $pageURL = SITE_URL."/install.php";
        
        // redirect to authorize url
        header("Location: " . $shopifyClient->getAuthorizeUrl(SHOPIFY_SCOPE, $pageURL));
        exit;
    }
    
    

    else if(isset($_SESSION['store_id']) && isset($_SESSION['token']) && isset($_SESSION['shop'])) {
          header("location: dashboard.php");
        }
?>