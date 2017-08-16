<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        $this->load->model(['user_model', 'account_model', 'settings_model']);
    }

    public function success()
    {
        // Defaults.
        $title = 'Success! - Makwire';

        // Allow calls to override.
        if (!empty($_SESSION['title'])) {
            $title = $_SESSION['title'];
            unset($_SESSION['title']);
        }

        if (empty($_SESSION['heading']) || empty($_SESSION['message'])) {
            redirect(base_url('account/error'));
        }
        else {
            $heading = $_SESSION['heading'];
            unset($_SESSION['heading']);

            $message = $_SESSION['message'];
            unset($_SESSION['message']);
        }

        $data['title'] = $title;
        $data['heading'] = $heading;
        $data['message'] = $message;

        $this->load->view('common/header', $data);
        $this->load->view('show/success', $data);
        $this->load->view('common/external-page-footer');
    }

    public function error()
    {
        $data = $this->user_model->initialize_user();

        // Defaults.
        $title = 'Error! - Makwire';
        $heading = 'Something isn\'t right';
        $message = 'Oh dear, I don\'t know how you ended up here.';

        // Allow calls to override.
        if (!empty($_SESSION['title'])) {
            $title = $_SESSION['title'];
            unset($_SESSION['title']);
        }

        if (!empty($_SESSION['heading'])) {
            $heading = $_SESSION['heading'];
            unset($_SESSION['heading']);
        }

        if (!empty($_SESSION['message'])) {
            $message = $_SESSION['message'];
            unset($_SESSION['message']);
        }

        $data['title'] = $title;
        $data['heading'] = $heading;
        $data['message'] = $message;

        $this->load->view('common/header', $data);
        $this->load->view('show/error', $data);
        $this->load->view('common/external-page-footer');
    }

    public function forgot_password()
    {
        if (isset($_SESSION['user_id'])) {
            redirect(base_url('news-feed'));
        }

        $data['title'] = 'Recover your password';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email_address = $this->input->post('email');
            if (strlen($email_address) == 0) {
                $error_message = 'Please enter an email address.';
            }
            elseif (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
                $error_message = 'Please enter a valid email address.';
            }
            elseif ( ! $this->settings_model->is_activated_email($email_address)) {
                $error_message = 'Sorry, makwire does not recognise that email address.';
            }

            if (isset($error_message)) {
                $data['error_message'] = $error_message;
                $data['email'] = $email_address;
            }
            else {
                // Send instructions for re-setting password.
            }
        }

        $this->load->view('settings/account/forgot-password', $data);
        $this->load->view('common/external-page-footer');
    }

    public function change_password()
    {
        $this->ensure_user_is_logged_in();

        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $error_messages = array();

            $oldpasswd = $this->input->post('oldpasswd');
            if (strlen($oldpasswd) == 0) {
                $error_messages['oldpasswd'] = 'Please enter your old password.';
            }
            // Check if there exists a user with this password.
            elseif (! $this->account_model->user_exists($_SESSION['user_id'], $oldpasswd)) {
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
                elseif (!preg_match('/^(\w*(?=\w*\d)(?=\w*[a-z])(?=\w*[A-Z])\w*){6,}$/', $passwd1)) {
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
                redirect(base_url('user/success'));
            }
            else {
                $data['error_messages'] = $error_messages;
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user());
        $data['title'] = 'Change your password';
        $this->load->view('common/header', $data);

        $this->load->view('settings/account/change-password', $data);
        $this->load->view('common/footer');
    }

    public function set_prefered_name()
    {
        $this->ensure_user_is_logged_in();

        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $prefered_name = trim(strip_tags($this->input->post('prefered_name')));
            if (strlen($prefered_name) != 0) {
                $this->account_model->set_prefered_profile_name($_SESSION['user_id'], $prefered_name);
                $_SESSION['message'] = "Your profile name has been successfully changed to ${prefered_name}.";
                redirect(base_url('user/success'));
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user());
        $data['title'] = 'Set prefered profile name';
        $this->load->view('common/header', $data);

        $data['name_combinations'] = $this->account_model->get_name_combinations($_SESSION['user_id']);
        $this->load->view('settings/account/prefered-profile-name', $data);
        $this->load->view('common/footer');
    }

    public function change_name()
    {
        $this->ensure_user_is_logged_in();

        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $error_messages = array();

            $lname = trim(strip_tags($this->input->post('lname')));
            if (strlen($lname) == 0) {
                $error_messages['lname'] = "Please enter your last name.";
            }

            $other_names = trim(strip_tags($this->input->post('other_names')));
            if (strlen($other_names) == 0) {
                $error_messages['other_names'] = "Please enter your other names.";
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

        $data = array_merge($data, $this->user_model->initialize_user());
        $data['title'] = 'Change your name';
        $this->load->view('common/header', $data);

        $this->load->view('settings/account/change-name', $data);
        $this->load->view('common/footer');
    }

    public function delete()
    {
        $this->ensure_user_is_logged_in();

        $data = [];

        $data = array_merge($data, $this->user_model->initialize_user());
        $data['title'] = 'Delete your account';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->account_model->delete_account($_SESSION['user_id']);
            unset($_SESSION['user_id']);

            $_SESSION['heading'] = 'Account deleted';
            $_SESSION['message'] = "Your account has been successfully deleted.
                                    It's so sad that you had leave too soon, we'll surely miss you.";
            redirect(base_url('account/success'));
        }

        $this->load->view('settings/account/delete', $data);
        $this->load->view('common/footer');
    }

    private function ensure_user_is_logged_in()
    {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }
    }

}
?>
