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
  public $kategori;
  public $id_varietas;
  public $scan_ktp;
  public $scan_kk;
  public $scan_surat;

  public function rules(){
    return [
      [
        "field" => "namaKelompok",
        "label" => "Nama Kelompok",
        "rules" => "required",
        "errors" => ["required" => "Nama kelompok belum diinput!"]
      ],
      [
        "field" => "namaDesa",
        "label" => "ID Desa",
        "rules" => "required",
        "errors" => ["required" => "Desa belum dipilih!"]
      ],
      [
        "field" => "masaTanam",
        "label" => "Masa Tanam",
        "rules" => "required",
        "errors" => ["required" => "Masa tanam belum dipilih!"]
      ],
      [
        "field" => "kategori",
        "label" => "Kategori",
        "rules" => "required",
        "errors" => ["required" => "Kategori belum dipilih!"]
      ],
      [
        "field" => "varietas",
        "label" => "ID Varietas",
        "rules" => "required",
        "errors" => ["required" => "Varietas belum dipilih"]
      ]
    ];
  }

  public function getAll(){
    return $this->db->get($this->_table)->result();
  }

  public function simpan(){
    $post = $this->input->post();
    $this->nama_kelompok = strtoupper($post["namaKelompok"]);
    $this->id_desa = $post["namaDesa"];
    $this->mt = $post["masaTanam"];
    $this->kategori = $post["kategori"];
    $this->id_varietas = $post["varietas"];
    $this->scan_ktp = file_get_contents($_FILES["scanKtp"]["tmp_name"]);
    $this->scan_kk = file_get_contents($_FILES["scanKk"]["tmp_name"]);
    $this->scan_surat = file_get_contents($_FILES["scanSurat"]["tmp_name"]);
    $this->db->trans_begin();
    $this->db->insert($this->_table, $this);
    $lastId = $this->db->insert_id();
    $afdeling = $this->session->userdata('afd');
    $noKontrak = $afdeling."-".$this->kategori."20-".str_pad($lastId, 4, "0", STR_PAD_LEFT);
    $this->db->set('no_kontrak', $noKontrak)->where('id_kelompok', $lastId)->update($this->_table);
    if ($this->db->trans_status()){
      $this->db->trans_commit();
      return TRUE;
    } else {
      $this->db->trans_rollback();
      return FALSE;
    }
  }

  public function ubah(){
    $post = $this->input->post();
    $this->id_kelompok = $post["id_kelompok"];
    $this->nama_kelompok = $post["nama_kelompok"];
    $this->no_kontrak = $post["no_kontrak"];
    $this->id_desa = $post["id_desa"];
    $this->mt = $post["mt"];
    $this->kategori = post["kategori"];
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
