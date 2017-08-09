<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        session_start();
        if ( ! empty($_SESSION['user_id'])) {
            redirect(base_url("user/{$_SESSION['user_id']}"));
        }

        $this->load->model(['register_model', 'settings_model']);
    }

    public function step_one()
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($this->input->post('email'));
            if (strlen($email) == 0) {
                $data['error_message'] = 'Please enter an email address!';
            }
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['error_message'] = 'Please enter a valid email address!';
            }
            else {
                $valid_mak_domains = [
                    'cedat.mak.ac.ug', 'chuss.mak.ac.ug',
                    'cns.mak.ac.ug', 'cees.mak.ac.ug',
                    'caes.mak.ac.ug', 'chs.mak.ac.ug',
                    'law.mak.ac.ug', 'covab.mak.ac.ug',
                    'cis.mak.ac.ug', 'bams.mak.ac.ug',
                    'musph.ac.ug', 'dicts.mak.ac.ug'];

                $is_valid_mak_email = FALSE;
                foreach ($valid_mak_domains as $d) {
                    if (stripos($email, $d) !== false) {
                        $is_valid_mak_email = TRUE;
                        break;
                    }
                }

                if (!$is_valid_mak_email) {
                    $data['error_message'] = 'Please enter a Mak email address!';
                }
                elseif ($this->settings_model->is_activated_email($email)) {
                        $data['error_message'] = 'This email address is already registered.';
                }
                elseif ($this->settings_model->is_registered_email($email)) {
                    $data['info_message'] = "Please use the link in the email sent to " .
                                            "<strong>{$email}</strong> to continue with " .
                                            "the registration process.<br>" .
                                            "If you cant' find the email, then we can " .
                                            "<a href='" . base_url("register/resend-email/{$email}") .
                                            "'>resend the email.</a>";
                }
                else {
                    $activation_code = md5(uniqid(rand(), true));
                    $this->settings_model->add_email($email, $activation_code);
                    $subject = 'Makwire: Please verify your email address.';

                    $email_data['email_heading'] = 'makwire account creation';
                    $email_data['message'] = "<p>
                                                Hi there, thanks for your interest in joining
                                                <a href='http://www.makwire.com'>makwire</a>.
                                            </p>
                                            <p>
                                              Please use the link below to verify your email
                                              address and continue with the registration process.
                                            </p>
                                            <a href='" .
                                                base_url("register/activate-email/{$activation_code}") . "'
                                                style='color: #fff; margin: 5px 0; padding: 10px; display: block; text-align: center; border-radius: 2px;
                                                    border-color: #46b8da; text-decoration: none; box-sizing: border-box; font-variant: small-caps;
                                                    background-color: #5bc0de;'>Verify your email address</a>

                                            <hr>
                                            <p>
                                                You’re receiving this email because you recently tried to create
                                                a new <b>makwire</b> account. If this wasn’t you, please ignore this email.
                                            </p>";

                    $email_html = $this->load->view('email', $email_data, TRUE);

                    if ( ! $this->utility_model->send_email($email, $subject, $email_html)) {
                        $data['info_message'] = "Sorry, we couldn't send your activation email.<br>" .
                                                "The admin has been notified about the issue " .
                                                "and will fix it as soon as possible.<br>" .
                                                "Please try again later.";
                    }
                    else {
                        $data['success_message'] = "An email has been sent to " .
                                                    "<strong>{$email}</strong>. Please " .
                                                    "use the link in that email to continue with " .
                                                    "the registration process.";
                    }
                }
            }

            if (isset($data['error_message'])) {
                $data['email'] = $email;
            }
        }

        $data['title'] = 'Sign Up: step 1 of 3';
        $this->load->view('common/header', $data);

        $this->load->view('register/step-one', $data);
        $this->load->view('common/external-page-footer');
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
                        $this->settings_model->activate_email($email, $activation_code);
                        $_SESSION['activation_code'] = $activation_code;
                        redirect(base_url("register/step-two/{$activation_code}"));
                    }
                    catch (NotFoundException $e) {
                        show_404();
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

        $data = array_merge($data, $this->user_model->initialize_user());
        $data['title'] = 'Activate your email address';
        $this->load->view('common/header', $data);

        $data['form_action'] = base_url("settings/activate-email/{$activation_code}");
        $this->load->view('settings/account/activate-email', $data);
        $this->load->view('common/footer');
    }

    public function step_two($activation_code = 0)
    {
        if (empty($_SESSION['activation_code']) ||
                $_SESSION['activation_code'] != $activation_code) {
            show_404();
        }
        else {
            unset($_SESSION['activation_code']);
        }

        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $lname = trim(strip_tags($this->input->post('lname')));
            if (strlen($lname) == 0) {
                $error_messages['lname'] = "Last name can't be empty!";
            }
            else {
                $data['lname'] = $lname;
            }

            $other_names = trim(strip_tags($this->input->post('other_names')));
            if (strlen($lname) == 0) {
                $error_messages['other_names'] = "Other names can't be empty!";
            }
            else {
                $data['other_names'] = $other_names;
            }

            $gender = trim($this->input->post('gender'));
            if (!in_array($gender, ['male', 'female'])) {
                $error_messages['gender'] = 'Please select your gender.';
            }
            else {
                $data['gender'] = ($gender == 'male')? 'M': 'F';
            }

            $day = $this->input->post('day');
            $month = $this->input->post('month');
            $year = $this->input->post('year');
            if (!checkdate($month, $day, $year)) {
                $error_messages['dob'] = 'Invalid date of birth!';
            }
            else {
                $data['dob'] = "{$year}-{$month}-{$day}";
            }

            if (isset($error_messages)) {
                $data = [
                    'lname'=>$lname, 'other_names'=>$other_names, 'gender'=>$gender,
                    'day'=>$day, 'month'=>$month, 'year'=>$year,
                    'error_messages'=>$error_messages,
                ];
            }
            else {
                session_start();
                session_regenerate_id(TRUE);

                $_SESSION['data'] = $data;
                $_SESSION['access_code'] = md5(uniqid(rand(), true));
                redirect(base_url("register/step-three/{$data['access_code']}"));
            }
        }

        $data['title'] = 'Sign Up: step 2 of 3';
        $this->load->view('common/header', $data);

        $this->load->view('register/step-two', $data);
        $this->load->view('common/external-page-footer');
    }

    public function step_three($access_code = 0)
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $uname = trim($this->input->post('uname'));
            if (strlen($uname) < 3) {
                $error_messages['uname'] = 'Username must be atleast 3 characters long!';
            }
            elseif (!preg_match('/^[A-Za-z0-9]{3,}$/', $uname)) {
                $error_messages['uname'] = 'Please ensure that your username meets ' .
                                            'the above requirements.';
            }
            else {
                $data['uname'] = $uname;
            }

            $passwd1 = $this->input->post('passwd1');
            if (strlen($passwd1) == 0) {
                $error_messages['passwd1'] = 'Please enter your password.';
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
                    $error_messages['passwd2'] = 'Please confirm your password.';
                }
                elseif ($passwd1 != $passwd2) {
                    $error_messages['passwd2'] = 'The two passwords do not match!';
                }
                else {
                    $data['passwd'] = $passwd1;
                }
            }

            if (isset($error_messages)) {
                $data = array();
                $data['uname'] = $uname;
                $data['error_messages'] = $error_messages;
            }
            else {
                session_start();
                session_regenerate_id(TRUE);

                $data = array_merge($data, $_SESSION['data']);
                $user_id = $this->register_model->register_user($data);
                unset($_SESSION['data']);

                $_SESSION['user_id'] = $user_id;
                redirect(base_url('welcome'));
            }
        }
        else {
            if (empty($_SESSION['access_code']) || ($_SESSION['access_code'] != $access_code)) {
                redirect(base_url('register/step-one'));
            }
            else {
                unset($_SESSION['access_code']);
            }
        }

        $data['title'] = 'Sign Up: step 3 of 3';
        $this->load->view('common/header', $data);

        $this->load->view('register/step-three', $data);
        $this->load->view('common/external-page-footer');
    }
}
?>
