<?php
defined('BASEPATH') OR exit('No direct script access allowed');
isset($page) OR $page = '';  // Default value for page.
?>

<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta charset='utf-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <meta name='msvalidate.01' content='65F07EE3CDE3AA10362142D958A76A3C'>
        <title><?= $title; ?></title>

        <link rel='shortcut icon' href='<?= base_url('images/favicon.ico'); ?>' type='image/x-icon'>
        <link rel='icon' href='<?= base_url('images/favicon.ico'); ?>' type='image/x-icon'>

        <link rel='stylesheet' href='<?= base_url('styles/bootstrap.min.css'); ?>'>
        <link rel='stylesheet' href='<?= base_url('styles/font-awesome-4.7.0/css/font-awesome.min.css'); ?>'>

        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <link rel='stylesheet' href='<?= base_url('styles/ie10-viewport-bug-workaround.css'); ?>'>

        <!-- Custom styles for this site -->
        <link href='https://fonts.googleapis.com/css?family=Ubuntu:400,400i,700,700i' rel='stylesheet'>
        <link href='https://fonts.googleapis.com/css?family=Roboto:400,400i,700,700i' rel='stylesheet'>
        <link href='<?= base_url('styles/styles.css'); ?>' rel='stylesheet'>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <header class='site-header'>
            <?php require_once(__DIR__ . '/mobile-nav.php'); ?>
        </header><?= "\n"; ?>
