<?php
if (session_id() == '')
  session_start();
$_SESSION['user'] = null;
session_destroy();
header('Location: ./main.html');
die();
