<?php

namespace App\Controllers;

use App\Models\UserModel; 
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function register()
    {
        helper(['form']);
        return view('auth/register');
    }

    public function procRegister()
    {
        helper(['form']);
        $session = session();
        $userModel = new UserModel();

        $rules = [
            // 'first_Name'     => 'required|min_length[3]',
            // 'last_Name'      => 'required|min_length[3]',
            // 'middle_Name'    => 'permit_empty|min_length[3]',
            'name'      => 'required|min_length[3]',
            'email'     => 'required|valid_email|is_unique[users.email]',
            'password'  => 'required|min_length[6]',
            'password_confirm' => 'matches[password]'
        ];

        if (!$this->validate($rules)) {
            return view('auth/register', [
                'validation' => $this->validator
            ]);
        }
        // Iinsert na sa Users Table
        $userModel->save([
            // 'first_Name'      => $this->request->getVar('first_Name'),
            // 'last_Name'       => $this->request->getVar('last_Name'),
            // 'middle_name'     => $this->request->getVar('middle_Name'),
            'name'       => $this->request->getVar('name'),
            'email'      => $this->request->getVar('email'),
            'password'   => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
            'role'       => $this->request->getVar('role'),
        ]);
        $session->setFlashdata('success', 'Registration successful. Please login.');
        return redirect()->to('login');
    }

    public function login()
    {
        helper(['form']);
        return view('auth/login');
    }

    public function loginAttempt()
    {
        helper(['form']);
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
                'email'     => $user['email'],
                'role'      => $user['role'],
                'isLoggedIn'=> true
            ]);
            $session->setFlashdata('success', 'Welcome ' . $user['name']);
            return redirect()->to('dashboard');
        }

        $session->setFlashdata('error', 'Invalid login credentials');
        return redirect()->back();
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
        return view('dashboard');
    }

    /* 
        Guides I see
        Models: https://codeigniter.com/user_guide/models/model.html
        Helpers: https://codeigniter.com/user_guide/helpers/index.html
    */
}
