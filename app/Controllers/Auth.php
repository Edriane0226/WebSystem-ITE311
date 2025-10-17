<?php

namespace App\Controllers;

use App\Models\UserModel; 
use App\Models\EnrollmentModel;
use App\Models\CourseModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function register()
    {
        helper(['form']);

        if ($this->request->getMethod() == 'POST') {
        $rules = [
            // 'first_Name'     => 'required|min_length[3]',
            // 'last_Name'      => 'required|min_length[3]',
            // 'middle_Name'    => 'permit_empty|min_length[3]',
            'name'      => 'required|min_length[3]|regex_match[/^[A-Za-zÀ-ÖØ-öø-ÿÑñ ]+$/]', //regex(regular expression) to only allow the user to inpute letters, accented letters and space
            'email'     => 'required|valid_email|is_unique[users.email]A-Za-z0-9.@', // kani allow a-z A-Z 0-9 . @
            'password'  => 'required|min_length[6]',
            'password_confirm' => 'matches[password]'
        ];
    

        if ($this->validate($rules)) {
        // Iinsert na sa Users Table
        $userModel = new UserModel();
        $userModel->save([
            // 'first_Name'      => $this->request->getVar('first_Name'),
            // 'last_Name'       => $this->request->getVar('last_Name'),
            // 'middle_name'     => $this->request->getVar('middle_Name'),
            'name'       => $this->request->getVar('name'),
            'email'      => $this->request->getVar('email'),
            'password'   => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            'role'       => 'student',
        ]);
        
        return redirect()->to('login')->with('success', 'Registration Successful. Please login.');
        } else {
            return view('auth/register', ['validation' => $this->validator]);
        }
      }
      return view('auth/register');
    }

    public function login()
    {
        helper(['form']);

        if ($this->request->getMethod() == 'POST') {
            $session = session();
            $userModel = new UserModel();
            
            $rules = [
                'email'    => 'required|valid_email',
                'password' => 'required|min_length[6]',
            ];

            if (!$this->validate($rules)) {
                return view('auth/login', ['validation' => $this->validator]);
            }

            $user = $userModel->where('email', $this->request->getVar('email'))->first();

            if ($user && password_verify($this->request->getVar('password'), $user['password'])) {
                $session->set([
                    'userID'    => $user['userID'],
                    'name'      => $user['name'],
                    'email'     => $user['email'],
                    'role'      => $user['role'],
                    'isLoggedIn'=> true
                ]);
                //$session->setFlashdata('success', 'Welcome ' . $user['name']);
                if ($user['role'] == 'student') {
                    return redirect()->to('/announcements');
                } elseif ($user['role'] == 'teacher') {
                    return redirect()->to('/teacher/dashboard');
                } else {
                    return redirect()->to('/admin/dashboard');
                }
            }

            $session->setFlashdata('error', 'Invalid login credentials');
            return redirect()->back();
        }
        return view('auth/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }

    public function dashboard()
    {   
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        //Old Lab 5 Manual
        // $userRole = session()->get('role');
        
        // if ($userRole == 'admin') {
        //     return redirect()->to('/admin');
        // } elseif ($userRole == 'teacher') {
        //     return redirect()->to('/teacher');
        // } elseif ($userRole == 'student') {
        //     return redirect()->to('/student');
        // }
        $course = new CourseModel();
        $enrollment = new EnrollmentModel();
        $session = session();
        
            $data = [
                'name' => $session->get('name'),
                'email' => $session->get('email'),
                'role' => $session->get('role'),
                'courses' => $course->findAll(),
                'enrollments' => $enrollment->getUserEnrollments($session->get('userID')),
            ];

        return view('templates/header', $data) . view('auth/dashboard', $data);
    }
    /*
        Guides I see
        Models: https://codeigniter.com/user_guide/models/model.html
        Helpers: https://codeigniter.com/user_guide/helpers/index.html
    */
}
