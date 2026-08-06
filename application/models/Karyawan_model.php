<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Karyawan_model extends CI_Model {

    // ── CRUD KARYAWAN ──
    public function get_all_karyawan() {
        // Hanya ambil karyawan yang aktif
        return $this->db->get_where('karyawan', ['is_active' => 1])->result();
    }

    public function simpan_karyawan($data) {
        return $this->db->insert('karyawan', $data);
    }

    public function update_karyawan($id, $data) {
        return $this->db->where('id', $id)->update('karyawan', $data);
    }

    public function hapus_karyawan($id) {
        // Soft delete: set is_active menjadi 0 agar data riwayat penggajian tidak rusak
        return $this->db->where('id', $id)->update('karyawan', ['is_active' => 0]);
    }

    // ── ABSENSI ──
    public function get_absensi_harian($tanggal) {
        $sql = "SELECT k.id as karyawan_id, k.nama_lengkap, a.id as absensi_id, 
                       IFNULL(a.status, 'Belum Input') as status, 
                       IFNULL(a.lembur_jam, 0) as lembur_jam, a.keterangan 
                FROM karyawan k 
                LEFT JOIN absensi_karyawan a ON k.id = a.karyawan_id AND a.tanggal = ?
                WHERE k.is_active = 1";
        return $this->db->query($sql, [$tanggal])->result();
    }

    public function simpan_absensi_batch($data) {
        if(empty($data)) return false;
        foreach ($data as $row) {
            $this->db->replace('absensi_karyawan', $row);
        }
        return true;
    }

    // ── PENGGAJIAN BULANAN ──
    public function get_rekap_gaji($bulan, $tahun) {
        $sql = "SELECT 
                    k.id, k.nama_lengkap, k.posisi, k.gaji_pokok, 
                    k.upah_lembur_per_jam, k.potongan_alfa,
                    SUM(CASE WHEN a.status = 'Masuk' THEN 1 ELSE 0 END) as total_masuk,
                    SUM(CASE WHEN a.status IN ('Izin', 'Sakit') THEN 1 ELSE 0 END) as total_izin,
                    SUM(CASE WHEN a.status = 'Alfa' THEN 1 ELSE 0 END) as total_alfa,
                    SUM(IFNULL(a.lembur_jam, 0)) as total_lembur_jam
                FROM karyawan k
                LEFT JOIN absensi_karyawan a ON k.id = a.karyawan_id 
                     AND MONTH(a.tanggal) = ? AND YEAR(a.tanggal) = ?
                WHERE k.is_active = 1
                GROUP BY k.id";
        
        $karyawan = $this->db->query($sql, [$bulan, $tahun])->result();

        foreach($karyawan as &$k) {
            $total_lembur_rp = $k->total_lembur_jam * $k->upah_lembur_per_jam;
            $total_potongan_rp = $k->total_alfa * $k->potongan_alfa;
            $k->total_gaji_bersih = $k->gaji_pokok + $total_lembur_rp - $total_potongan_rp;
        }
        return $karyawan;
    }
}