<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('register_model');
    }

    public function step_one()
    {
        $data['title'] = "Sign Up: step 1 of 3";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($this->input->post('email'));
            if (strlen($email) == 0) {
                $data['error_message'] = "Please enter an email address!";
            }
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['error_message'] = "Please enter a valid email address!";
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
                    $data['error_message'] = 'Please enter a valid email address!';
                }
                elseif ($this->register_model->is_registered_email($email)) {

                    // Check if the user completed the registration process.
                    if ($this->register_model->email_user_id_exists($email)) {
                        $data['error_message'] = 'This email address is already registered.';
                    }
                    else {
                        $data['info_message'] = "Please use the link in the email sent to " .
                                                "<strong>{$email}</strong> to continue with " .
                                                "the registration process.<br>" .
                                                "If you deleted the email, then we can " .
                                                "<a href='" . base_url("register/resend_email/{$email}") .
                                                "'>resend the email.</a>";
                    }
                }
                else {
                    $id = $this->register_model->add_email($email);
                    if (!$this->send_register_email($id, $email)) {
                        $data['info_message'] = "Sorry, we couldn't send an email to your " .
                                                "email address.<br>" .
                                                "The admin has been notified about the issue " .
                                                "and will fix it as soon as possible.<br>" .
                                                "Please try again later.";
                    }
                    else {
                        $data['success_message'] = "An email has been sent to " .
                                                    "<strong>{$email}</strong>, please " .
                                                    "use the link in that email to continue with " .
                                                    "the registration process.";
                    }
                }
            }

            if (isset($data['error_message'])) {
                $data['email'] = $email;
            }
        }

        $this->load->view('register-step-one', $data);
        $this->load->view('common/external-page-footer');
    }

    public function step_two($user_email_id=0, $activation_code=0)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            try {
                $this->register_model->activate_email($user_email_id, $activation_code);
            }
            catch (EmailNotFoundException $e) {
                show_404();
            }
        }

        $data['title'] = "Sign Up: step 2 of 3";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $fname = trim($this->input->post('fname'));
            if (strlen($fname) == 0) {
                $error_messages['fname'] = "First name can't be empty!";
            }
            else {
                $data['fname'] = $fname;
            }

            $lname = trim($this->input->post('lname'));
            if (strlen($lname) == 0) {
                $error_messages['lname'] = "Last name can't be empty!";
            }
            else {
                $data['lname'] = $lname;
            }

            $gender = trim($this->input->post('gender'));
            if (!in_array($gender, ['male', 'female'])) {
                $error_messages['gender'] = "Please select your gender.";
            }
            else {
                $data['gender'] = ($gender == 'male')? 'M': 'F';
            }

            $day = $this->input->post('day');
            $month = $this->input->post('month');
            $year = $this->input->post('year');
            if (!checkdate($month, $day, $year)) {
                $error_messages['dob'] = "Invalid date of birth!";
            }
            else {
                $data['dob'] = "{$year}-{$month}-{$day}";
            }

            if (isset($error_messages)) {
                $data = [
                    'fname'=>$fname, 'lname'=>$lname, 'gender'=>$gender,
                    'day'=>$day, 'month'=>$month, 'year'=>$year,
                    'error_messages'=>$error_messages,
                ];
            }
            else {
                // For updating user_emails table.
                $data['user_email_id'] = $user_email_id;

                session_start();
                session_regenerate_id(TRUE);

                $_SESSION['data'] = $data;
                redirect(base_url('register/step-three'));
            }
        }

        $this->load->view('register-step-two', $data);
        $this->load->view('common/external-page-footer');
    }

    public function step_three()
    {
        if (!isset($_SESSION['data']) || !is_array($_SESSION['data'])) {
            redirect(base_url('register/step-one'));
        }

        $data['title'] = "Sign Up: step 3 of 3";
        $this->load->view('common/header', $data);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $uname = trim($this->input->post('uname'));
            if (strlen($uname) < 3) {
                $error_messages['uname'] = "Username must be atleast 3 characters long!";
            }
            elseif (!preg_match('/^[A-Za-z0-9]{3,}$/', $uname)) {
                $error_messages['uname'] = "Please ensure that your username adheres to " .
                                            "the above requirements.";
            }
            else {
                $data['uname'] = $uname;
            }

            $passwd1 = $this->input->post('passwd1');
            if (strlen($passwd1) < 6) {
                $error_messages['passwd1'] = "Password must be atleast 6 characters long!";
            }
            elseif (!preg_match('/^(\w*(?=\w*\d)(?=\w*[a-z])(?=\w*[A-Z])\w*){6,}$/', $passwd1)) {
                    $error_messages['passwd1'] = "Please ensure that your password adheres to " .
                                                    "the above requirements.";
            }
            else {
                $passwd2 = $this->input->post('passwd2');
                if ($passwd1 != $passwd2) {
                    $error_messages['passwd2'] = "The two passwords do not match!";
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

        $this->load->view('register-step-three', $data);
        $this->load->view('common/external-page-footer');
    }

    private function send_register_email($user_email_id, $email)
    {
        $this->load->library('email');

        $subject = 'Makwire account creation.';
        $activation_code = sha1($email);
        $message = '<p>Hi, thanks for your interest in makwire!</p>' .
                    '<p>Please use <a href="' .
                    base_url("register/step-two/{$user_email_id}/{$activation_code}") .
                    '">this link</a> to verify your email adress.</p>';

        // Get full html:
        $body = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=' .
            strtolower(config_item('charset')) . '" />
            <title>' . html_escape($subject) . '</title>
            <style type="text/css">
                body {
                    background-color: green;
                    font-family: Arial, Verdana, Helvetica, sans-serif;
                    font-size: 16px;
                }
            </style>
        </head>
        <body>
        ' . $message . '
        </body>
        </html>';

        $result = $this->email
                ->from('robertelvisodoch@gmail.com')
                ->to($email)
                ->subject($subject)
                ->message($body)
                ->send();

        return $result;
    }
}
?>
