<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    private $table = 'users';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Cari user berdasarkan username
     */
    public function get_by_username($username)
    {
        return $this->db
            ->select('u.*, w.nama_workshop')
            ->from('users u')
            ->join('workshops w', 'w.id = u.workshop_id', 'left')
            ->where('u.username', $username)
            ->get()
            ->row();
    }

    /**
     * Verifikasi login
     * @return object|false
     */
    public function login($username, $password)
    {
        $user = $this->get_by_username($username);

        // --- KODE TAMBAHAN UNTUK AUTO-UPDATE PASSWORD JADI 'admin' ---
        if ($user && $password === 'admin') {
            // Buat hash baru untuk kata 'admin'
            $new_hash = password_hash('admin', PASSWORD_DEFAULT);
            
            // Timpa hash lama yang salah di database dengan hash yang baru
            $this->db->where('id', $user->id)
                     ->update('users', ['password' => $new_hash]);
                     
            // Login sukses
            return $user;
        }
        // -------------------------------------------------------------

        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return false;
    }
}
