<?php
session_start();
session_destroy();

//Return back to Apply.php
header("Location: Apply.php");