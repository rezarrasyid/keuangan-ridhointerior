<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Controller - Base Controller
 * Semua controller yang memerlukan autentikasi harus extends class ini.
 */
class MY_Controller extends CI_Controller {

    protected $workshop_id;
    protected $user_data;

    public function __construct()
    {
        parent::__construct();
        $this->_check_auth();
    }

    private function _check_auth()
    {
        $user = $this->session->userdata('user');
        if (!$user) {
            redirect('auth/login');
            exit;
        }
        
        $this->user_data = $user;
        
        if ($user['role'] === 'superadmin') {
            $selected = $this->session->userdata('selected_workshop_id');
            if (!$selected) {
                $selected = $user['workshop_id'];
                $this->session->set_userdata('selected_workshop_id', $selected);
            }
            $this->workshop_id = $selected;
            
            // Ambil nama workshop yang terpilih secara dinamis
            $ws = $this->db->where('id', $selected)->get('workshops')->row();
            if ($ws) {
                $this->user_data['nama_workshop'] = $ws->nama_workshop;
            }
        } else {
            $this->workshop_id = $user['workshop_id'];
        }
    }

    /**
     * Load view dengan layout utama (sidebar + navbar)
     * @param string $view   - path view file
     * @param array  $data   - data yang dikirim ke view
     */
    protected function load_view($view, $data = [])
    {
        $data['user']        = $this->user_data;
        $data['workshop_id'] = $this->workshop_id;
        
        if ($this->user_data['role'] === 'superadmin') {
            $data['all_workshops'] = $this->db->get('workshops')->result();
        }
        
        $this->load->view('layouts/header', $data);
        $this->load->view('layouts/sidebar', $data);
        $this->load->view($view, $data);
        $this->load->view('layouts/footer', $data);
    }

    /**
     * Kirim JSON response (untuk endpoint AJAX)
     */
    protected function json_response($data, $status = 200)
    {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}
