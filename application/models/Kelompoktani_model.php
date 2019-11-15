<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 */
class Kelompoktani_model extends CI_Model{

  private $_table = "tbl_simtr_kelompoktani";
  public $id_kelompok;
  public $nama_kelompok;
  public $no_kontrak;
  public $id_desa;
  public $mt;
  public $id_varietas;
  public $scan_ktp;
  public $scan_kk;
  public $scan_surat;

  public function rules(){
    return [
      [
        "field" => "namaKelompok",
        "label" => "Nama Kelompok",
        "rules" => "required"
      ],
      [
        "field" => "id_desa",
        "label" => "ID Desa",
        "rules" => "required"
      ],
      [
        "field" => "mt",
        "label" => "Masa Tanam",
        "rules" => "required"
      ],
      [
        "field" => "id_varietas",
        "label" => "ID Varietas",
        "rules" => "required"
      ],
      [
        "field" => "scan_ktp",
        "label" => "Scan KTP",
        "rules" => "required"
      ],
      [
        "field" => "scan_kk",
        "label" => "Scan KK",
        "rules" => "required"
      ],
      [
        "field" => "scan_surat",
        "label" => "Scan Surat",
        "rules" => "required"
      ]
    ];
  }

  public function getAll(){
    return $this->db->get($this->_table)->result();
  }

  public function simpan(){
    $post = $this->input->post();
    $this->nama_kelompok = $post["namaKelompok"];
    $this->no_kontrak = $post["no_kontrak"];
    $this->id_desa = $post["id_desa"];
    $this->mt = $post["mt"];
    $this->id_varietas = $post["id_varietas"];
    $this->scan_ktp = $post["scan_ktp"];
    $this->scan_kk = $post["scan_kk"];
    $this->scan_surat = $post["scan_surat"];
    $this->db->insert($this->_table, $this);
    return $this->db->insert_id();
  }

  public function ubah(){
    $post = $this->input->post();
    $this->id_kelompok = $post["id_kelompok"];
    $this->nama_kelompok = $post["nama_kelompok"];
    $this->no_kontrak = $post["no_kontrak"];
    $this->id_desa = $post["id_desa"];
    $this->mt = $post["mt"];
    $this->id_varietas = $post["id_varietas"];
    $this->scan_ktp = $post["scan_ktp"];
    $this->scan_kk = $post["scan_kk"];
    $this->scan_surat = $post["scan_surat"];
    $this->db->update($this->_table, array('id_kelompok' => $post["id_kelompok"]));
  }

  public function hapus(){
    return $this->db->delete($this->_table, array('id_kelompok' => $post["id_kelompok"]));
  }

}
