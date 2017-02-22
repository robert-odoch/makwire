<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" href="<?php echo base_url('styles/bootstrap.min.css'); ?>">

        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <link rel="stylesheet" href="<?php echo base_url('styles/ie10-viewport-bug-workaround.css'); ?>">

        <!-- Custom styles for this site -->
        <link href="<?php echo base_url('styles/styles.css'); ?>" rel="stylesheet">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <header>
            <nav role="navigation" class="navbar navbar-inverse navbar-fixed-top">
                <div class="wrapper">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="<?php echo base_url(); ?>">Mak Wire</a>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav navbar-right">
                            <?php
                            if (isset($_SESSION['user_id'])): ?>
                            <li>
                                <a href="<?= base_url('user/chat'); ?>">Chat
                                <?php
                                if ($num_active_friends > 0) {
                                    print "<span class='badge'>{$num_active_friends}</span>";
                                }
                                ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo base_url('user/messages'); ?>">
                                <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> Messages
                                <?php
                                if ($num_new_messages > 0) {
                                    print "<span class='badge'>{$num_new_messages}</span>";
                                }
                                ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo base_url('user/notifications'); ?>">
                                <span class="glyphicon glyphicon-bell" aria-hidden="true"></span> Notifications
                                <?php
                                if ($num_new_notifs > 0) {
                                    print " <span class='badge'>{$num_new_notifs}</span>";
                                }
                                ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('user/friend_requests'); ?>"><span class="glyphicon glyphicon-user"></span> Friends
                                <?php
                                if ($num_friend_requests > 0) {
                                    print " <span class='badge'>{$num_friend_requests}</span>";
                                }
                                ?>
                                </a>
                            </li>
                            <li><a href="<?php echo base_url('logout'); ?>"><span class="glyphicon glyphicon-log-out"></span> Log out</a></li>
                            <?php else: ?>
                            <li><a href="<?php echo base_url('login'); ?>"><span class="glyphicon glyphicon-log-in"></span> Log In</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </header><?php echo "\n"; ?>
