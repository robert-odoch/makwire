<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>


        <?php if (!empty($_SESSION['user_id'])): ?>
                    </div><!-- .main-content -->
        <?php endif; ?>

                </div><!-- main -->

        <?php if (!empty($_SESSION['user_id'])): ?>
                <div class='suggestions'>
                <?php require_once('suggested-users.php'); ?>
                </div>
            </div> <!-- .col-large -->

            <div class='col-small'>
                <?php require_once('active-users.php'); ?>
            </div>
        <?php endif; ?>

            <span class='clearfix'></span>
        </div> <!-- #wrapper-* -->

        <footer role='contentinfo' class='footer'>
            <div class='wrapper'>
                <p id='site-copyright'>Copyright &copy; <?php echo date('Y'); ?> makwire</p>
                <a href='#' class='pull-right'>Back to top</a>
            </div>
        </footer>

        <!-- Need JQuery to support Bootstrap JQuery plugins -->
        <script src='<?php echo base_url('scripts/jquery-1.11.3.js'); ?>'></script>

        <!-- Bootsrap core JavaScript -->
        <script src='<?php echo base_url('scripts/bootstrap.min.js'); ?>'></script>

        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src='<?php echo base_url('scripts/ie10-viewport-bug-workaround.js'); ?>'></script>

        <!-- And then our very own. -->
        <script src='<?php echo base_url('scripts/site.js'); ?>'></script>
    </body>
</html>
