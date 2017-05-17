<?php
 /* Define your APP`s key and secret*/
define('SHOPIFY_API_KEY','2e59654396994fb0e7978edd88aa4603');
define('SHOPIFY_SECRET','6385838e2799f24fa3308e6211c6d9a9');
define('SITE_URL', 'https://pixiflexapp.com');

/* Define requested scope (access rights) - checkout https://docs.shopify.com/api/authentication/oauth#scopes   */
define('SHOPIFY_SCOPE','read_content,write_content, read_themes, write_themes, read_products, write_products, read_customers, write_customers, read_orders, write_orders, read_script_tags, write_script_tags, read_fulfillments, write_fulfillments, read_shipping, write_shipping'); //eg: define('SHOPIFY_SCOPE','read_orders,write_orders');

//$servername = "localhost";
//$username = "cdemo_demo";
//$password = "x0f(XhXmsoGm";
//$dbname = "cdemo_shopify";

$servername = "localhost";
$username = "tarceaw_pixiflex";
$password = "Trenton2010!";
$dbname = "tarceaw_pixiflex";

global $conn;   
$conn = new mysqli($servername, $username, $password, $dbname);