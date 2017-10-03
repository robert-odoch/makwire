<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        $this->load->model(['user_model', 'account_model']);
    }

    public function forgot_password($token = NULL)
    {
        if (isset($_SESSION['user_id'])) {
            redirect(base_url('news-feed'));
        }

        if ( ! empty($token)) {
            $user_data = $this->account_model->get_password_reset_user_data($token);
            if (empty($user_data)) {
                redirect(base_url('error'));
            }

            // Update the database.
            $new_passwd = substr(md5(uniqid(rand(), true)), 3, 10);
            $this->account_model->save_password_reset_password($user_data['email'], $new_passwd);

            // Send instructions for re-setting password.
            $subject = 'Makwire: your new password';
            $email_data['email_heading'] = 'Makwire password reset';
            $email_data['message'] = "<p>Your password for logging into <b>makwire</b> has
                                        been temporarily changed to {$new_passwd}. Please login
                                        using this password. Then you may change it to something
                                        more familiar.
                                        </p>";
            $email_body = $this->load->view('email', $email_data, true);
            $email_sent = $this->account_model->send_email('robertelvisodoch@gmail.com', $user_data['email'], $subject, $email_body);
            if ($email_sent) {
                $data['success_message'] = "Your password has been changed!<br><br>
                                            The new, temporary password has been sent to <b>{$user_data['email']}</b>.
                                            Once you have logged in with this password, you may change it by going to
                                            <b>settings > change password</b>.";
            }
            else {
                $data['info_message'] = 'Your password could not be changed due to a system error.
                                            We apologize for the inconvenience.';
            }
        }
        else {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $email_address = $this->input->post('email');
                if (strlen($email_address) == 0) {
                    $error_message = 'Please enter an email address.';
                }
                elseif ( ! filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
                    $error_message = 'Please enter a valid email address.';
                }
                elseif ( ! $this->account_model->is_activated_email($email_address)) {
                    $error_message = 'Sorry, makwire does not recognise that email address.';
                }

                if (isset($error_message)) {
                    $data['error_message'] = $error_message;
                    $data['email'] = $email_address;
                }
                else {
                    // Verify that this email address belongs to the user.
                    $token = $this->account_model->gen_email_verification_code();
                    $this->account_model->save_password_reset_token($email_address, $token);
                    $subject = 'Makwire: recover forgotten password';
                    $email_data['email_heading'] = 'Makwire Password Reset';
                    $email_data['message'] = "<p>Please use the link below to reset your password.</p>
                                                <a href='" . base_url("account/forgot-password/{$token}") . "'
                                                style='color: #fff; margin: 5px 0; padding: 10px; display: block; text-align: center; border-radius: 2px;
                                                border-color: #46b8da; text-decoration: none; box-sizing: border-box; font-variant: small-caps;
                                                background-color: #5bc0de;'>Reset Password</a>";
                    $email_body = $this->load->view('email', $email_data, true);

                    $email_sent = $this->account_model->send_email('robertelvisodoch@gmail.com', $email_address, $subject, $email_body);
                    if ($email_sent) {
                        $data['success_message'] = "We just emailed you!<br><br>
                                                    An email has been sent to <b>{$email_address}</b>.
                                                    Please use the link in that email to reset your password.";
                    }
                    else {
                        $data['info_message'] = 'Your password could not be changed due to a system error.
                                                    We apologize for the inconvenience.';
                    }
                }
            }
        }

        $data['title'] = 'Recover your password';
        $this->load->view('common/header', $data);

        $this->load->view('settings/account/forgot-password', $data);
        $this->load->view('common/footer');
    }

    public function change_password()
    {
        ensure_user_is_logged_in();

        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $error_messages = array();

            $oldpasswd = $this->input->post('oldpasswd');
            if (strlen($oldpasswd) == 0) {
                $error_messages['oldpasswd'] = 'Please enter your old password.';
            }
            // Check if there exists a user with this password.
            elseif ( ! $this->account_model->user_exists($_SESSION['user_id'], $oldpasswd)) {
                $error_messages['oldpasswd'] = 'Incorrect password, please try again.';
            }
            else {
                $passwd1 = $this->input->post('passwd1');
                if (strlen($passwd1) == 0) {
                    $error_messages['passwd1'] = 'Please enter your new password.';
                }
                elseif (strlen($passwd1) < 6) {
                    $error_messages['passwd1'] = 'Password must be atleast 6 characters long!';
                }
                elseif ( ! preg_match('/^(\w*(?=\w*\d)(?=\w*[a-z])(?=\w*[A-Z])\w*){6,}$/', $passwd1)) {
                        $error_messages['passwd1'] = 'Please ensure that your password meets ' .
                                                        'the above requirements.';
                }
                else {
                    $passwd2 = $this->input->post('passwd2');
                    if (strlen($passwd2) == 0) {
                        $error_messages['passwd2'] = 'Please confirm your new password.';
                    }
                    elseif ($passwd1 != $passwd2) {
                        $error_messages['passwd2'] = 'The two passwords do not match!';
                    }
                    else {
                        $new_passwd = $passwd1;
                    }
                }
            }

            if (empty($error_messages)) {
                $this->account_model->change_password($_SESSION['user_id'], $new_passwd);
                $_SESSION['message'] = 'Your password has been successfully changed.';
                redirect(base_url('success'));
            }
            else {
                $data['error_messages'] = $error_messages;
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Change your password';
        $this->load->view('common/header', $data);

        $this->load->view('settings/account/change-password', $data);
        $this->load->view('common/footer');
    }

    public function set_prefered_name()
    {
        ensure_user_is_logged_in();

        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $prefered_name = trim(strip_tags($this->input->post('prefered_name')));
            if (strlen($prefered_name) != 0) {
                $this->account_model->set_prefered_profile_name($_SESSION['user_id'], $prefered_name);
                $_SESSION['message'] = "Your profile name has been successfully changed to ${prefered_name}.";
                redirect(base_url('success'));
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Set prefered profile name';
        $this->load->view('common/header', $data);

        $data['name_combinations'] = $this->account_model->get_name_combinations($_SESSION['user_id']);
        $this->load->view('settings/account/prefered-profile-name', $data);
        $this->load->view('common/footer');
    }

    public function change_name()
    {
        ensure_user_is_logged_in();

        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $error_messages = array();

            $lname = trim(strip_tags($this->input->post('lname')));
            if (strlen($lname) == 0) {
                $error_messages['lname'] = "Please enter your last name.";
            }
            elseif ( ! preg_match('/^[A-Za-z]+( ?[A-Za-z]+)*$/', $lname)) {
                $error_messages['lname'] = "Name must contain only letters of the alphabet.";
            }

            $other_names = trim(strip_tags($this->input->post('other_names')));
            if (strlen($other_names) == 0) {
                $error_messages['other_names'] = "Please enter your other names.";
            }
            elseif ( ! preg_match('/^[A-Za-z]+( ?[A-Za-z]+)*$/', $other_names)) {
                $error_messages['other_names'] = "Name must contain only letters of the alphabet.";
            }

            if (empty($error_messages)) {
                $this->account_model->change_name($_SESSION['user_id'], $lname, $other_names);
                redirect(base_url('account/set-prefered-name'));
            }
            else {
                $data['error_messages'] = $error_messages;
                $data['lname'] = $lname;
                $data['other_names'] = $other_names;
            }
        }
        else {
            $name = $this->account_model->get_name($_SESSION['user_id']);
            $data['lname'] = $name['lname'];
            $data['other_names'] = $name['other_names'];
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Change your name';
        $this->load->view('common/header', $data);

        $this->load->view('settings/account/change-name', $data);
        $this->load->view('common/footer');
    }

    public function delete()
    {
        ensure_user_is_logged_in();

        $data = [];

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Delete your account';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->account_model->delete_account($_SESSION['user_id']);
            unset($_SESSION['user_id']);

            $_SESSION['heading'] = 'Account deleted';
            $_SESSION['message'] = "Your account has been successfully deleted.
                                    It's so sad that you had to leave too soon, we'll surely miss you.";
            redirect(base_url('success'));
        }

        // Require the user to first login before continuing.
        if ($_SERVER['HTTP_REFERER'] != base_url('login')) {
            $this->load->model('logout_model');
            $this->logout_model->logout($_SESSION['user_id']);

            session_start();
            $_SESSION['message'] = 'Please login to continue!<br><br>
                                    You must login again to verify that you are the rightful owner
                                    of this account, sorry for the inconvenience.';
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        $this->load->view('settings/account/delete', $data);
        $this->load->view('common/footer');
    }

    public function resend_email()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $allowed_referers = [
                base_url('settings/emails'),
                base_url('register/step-one')
            ];

            if (in_array($_SERVER['HTTP_REFERER'], $allowed_referers) ||
                    strpos($_SERVER['HTTP_REFERER'], base_url()) !== FALSE) {
                // continue...
            }
            else {
                redirect(base_url('error'));
            }
        }

        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $this->input->post('email');
            if (strlen($email) == 0) {
                $error_message = 'Please enter your email address.';
            }
            else {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    if ( ! $this->account_model->is_registered_email($email)) {
                        $error_message = "Sorry, makwire does not recognise that email address.";
                    }
                    elseif ($this->account_model->is_activated_email($email)) {
                        $error_message = "This email address was activated LONG T.";
                    }
                    else {
                        $activation_code = $this->account_model->gen_email_verification_code();
                        $subject = 'Makwire: Please verify your email address.';
                        if ($this->account_model->email_has_user($email)) {
                            $email_body = email_verification_message($activation_code);
                        }
                        else {
                            $email_body = registration_email_message($activation_code);
                        }

                        $this->account_model->send_email('robertelvisodoch@gmail.com', $email, $subject, $email_body);
                        $data['success_message'] = "We just emailed you!<br><br>
                                                    An email has been sent to {$email}. Please use the link in that email
                                                    to activate your email address.";
                    }
                }
                else {
                    $error_message = 'Please enter a valid email address.';
                }
            }

            if (isset($error_message)) {
                $data['email'] = $email;
                $data['error_message'] = $error_message;
            }
        }

        if ( ! empty($_SESSION['user_id'])) {
            $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        }
        $data['title'] = 'Resend email activation link';
        $this->load->view('common/header', $data);

        $data['form_action'] = base_url("account/resend-email");
        $this->load->view('settings/account/resend-email', $data);
        $this->load->view('common/footer');
    }

    public function activate_email($activation_code)
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $this->input->post('email');
            if (strlen($email) == 0) {
                $error_message = 'Please enter your email address.';
            }
            else {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    try {
                        $this->account_model->activate_email($email, $activation_code);
                        if ($this->account_model->email_has_user($email)) {
                            $_SESSION['message'] = 'Your email address has been successfully activated. Enjoy!';
                            redirect(base_url('success'));
                        }
                        else {
                            $_SESSION['email'] = $email;
                            $_SESSION['activation_code'] = $activation_code;
                            redirect(base_url("register/step-two/{$activation_code}"));
                        }
                    }
                    catch (NotFoundException $e) {
                        if ($this->account_model->is_activated_email($email) &&
                                ! $this->account_model->email_has_user($email)) {

                            // A user didn't complete the registration process the previous time.
                            $data['info_message'] = "Dear user, it seems the last time you didn't complete the
                                                        registration process. We need to resend you the registration
                                                        email again so that you can continue with the registration process,
                                                        please click on the button below to continue.<br><br>

                                                        <a href='" . base_url('account/resend-email') . "' class='btn btn-sm'>
                                                            Resend email
                                                        </a>";
                        }
                        else {
                            $error_message = "Sorry, makwire does not recognise that email address.";
                        }
                    }
                }
                else {
                    $error_message = 'Please enter a valid email address.';
                }
            }

            if (isset($error_message)) {
                $data['email'] = $email;
                $data['error_message'] = $error_message;
            }
        }

        if ( ! empty($_SESSION['user_id'])) {
            $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        }
        $data['title'] = 'Activate your email address';
        $this->load->view('common/header', $data);

        $data['form_action'] = base_url("account/activate-email/{$activation_code}");
        $this->load->view('settings/account/activate-email', $data);
        $this->load->view('common/footer');
    }

}
?>
