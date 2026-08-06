<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Karyawan extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if (!isset($this->session->userdata['user']) || $this->session->userdata['user']['role'] !== 'superadmin') {
            show_error('Akses Ditolak. Halaman ini hanya untuk Superadmin.', 403);
        }
        $this->load->model('Karyawan_model');
    }

    public function index() {
        $data['title'] = 'Manajemen Karyawan & Penggajian - Ridho Interior';
        
        // Ambil status tab mana yang sedang aktif (default: data)
        $data['active_tab'] = $this->input->get('tab') ? $this->input->get('tab') : 'data';
        
        // Setup filter tanggal & bulan
        $data['tanggal_absen'] = $this->input->get('tgl') ? $this->input->get('tgl') : date('Y-m-d');
        $data['bulan_gaji'] = $this->input->get('bulan') ? str_pad($this->input->get('bulan'), 2, '0', STR_PAD_LEFT) : date('m');
        $data['tahun_gaji'] = $this->input->get('tahun') ? $this->input->get('tahun') : date('Y');

        // Ambil Data dari Database
        $data['karyawan'] = $this->Karyawan_model->get_all_karyawan();
        $data['absensi'] = $this->Karyawan_model->get_absensi_harian($data['tanggal_absen']);
        $data['rekap_gaji'] = $this->Karyawan_model->get_rekap_gaji($data['bulan_gaji'], $data['tahun_gaji']);

        $this->load_view('karyawan/index', $data);
    }

    public function tambah_karyawan() {
        $this->_simpan_data_karyawan(); // Gunakan fungsi privat agar bisa dipakai bersama dengan edit
        redirect('karyawan?tab=data');
    }

    public function edit_karyawan($id) {
        $this->_simpan_data_karyawan($id);
        redirect('karyawan?tab=data');
    }

    public function hapus_karyawan($id) {
        $this->Karyawan_model->hapus_karyawan($id);
        redirect('karyawan?tab=data');
    }

    // Fungsi pembantu untuk tambah dan edit (menghapus titik otomatis)
    private function _simpan_data_karyawan($id = null) {
        $data = [
            'nama_lengkap' => $this->input->post('nama_lengkap', TRUE),
            'posisi' => $this->input->post('posisi', TRUE),
            'gaji_pokok' => str_replace('.', '', $this->input->post('gaji_pokok')),
            'upah_lembur_per_jam' => str_replace('.', '', $this->input->post('upah_lembur_per_jam')),
            'potongan_alfa' => str_replace('.', '', $this->input->post('potongan_alfa'))
        ];

        if ($id) {
            $this->Karyawan_model->update_karyawan($id, $data);
        } else {
            $this->Karyawan_model->simpan_karyawan($data);
        }
    }

    public function simpan_absensi() {
        $tanggal = $this->input->post('tanggal');
        $karyawan_ids = $this->input->post('karyawan_id');
        $status = $this->input->post('status');
        $lembur = $this->input->post('lembur_jam');

        $data_absensi = [];
        if(!empty($karyawan_ids)){
            for ($i = 0; $i < count($karyawan_ids); $i++) {
                $data_absensi[] = [
                    'karyawan_id' => $karyawan_ids[$i],
                    'tanggal' => $tanggal,
                    'status' => $status[$i],
                    'lembur_jam' => $lembur[$i] ?: 0
                ];
            }
            $this->Karyawan_model->simpan_absensi_batch($data_absensi);
        }
        // Pastikan redirect kembali ke tab absensi dengan tanggal yang sama!
        redirect('karyawan?tab=absensi&tgl=' . $tanggal);
    }
}