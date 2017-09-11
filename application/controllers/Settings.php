<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if (empty($_SESSION['user_id'])) {
            $_SESSION['return_uri'] = $_SERVER['REQUEST_URI'];
            redirect(base_url('login'));
        }

        $this->load->model(['user_model', 'account_model']);
    }

    public function account()
    {
        $data = $this->user_model->initialize_user($_SESSION['user_id']);
        $data['title'] = 'Account settings';
        $this->load->view('common/header', $data);
        $this->load->view('settings/account/index');
        $this->load->view('common/footer');
    }

    public function emails()
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $primary_email = $this->input->post('primary-email');
            if ($primary_email !== NULL) {
                $this->account_model->set_primary_email($_SESSION['user_id'], $primary_email);
                $data['primary_email_success_message'] = 'You primary email address has been successfully saved.';
            }

            $backup_email = $this->input->post('backup-email');
            if ($backup_email !== NULL) {
                $this->account_model->set_backup_email($_SESSION['user_id'], $backup_email);
                $data['backup_email_success_message'] = 'You backup email address has been successfully saved.';
            }

            $email = $this->input->post('email');
            if ($email !== NULL) {
                if (strlen($email) == 0) {
                    $data['error_message'] = "Please enter an email address.";
                }
                elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $data['error_message'] = "Please enter a valid email address.";
                }
                elseif ($this->account_model->is_activated_email($email)) {
                    $data['error_message'] = "This email address is already registered.";
                }
                elseif ($this->account_model->is_registered_email($email)) {
                    $data['info_message'] = "Please use the link in the email sent to
                                            <strong>{$email}</strong> to activate your
                                            email address. If you can't find the email, then we can
                                            <a href='" . base_url("account/resend-email") .
                                            "'>resend the email.</a>";
                }
                else {
                    $activation_code = $this->account_model->gen_email_activation_code();
                    $user_email_id = $this->account_model->add_email($_SESSION['user_id'], $email, $activation_code);
                    $subject = 'Makwire: Activate your email address.';
                    $message = 'Please use <a href="' .
                                base_url("account/activate-email/${activation_code}") .
                                '">this link</a> to activate your email address.';
                    if ( ! $this->account_model->send_email('robertelvisodoch@gmail.com', $email, $subject, $message)) {
                        $data['info_message'] = "Sorry, we couldn't send your activation email.<br>
                                                The admin has been notified about the issue
                                                and will fix it as soon as possible.<br>
                                                Please try again later.";
                    }
                    else {
                        $data['success_message'] = "An email has been sent to <strong>{$email}</strong>, please
                                                    use the link in that email to activate
                                                    your email address.";
                    }
                }
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Email settings';
        $this->load->view('common/header', $data);

        $data['emails'] = $this->account_model->get_emails($_SESSION['user_id']);
        $this->load->view('settings/email', $data);
        $this->load->view('common/footer');
    }

}
?>
