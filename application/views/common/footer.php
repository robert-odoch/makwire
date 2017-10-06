<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>


        <?php if (!empty($_SESSION['user_id'])): ?>
                    </div><!-- .main-content -->
        <?php endif; ?>

                </div><!-- .main -->

        <?php if (!empty($_SESSION['user_id'])): ?>
            </div> <!-- .col-large -->
        <?php endif; ?>

            <span class='clearfix'></span>
        </div> <!-- #wrapper-* -->

        <footer role='contentinfo' class='footer site-footer'>
            <p id='site-copyright'>&copy; <?= date('Y'); ?> Makwire</p>
        </footer>

        <!-- Need JQuery to support Bootstrap JQuery plugins -->
        <script src='<?= base_url('scripts/jquery-1.11.3.js'); ?>'></script>

        <!-- Bootsrap core JavaScript -->
        <script src='<?= base_url('scripts/bootstrap.min.js'); ?>'></script>

        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src='<?= base_url('scripts/ie10-viewport-bug-workaround.js'); ?>'></script>

        <!-- And then our very own. -->
        <script src='<?= base_url('scripts/plugins/jquery.scrollTo-2.1.2/jquery.scrollTo.min.js'); ?>'></script>
        <script src='<?= base_url('scripts/site.js'); ?>'></script>
    </body>
</html>
