<?php ob_start();//output buffer, allows for instant page change
//IE delete the category on cat page, dont have to refresh page?>
<?php include "../db/config.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo $_SESSION['username'] . " Home Page";?></title>

    <!-- Bootstrap Core CSS This is the styling-->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/sb-admin.css" rel="stylesheet">

    <!-- Custom Fonts adds some icons-->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<style>
<?php
if ($_SESSION['user_role'] == "Exhibitor"){ ?>
    body {
    background-color: #5EE4F0 !important;
    }
    nav {
    background-color: #5ee4f0 !important;
    }
<?php } ?>

<?php
if ($_SESSION['user_role'] == "Organizer"){ ?>
    body {
    background-color: #4AD9BC !important;
    }
    nav {
    background-color: #4AD9BC !important;
    }
<?php } ?>

<?php
if ($_SESSION['user_role'] == "Attendee"){ ?>
    body {
    background-color: #4AA7D9 !important;
    }
    nav {
    background-color: #4AA7D9 !important;
    }
<?php } ?>

<?php
if ($_SESSION['user_role'] == "Judge"){ ?>
    body {
    background-color: #51F0A4 !important;
    }
    nav {
    background-color: #51F0A4 !important;
    }
<?php } ?>


</style>
<body>