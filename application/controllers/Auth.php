<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function index()
    {
        $this->login();
    }

    public function login()
    {
        // Jika sudah login, redirect ke dashboard
        if ($this->session->userdata('user')) {
            redirect('dashboard');
        }
        $data['title'] = 'Login - Ridho Interior';
        $this->load->view('auth/login', $data);
    }

    public function do_login()
    {
        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password', TRUE);

        $user = $this->User_model->login($username, $password);

        if ($user) {
            $session_data = [
                'id'           => $user->id,
                'username'     => $user->username,
                'nama_lengkap' => $user->nama_lengkap,
                'role'         => $user->role,
                'workshop_id'  => $user->workshop_id,
                'nama_workshop'=> $user->nama_workshop,
                'logged_in'    => TRUE,
            ];
            $this->session->set_userdata('user', $session_data);
            redirect('dashboard');
        } else {
            $this->session->set_flashdata('error', 'Username atau password salah!');
            redirect('auth/login');
        }
    }

    public function logout()
    {
        $this->session->unset_userdata('user');
        $this->session->sess_destroy();
        redirect('auth/login');
    }

    public function switch_workshop($id)
    {
        $user = $this->session->userdata('user');
        if ($user && $user['role'] === 'superadmin') {
            $this->session->set_userdata('selected_workshop_id', (int)$id);
        }
        
        if (isset($_SERVER['HTTP_REFERER'])) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect('dashboard');
        }
    }
}
