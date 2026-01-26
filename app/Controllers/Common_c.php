<?php

namespace App\Controllers;

use App\Models\Common_m;
use App\Models\Employeem\Cases_m;
use App\ThirdParty\smtp_mail\SMTP_mail;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;

class Common_c extends BaseController
{
    use ResponseTrait;

    private $Common_m;
    private $Cases_m;
    protected $session;
    private $_validation;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->session->start();
        helper('functions');
        helper('url');
        $this->Common_m = new Common_m();
        $this->Cases_m = new Cases_m();
        $this->_validation = \Config\Services::validation();
    }


    public function forget_password($user_type)
    {
        $validation = $this->_validation;
        if ($this->request->getMethod() == "post") {
            $validation->reset();
            $validation->setRule('username', 'Email', 'required|valid_email');

            if (!$validation->run($this->request->getPost())) {
                $redirectURL = FORGET_PASSWORD_LINK ;
                return redirect()->route('forgotPass'. $user_type)->with('error', COMMON_VALIDATION_ERROR_MSG)->withInput($this->request->getPost());
            } else {
                $_SESSION['email_not_exist'] = 0;
                $sendData = array();
                $sendData['success'] = 0;
                if ($user_type == 'customer') {
                    $resEmailCheck = $this->Common_m->email_exist_check($this->request->getPost('username'), 'hpshrc_customer');
                    if ($resEmailCheck['success']) {
                        $sendData['success'] = 1;
                    } else {
                        $_SESSION['email_not_exist'] = 1;
                    }
                }

                if ($user_type == 'employee') {
                    $resEmailCheck = $this->Common_m->email_exist_check($this->request->getPost('username'), 'employee');
                    if ($resEmailCheck['success']) {
                        $sendData['success'] = 1;
                    } else {
                        $_SESSION['email_not_exist'] = 1;
                    }
                }

                if ($sendData['success'] == 1) {

                    if ($user_type == 'employee') {
                        $user_id = $resEmailCheck['data']['employee_user_id'];
                    }
                    if ($user_type == 'customer') {
                        $user_id = $resEmailCheck['data']['customer_id'];
                    }

                    $chekReqValidity = $this->Common_m->check_forget_validity($user_type, $user_id, date("Y-m-d"));
                    if ($chekReqValidity) {
                        $link_code = forget_password_uuid($user_id, $user_type, 'e');
                        $change_password_link = CHANGE_FORGET_PASSWORD_LINK . $link_code;
                        $data = array(
                            'username' => $this->request->getPost('username'),
                            'template' => 'forgetPasswordChangeTemplate.html',
                            'change_password_link' => $change_password_link
                        );

                        include APPPATH . 'ThirdParty/smtp_mail/smtp_send.php';
                        $sendmail = new \SMTP_mail();
                        $resMail = $sendmail->sendForgetLink($this->request->getPost('username'), $data);
                        if ($resMail['success'] == 1) {
                            $params = array();
                            $params['user_id'] = $user_id;
                            $params['link_code'] = $link_code;
                            $params['user_type'] = $user_type;
                            $params['request_date'] = date("Y-m-d");
                            $this->Common_m->user_forget_link($params);

                            $_SESSION['forget_mail_sent'] = 1;
                        } else {
                            $_SESSION['send_email_error'] = 1;
                            $send_email_error = 1;
                        }
                    } else {
                        $_SESSION['forget_validity'] = 1;
                    }
                }

                return redirect()->route('forgotPass' . $user_type);
            }
        }

        if ($this->request->getMethod() == "get") {
            helper('form');
            $data['user_type'] = $user_type;
            $data['title'] = FORGET_PASSWORD_TITLE;
            $data['validation'] = $validation;
            return view('pages/forgot-password', $data);
        }
    }

    public function forget_password_change($link_code)
    {
        helper('form');
        $resCode = forget_password_uuid($link_code, '', 'd');
        $date = date("Y-m-d");
        $res = $this->Common_m->chek_forget_code_exist($resCode['user_id'], $resCode['user_type'], $link_code, $date);
        $data['success'] = 0;
        if ($res) {
            $data['user_id'] = $resCode['user_id'];
            $data['user_type'] = $resCode['user_type'];
            $data['success'] = 1;
            $data['title'] = CHANGE_FORGET_PASSWORD_TITLE;
            echo front_view('frontside/change_forget_password', $data);
            exit();
        } else {
            $data['title'] = CHANGE_FORGET_PASSWORD_TITLE;
            $data['success'] = 0;
            echo single_page('frontside/forget_expierd', $data);
            exit();
        }
    }

    public function update_forget_password()
    {
        $validation = $this->_validation;

        $validation->reset();
        $validation->setRules([
            'rmsa_user_new_password' => 'required|min_length[8]',
            'rmsa_user_confirm_password' => 'required|matches[rmsa_user_new_password]',
            'user_id' => 'required|numeric',
            'user_type' => 'required|in_list[customer,employee]',
        ]);

        if (!$validation->run($this->request->getPost())) {
            $errors = $validation->getErrors();
            return $this->respond($errors, 400);
        } else {
            $_SESSION['update_forget_password'] = 0;

            $params = [
                'rmsa_user_new_password' => $this->request->getPost('rmsa_user_new_password'),
                'rmsa_user_confirm_password' => $this->request->getPost('rmsa_user_confirm_password'),
                'user_id' => $this->request->getPost('user_id'),
                'user_type' => $this->request->getPost('user_type'),
            ];

            $res = $this->Common_m->update_forget_password($params);
            if ($res) {
                $_SESSION['update_forget_password'] = 1;
                $result['success'] = "success";
            } else {
                $result['success'] = "fail";
            }

            return $this->respond($result);
        }
    }

    public function add_cases()
    {
        $validation = $this->_validation;
        if ($this->request->getMethod() == 'post') {
            $validation->reset();

            $sso_details = $this->session->get(SSO_SESSION);
            $currentCustomer = $this->session->get('customer'); // Old logic

            $rules = [
                'cases_title' => 'required',
                'cases_party_name' => 'required|alpha_space',
                'cases_party_address' => 'permit_empty|string',
                'cases_party_number' => 'permit_empty|numeric|exact_length[10]',
                'case_files_file' => 'uploaded[case_files_file]|ext_in[case_files_file,pdf,jpg,jpeg,png]',
            ];

            if (empty($currentCustomer)) {
                // $rules['howtocontact'] = 'required|in_list[Email,Mobile,Both]';
            }

            // $howToContact = $this->request->getPost('howtocontact');
            // switch ($howToContact) {
            //     case 'Email':
            //         $rules['customer_email'] = "required|valid_email";
            //         break;
            //     case 'Mobile':
            //         $rules['customer_contact'] = "required|numeric|exact_length[10]";
            //         break;
            //     case 'Both':
            //         $rules['customer_email'] = "required|valid_email";
            //         $rules['customer_contact'] = "required|numeric|exact_length[10]";
            //         break;
            //     default:
            //         break;
            // };

            $validation->setRules($rules, [
                'cases_title' => [
                    'required' => 'Title field is required.',
                ],
                // 'howtocontact' => [
                //     'required' => 'How to contact field is required.',
                //     'in_list' => 'How to contact field must be one of: Email, Mobile, Both.'
                // ],
                'cases_party_name' => [
                    'required' => 'Party name field is required.',
                    'alpha_space' => 'Party name field may only contain alphabetical characters and spaces.',
                ],
                'cases_party_number' => [
                    'numeric' => 'Party contact number field must contain only numbers.',
                    'exact_length' => 'Party contact number field must be exactly {param} characters in length.',
                ],
                'customer_email' => [
                    'required' => 'Complainant email field is required.',
                    'valid_email' => 'Complainant email field must contain a valid email address.',
                ],
                'customer_contact' => [
                    'required' => 'Complainant mobile field is required.',
                    'numeric' => 'Complainant mobile field must contain only numbers.',
                    'exact_length' => 'Complainant mobile field must be exactly {param} characters in length.',
                ],
                'case_files_file' => [
                    'uploaded' => 'Files field is required.',
                    'ext_in' => 'One of the uploaded file does not have a valid file extension.',
                ],
            ]);

            if (!$validation->run($this->request->getPost())) {
                return redirect()->route('complaint.req')->with('error', COMMON_VALIDATION_ERROR_MSG)->withInput($this->request->getPost());
            } else {
                if (!empty($currentCustomer)) {
                    $customer_id = $currentCustomer['customer_id'];
                } else {
                    $customer_data = [
                        'customer_email_id' => $sso_details['email'],
                        'customer_mobile_no' => $sso_details['mobile'],
                        'customer_email_password' => generateStrongPassword(),
                        'customer_first_name' => $sso_details['name'],
                    ];
                    $customer_id = $this->Cases_m->create_customer($customer_data);
                    // dd($customer_data, $customer_id);
                }

                $case_data = [
                    'cases_title' => $this->request->getPost('cases_title'),
                    'cases_message' => $this->request->getPost('cases_message'),
                    'cases_dt_created' => date("Y-m-d H:i:s"),
                    'refCustomer_id' => $customer_id,
                    'createdby_user_type' => 'customer',
                    'created_by' => $customer_id,
                    'cases_party_name' => $this->request->getPost('cases_party_name'),
                    'cases_party_address' => $this->request->getPost('cases_party_address'),
                    'cases_party_number' => $this->request->getPost('cases_party_number'),
                    'customer_email' => $this->request->getPost('customer_email'),
                    'customer_contact' => $this->request->getPost('customer_contact'),
                ];

                $res = $this->Cases_m->create_case($case_data);
                if ($res) {
                    // include APPPATH . 'ThirdParty/smtp_mail/smtp_send.php';
                    // $admin_email = $this->Cases_m->get_admin_email();
                    // $email_data = array();
                    // $email_data['mail_title'] = 'Customer is created new case.';
                    // $email_data['link_title'] = 'View case details by clicking this link ';
                    // $email_data['case_link'] = EMPLOYEE_VIEW_CASES_LINK . $res;
                    // $sendmail = new \SMTP_mail();
                    // $sendmail->sendCommentDetails($admin_email['user_email_id'], $email_data);

                    // if (($_FILES['case_files_file']['name'][0]) != '') {
                    //    $cases_files = multiFileUpload('case_files_file', $res . '/');
                    //   // $cases_files = WRITEPATH . 'uploads/' . $img->store( $res.'/');
                    //     $i = 0;
                    //     foreach ($cases_files as $row) {
                    //         $params = array();
                    //         $params['refCases_id'] = $res;
                    //         $params['case_files_title'] = $_POST['title_file'][$i];
                    //         $params['case_files_desc'] = $_POST['desc_file'][$i];
                    //         $params['case_files_name'] = $row[2]['original_file_name'];
                    //         $params['case_files_unique_name'] = $row[2]['file_name'];
                    //         $params['case_files_size'] = $row[2]['file_size'];
                    //         $params['case_files_ext'] = $row[2]['file_ext'];
                    //         $params['case_files_type'] = "main";
                    //         $this->Cases_m->add_cases_files($params);
                    //         $i = $i + 1;
                    //     }
                    // }
                    
                    if ($imagefile = $this->request->getFiles()) {
                        $i = 0;
                        foreach ($imagefile['case_files_file'] as $img) {
                            $params = array();
                            if ($img->isValid() && ! $img->hasMoved()) {

                                $newName      = $img->getRandomName();
                                $originalName = $img->getClientName();
                                $ext          = $img->getClientExtension();
                                $sizeKb       = $img->getSizeByUnit('kb');                       

                                $storedPath = $img->store('doc/causes/', $newName);
                                $filepath   = WRITEPATH . 'uploads/' . $storedPath;

                                $file = new File($filepath);

                                $params = [
                                    'refCases_id'            => $res,
                                    'case_files_title'       => $_POST['title_file'][$i] ?? null,
                                    'case_files_desc'        => $_POST['desc_file'][$i] ?? null,
                                    'case_files_name'        => $newName,
                                    'case_files_unique_name' => $originalName,
                                    'case_files_ext'         => $ext,
                                    'case_files_size'        => $sizeKb,
                                    'case_files_type'        => 'main',
                                ];
                            }
                            $this->Cases_m->add_cases_files($params);
                            $i = $i + 1;
                        }
                    }

                    $params = array();
                    $params['refCases_id'] = $res;
                    $params['comment_type'] = 'create';
                    $params['comment_from'] = $customer_id;
                    $params['comment_to'] = $customer_id;
                    $params['comment_from_usertype'] = 'customer';
                    $params['comment_to_usertype'] = 'customer';
                    $params['comment_datetime'] = date("Y-m-d H:i:s");
                    $this->Cases_m->add_cases_comment($params);
                } else {
                    successOrErrorMessage("Somthing happen wrong plz try again", 'error');
                    return redirect()->route('case.list')->with('error', 'Somthing happen wrong plz try again.');
                }

                successOrErrorMessage("Request sent successfully", 'success');
                return redirect()->route('case.list')->with('success', 'Request sent successfully.');
            }
        }

        if ($this->request->getMethod() == 'get') {
            helper('form');
            $data['title'] = REQUEST_CASES_TITLE;
            $data['validation'] = $validation;
            return view('pages/case-request', $data);
        }
    }

    public function create_customer()
    {
        $validation = $this->_validation;

        $_SESSION['exist_email'] = 0;

        if ($this->request->getMethod() == 'post') {
            include APPPATH . 'ThirdParty/smtp_mail/smtp_send.php';

            $validation->reset();
            $validation->setRules([
                'customer_first_name' => 'required|alpha',
                'customer_middle_name' => 'permit_empty|alpha',
                'customer_last_name' => 'required|alpha',
                'customer_father_name' => 'required|alpha_space',
                'customer_mobile_no' => 'required|numeric|exact_length[10]',
                'customer_email_id' => 'required|valid_email|is_unique[hpshrc_customer.customer_email_id]',
                'customer_email_password' => 'required|min_length[8]',
                'user_confirm_password' => 'required|matches[customer_email_password]',
                'customer_dob' => 'required|valid_date',
                'customer_gender' => 'required|in_list[M,F,O]',
            ], [
                'customer_first_name' => [
                    'required' => 'First name field is required.',
                    'alpha' => 'First name field may only contain alphabetical characters.',
                ],
                'customer_middle_name' => [
                    'alpha' => 'Middle name field may only contain alphabetical characters.'
                ],
                'customer_last_name' => [
                    'required' => 'Last name field is required.',
                    'alpha' => 'Last name field may only contain alphabetical characters.'
                ],
                'customer_father_name' => [
                    'required' => 'Father name field is required.',
                    'alpha_space' => 'Father name field may only contain alphabetical characters and spaces.'
                ],
                'customer_mobile_no' => [
                    'required' => 'Mobile number field is required.',
                    'numeric' => 'Mobile number field must contain only numbers.',
                    'exact_length' => 'Mobile number field must be exactly {param} characters in length.',
                ],
                'customer_email_id' => [
                    'required' => 'Email field is required.',
                    'valid_email' => 'Email field must contain a valid email address.',
                    'is_unique' => 'Email field must contain a unique value.',
                ],
                'customer_email_password' => [
                    'required' => 'Password field is required.',
                    'min_length' => 'Password field must be at least {param} characters in length.',
                ],
                'user_confirm_password' => [
                    'required' => 'Confirm password field is required.',
                    'matches' => 'Password does not match.'
                ],
                'customer_dob' => [
                    'required' => 'Date of birth field is required.',
                    'valid_date' => 'Date of birth field must contain a valid date.',
                ],
                'customer_gender' => [
                    'required' => 'Gender field is required.',
                    'in_list' => 'Gender field must be one of: Male, Female, Other.'
                ],
            ]);

            if (!$validation->run($this->request->getPost())) {
                return redirect()->route('admin.customer_registration')->with('error', COMMON_VALIDATION_ERROR_MSG)->withInput($this->request->getPost());
            } else {
                $params = [
                    'customer_first_name' => $this->request->getPost('customer_first_name'),
                    'customer_middle_name' => $this->request->getPost('customer_middle_name'),
                    'customer_last_name' => $this->request->getPost('customer_last_name'),
                    'customer_father_name' => $this->request->getPost('customer_father_name'),
                    'customer_mobile_no' => $this->request->getPost('customer_mobile_no'),
                    'customer_email_id' => $this->request->getPost('customer_email_id'),
                    'customer_email_password' => $this->request->getPost('customer_email_password'),
                    'customer_dob' => $this->request->getPost('customer_dob'),
                    'customer_gender' => $this->request->getPost('customer_gender'),
                ];

                $res =  $this->Common_m->register_customer($params);
                $result = array();
                $send_email_error = 0;

                if ($res['success'] == true) {
                    $result['success'] = 'success';
                    $link_code = gen_uuid($res['customer_id'], 'e');
                    $email_active_link = CUSTOMER_ACTIVE_EMAIL_LINK . 'customer/' . $link_code;
                    $result['success'] = 'success';
                    $data = array(
                        'username' => $res['email'],
                        'password' => $_POST['customer_email_password'],
                        'template' => 'studentRegistrationTemplate.html',
                        'activationlink' => $email_active_link
                    );
                    $sendmail = new \SMTP_mail();
                    $resMail = $sendmail->sendRegistrationDetails($res['email'], $data);
                    if ($resMail['success'] == 1) {
                        $params = array();
                        $params['user_id'] = $res['customer_id'];
                        $params['link_code'] = $link_code;
                        $params['user_type'] = 'customer';
                        $this->Common_m->user_email_link($params);
                    } else {
                        $_SESSION['send_email_error'] = 1;
                        $send_email_error = 1;
                    }
                } else {
                    if (isset($res['email_exist'])) {
                        if ($res['email_exist'] == true) {
                            $_SESSION['exist_email'] = 1;
                            $result['exist_email'] = 1;
                        }
                    }
                    $result['success'] = 'fail';
                }

                if ($result['success'] == 'success' && $send_email_error == 1) {
                    $_SESSION['registration'] = 1;
                }

                if ($result['success'] == 'success' && $send_email_error == 0) {
                    $_SESSION['registration'] = 2;
                }

                if ($result['success'] == 'fail') {
                    $_SESSION['registration'] = 3;
                }

                if ($result['success'] == "success") {
                    if (isset($_SESSION['post_data'])) {
                        unset($_SESSION['post_data']);
                    }
                }

                return redirect()->route('admin.customer_registration');
            }
        }

        if ($this->request->getMethod() == 'get') {
            if (isset($_SESSION['customer']['customer_id'])) {
                return redirect()->to(BASE_URL);
            }

            if (isset($_SESSION['post_data'])) {
                unset($_SESSION['post_data']);
            }

            helper('form');
            $data['title'] = CUSTOMER_REGISTRATION_TITLE;
            $data['validation'] = $validation;
            return view('pages/register', $data);
            // echo front_view('frontside/user_registration', $data);
            // exit();
        }
    }

    public function verify_email($user_type, $link_code)
    {
        if (!in_array($user_type, ["customer", "employee"])) {
            return redirect()->to('/')->with('error', 'Invalid user type.');
        }

        if (!preg_match('/^[a-f0-9]{32}-[A-Za-z0-9+=\/]+$/', $link_code)) {
            return redirect()->to('/')->with('error', 'Invalid link code.');
        }

        $user_id = gen_uuid($link_code, 'd');
        $res = $this->Common_m->chek_code_exist($user_id, $link_code, $user_type);
        $data['success'] = 0;
        if ($res) {
            $data['success'] = 1;
        }
        echo single_page('frontside/thankyou', $data);
    }

    public function createSymlink()
    {
        $target = WRITEPATH . 'uploads';
        $link   = FCPATH  .   'uploads';
        if(!file_exists($link))
        {
            if(symlink($target, $link )){
                echo "Symlink created successfully!";
            } else {
                echo "Failed to create Symlink";
            }
        } else {
            echo "Symlink already exists !";
        }
    } 
}
