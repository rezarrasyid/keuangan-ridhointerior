<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Workshop_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    // Ambil semua workshop beserta akun admin-nya
    public function get_all()
    {
        $this->db->select('w.*, u.id as user_id, u.username, u.nama_lengkap');
        $this->db->from('workshops w');
        $this->db->join('users u', "u.workshop_id = w.id AND u.role = 'admin'", 'left');
        $this->db->order_by('w.id', 'DESC');
        return $this->db->get()->result();
    }

    public function get_by_id($id)
    {
        $this->db->select('w.*, u.id as user_id, u.username, u.nama_lengkap');
        $this->db->from('workshops w');
        $this->db->join('users u', "u.workshop_id = w.id AND u.role = 'admin'", 'left');
        $this->db->where('w.id', $id);
        return $this->db->get()->row();
    }

    // Simpan workshop dan user admin secara bersamaan (Transaction)
    public function insert_with_admin($workshop_data, $user_data)
    {
        $this->db->trans_start();
        
        $this->db->insert('workshops', $workshop_data);
        $workshop_id = $this->db->insert_id();
        
        $user_data['workshop_id'] = $workshop_id;
        $user_data['role']        = 'admin';
        $this->db->insert('users', $user_data);
        
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // Update workshop dan user admin
    public function update_with_admin($workshop_id, $workshop_data, $user_id, $user_data)
    {
        $this->db->trans_start();
        
        $this->db->where('id', $workshop_id)->update('workshops', $workshop_data);
        
        if ($user_id) {
            $this->db->where('id', $user_id)->update('users', $user_data);
        } else {
            // Jaga-jaga jika sebelumnya workshop ini tidak punya akun admin
            $user_data['workshop_id'] = $workshop_id;
            $user_data['role']        = 'admin';
            $this->db->insert('users', $user_data);
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // Hapus workshop (User admin akan terhapus otomatis karena ON DELETE CASCADE)
    public function delete($id)
    {
        return $this->db->where('id', $id)->delete('workshops');
    }
}