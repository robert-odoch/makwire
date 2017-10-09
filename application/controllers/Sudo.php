<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sudo extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        session_start();
        ensure_user_is_logged_in();

        $this->load->model(['user_model', 'profile_model', 'account_model', 'admin_model']);
        $roles = $this->user_model->get_user_roles($_SESSION['user_id']);

        $is_admin = FALSE;
        foreach ($roles as $r) {
            if ($r['name'] == 'admin') {
                $is_admin = TRUE;
                break;
            }
        }

        if ( ! $is_admin) {
            redirect(base_url("user/{$_SESSION['user_id']}"));
        }
    }

    public function invite_user()
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $this->input->post('email');
            $college_id = $this->input->post('college');
            $return_here = $this->input->post('return');

            if ($this->account_model->is_valid_mak_email($email)) {
                $error = 'Sorry, you can only send invitations to non-mak emails.';
            }
            elseif ($this->account_model->email_has_user($email)) {
                $error = 'This email address is already registered.';
            }

            if (empty($error)) {
                $activation_code = $this->account_model->add_email(NULL, $email);
                $subject = '[Makwire] Special invitation to join makwire.';
                $message = registration_email_message($activation_code);
                if ( ! $this->account_model->send_email('Makwire <' . admin_email() . '>', $email, $subject, $message)) {
                    $data['info_message'] = "Sorry, we couldn't send the invitation.<br><br>
                                            The admin has been notified about the issue
                                            and will fix it as soon as possible. Please try again later.";
                }
                else {
                    $this->admin_model->add_user_invite($email, $college_id);
                    $message = 'The invitation has been successfully sent.';
                    if ( ! $return_here) {
                        $_SESSION['message'] = $message;
                        redirect(base_url('success'));
                    }

                    $data['success_message'] = $message;
                }
            }
            else {
                $data['email'] = $email;
                $data['error'] = $error;
            }
        }

        $data = array_merge($data, $this->user_model->initialize_user($_SESSION['user_id']));
        $data['title'] = 'Makwire - Invite user';
        $this->load->view('common/header', $data);

        $data['colleges'] = $this->profile_model->get_colleges();
        $this->load->view('admin/invite-user', $data);
        $this->load->view('common/footer');
    }

}
?>
