<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="wrap-single">
<div role="main" class="main">
<div class="box">
    <?php if (isset($message)): ?>
    <h3>Log in to continue</h3>
    <?php else: ?>
    <h3>Log In</h3>
    <?php endif; ?>
</div>
<div class="box">
    <?php
    if (isset($login_errors) && array_key_exists('login', $login_errors)) {
        print "<div class='alert alert-danger'><p>{$login_errors['login']}</p></div>";
    }
    else if (isset($message)) {
        print "<div class='alert alert-info'><p>{$message}</p></div>";
        unset($message);
    }
    ?>
    <form action="<?php echo base_url('login'); ?>" method="post" accept-charset="utf-8" role="form">
        <fieldset>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" size="30"<?php
                if (isset($login_errors) && array_key_exists('username', $login_errors)) {
                    print " class='has-error'";
                }

                if (isset($username)) {
                    print(" value='{$username}'");
                }
                ?>>
                <?php
                if (isset($login_errors) && array_key_exists('username', $login_errors)) {
                    print "<span class='error'>{$login_errors['username']}</span>\n";
                }
                ?>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" size="30"<?php if (isset($login_errors) && array_key_exists('password', $login_errors)) { print " class='has-error'"; } ?>>
                <?php
                if (isset($login_errors) && array_key_exists('password', $login_errors)) {
                    print "<span class='error'>{$login_errors['password']}</span>\n";
                }
                ?>
                <span class="help-block">
                    <a href="forgot-password.html" title="Recover password">Forgot password?</a>
                </span>
            </div>
        </fieldset>
        <input type="submit" name="submit" value="Log In" class="btn">
    </form>
    <p style="margin: 5px 0;">Don't have an account? <a href="register-step-one.html" title="Create an account">create one</a></p>
</div><!-- box -->
