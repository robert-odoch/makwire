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

    public function forgot_password()
    {

    }

    public function change_password()
    {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Change you password';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $error_messages = array();

            $oldpasswd = $this->input->post('oldpasswd');
            if (strlen($oldpasswd) == 0) {
                $error_messages['oldpasswd'] = 'Please enter your old password.';
            }

            // Check if there exists a user with this password.
            if (! $this->account_model->user_exists($oldpasswd)) {
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
                $this->account_model->change_password($new_passwd);
                $this->utility_model->show_success("Your password has been successfully changed.");
                return;
            }
            else {
                $data['error_messages'] = $error_messages;
            }
        }

        $this->load->view('settings/account/change-password', $data);
        $this->load->view('common/footer');
    }

    public function set_prefered_name()
    {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $prefered_name = trim(strip_tags($this->input->post('prefered_name')));
            if (strlen($prefered_name) != 0) {
                $this->account_model->set_prefered_profile_name($prefered_name);
                $this->utility_model->show_success(
                    "Your profile name has been successfully changed to ${prefered_name}."
                );
                return;
            }
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Set prefered profile name';
        $this->load->view('common/header', $data);

        $data['name_combinations'] = $this->account_model->get_name_combinations();
        $this->load->view('settings/account/prefered-profile-name', $data);
        $this->load->view('common/footer');
    }

    public function change_name()
    {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Change your name';
        $this->load->view('common/header', $data);

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
                $this->account_model->change_name($lname, $other_names);
                redirect(base_url('account/set-prefered-name'));
            }
            else {
                $data['error_messages'] = $error_messages;
                $data['lname'] = $lname;
                $data['other_names'] = $other_names;
            }
        }

        $this->load->view('settings/account/change-name', $data);
        $this->load->view('common/footer');
    }

    public function delete()
    {
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        $data = $this->user_model->initialize_user();
        $data['title'] = 'Delete your account';
        $this->load->view('common/header', $data);

        $this->load->view('settings/account/delete', $data);
        $this->load->view('common/footer');
    }

}
?>
