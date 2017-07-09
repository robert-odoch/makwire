<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

                    </div><!-- .main-content -->
                </div><!-- main -->

                <div class="suggestions">
                <?php require_once("suggested-users.php"); ?>
                </div>
            </div>

            <div class="col-small">
            <?php require_once("active-users.php"); ?>
            </div>
            <span class="clearfix"></span>
        </div> <!-- #wrapper -->

        <footer role="contentinfo" class="footer">
                <p class="copyright">Copyright &copy; <?php echo date('Y'); ?>, Makwire</p>
                <a href="">Terms of Use</a><span> &middot; </span>
                <a href="">Privacy Policy</a><span> &middot; </span>
                <a href="">Advertising</a><span> &middot; </span>
                <a href="">About Us</a><span> &middot; </span>
                <a href="">Contact Us</a><span> &middot; </span>
                <a href="#" class="pull-right">Back to top</a>
        </footer>

        <!-- Need JQuery to support Bootstrap JQuery plugins -->
        <script src="<?php echo base_url('scripts/jquery-1.11.3.js'); ?>"></script>

        <!-- Bootsrap core JavaScript -->
        <script src="<?php echo base_url('scripts/bootstrap.min.js'); ?>"></script>

        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="<?php echo base_url('scripts/ie10-viewport-bug-workaround.js'); ?>"></script>
    </body>
</html>
