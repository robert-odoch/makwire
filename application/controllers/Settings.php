<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        $this->load->model(['user_model', 'settings_model']);

        // Check whether the user hasn't been logged out from some where else.
        $this->user_model->confirm_logged_in();
    }

    public function account()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Account settings';
        $this->load->view('common/header', $data);


        $this->load->view("settings/account/index");
        $this->load->view('common/footer');
    }

    public function emails()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Email settings';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $primary_email = $this->input->post('primary-email');
            if ($primary_email !== NULL) {
                $this->settings_model->set_primary_email($primary_email);
                $this->utility_model->show_success(
                    'You primary email address has been successfully saved.'
                );
                return;
            }

            $backup_email = $this->input->post('backup-email');
            if ($backup_email !== NULL) {
                if ($backup_email !== 'all' && $backup_email !== 'none') {
                    $this->settings_model->set_backup_email($backup_email);
                    $this->utility_model->show_success(
                        'You backup email address has been successfully saved.'
                    );
                    return;
                }
            }

            $email = $this->input->post('email');
            if ($email !== NULL) {
                if (strlen($email) == 0) {
                    $data['error_message'] = "Please enter an email address.";
                }
                elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $data['error_message'] = "Please enter a valid email address.";
                }
                elseif ($this->settings_model->is_activated_email($email)) {
                    $data['error_message'] = "This email address is already registered.";
                }
                elseif ($this->settings_model->is_registered_email($email)) {
                    $data['info_message'] = "Please use the link in the email sent to " .
                                            "<strong>{$email}</strong> to activate your " .
                                            "email address.<br>" .
                                            "If you can't find the email, then we can " .
                                            "<a href='" . base_url("register/resend_email/{$email}") .
                                            "'>resend the email.</a>";
                }
                else {
                    $user_email_id = $this->settings_model->add_email($email);
                    $activation_code = sha1($email);
                    $subject = 'Makwire: Activate your email address.';
                    $message = 'Please use <a href="' .
                                base_url("settings/activate-email/${user_email_id}/${activation_code}") .
                                '">this link</a> to activate your email address.';
                    if ( ! $this->utility_model->send_email($email, $subject, $message)) {
                        $data['info_message'] = "Sorry, we couldn't send your activation email.<br>" .
                                                "The admin has been notified about the issue " .
                                                "and will fix it as soon as possible.<br>" .
                                                "Please try again later.";
                    }
                    else {
                        $data['success_message'] = "An email has been sent to " .
                                                    "<strong>{$email}</strong>, please " .
                                                    "use the link in that email to activate " .
                                                    "your email address.";
                    }
                }
            }
        }

        $data['emails'] = $this->settings_model->get_emails();
        $this->load->view('settings/email', $data);
        $this->load->view('common/footer');

    }

    public function activate_email($user_email_id, $activation_code)
    {
        try {
            $this->settings_model->activate_email($user_email_id, $activation_code);
            $this->utility_model->show_success(
                'Your email address has been successfully activated. Enjoy!'
            );
        }
        catch (NotFoundException $e) {
            show_404();
        }
    }

    public function notifications()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Notification settings';
        $this->load->view('common/header', $data);

        $this->load->view('settings/notification');
        $this->load->view('common/footer');
    }

    public function block_user($user_id)
    {
        if ( ! $this->user_model->are_friends($user_id)) {
            $this->utility_model->show_error(
                'Permission denied!',
                "You don't have the proper permissions to block this user."
            );
        }
        else {
            $this->settings_model->block_user($user_id);
            $this->utility_model->show_success(
                'This user has been successfully blocked.'
            );
        }
    }

    public function unblock_user($user_id)
    {
        if ( ! $this->user_model->are_friends($user_id)) {
            $this->utility_model->show_error(
                'Permission denied!',
                "Do you even know the user you are trying to unblock?"
            );
        }
        else {
            $this->settings_model->unblock_user($user_id);
            $this->utility_model->show_success(
                'This user has been successfully unblocked. Thanks for your kindness!'
            );
        }
    }

    public function blocked_users()
    {
        $data = $this->user_model->initialize_user();
        $data['title'] = 'Blocked users';
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $query = $this->input->post('query');
            if (strlen($query) == 0) {
                $data['error_message'] = "Please enter a name or email address.";
            }
            else {
                $data['search_results'] = $this->settings_model->get_searched_user($query);
            }
        }
        else {
            $data['blocked_users'] = $this->settings_model->get_blocked_users();
        }

        $this->load->view('settings/block-user', $data);
        $this->load->view('common/footer');
    }

}
?>
