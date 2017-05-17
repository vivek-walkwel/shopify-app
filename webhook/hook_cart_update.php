<?php
session_start();
session_unset();
unset($_SESSION);
session_destroy();
?>