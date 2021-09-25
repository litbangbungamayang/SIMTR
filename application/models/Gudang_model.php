<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gudang_model extends CI_Model{

  private $_table = "tbl_simtr_gudang";
  public $nama_gudang;
  public $lokasi_gudang;
  public $deskripsi;
  public $status;

  public function rules(){
    return [
    ];
  }

  public function simpan(){
    $post = $this->input->post();
    $this->nama_gudang = strtoupper($post["nama_gudang"]);
    $this->lokasi_gudang = strtoupper($post["lokasi_gudang"]);
    $this->deskripsi = strtoupper($post["deskripsi"]);
    $this->status = $post["status"];
    $this->db->insert($this->_table, $this);
    return $this->db->insert_id();
  }

  public function edit(){
    $post = $this->input->post();
    $this->nama_gudang = strtoupper($post["nama_gudang"]);
    $this->lokasi_gudang = strtoupper($post["lokasi_gudang"]);
    $this->deskripsi = strtoupper($post["deskripsi"]);
    $this->status = $post["status"];
    return $this->db->where("id_gudang", $post["id_gudang"])->update($this->_table, $this);
  }

  public function getAllGudang(){
    return json_encode($this->db->query("
      select * from tbl_simtr_gudang
    ")->result());
  }

  public function getAllGudangAktif(){
    return json_encode($this->db->query("
      select * from tbl_simtr_gudang where status=?
    ", array(1))->result());
  }

  public function getGudangById(){
    $id_gudang = $this->input->get("id_gudang");
    $query = "select * from tbl_simtr_gudang where id_gudang = ?";
    return json_encode($this->db->query($query, array($id_gudang))->row());
  }

  public function hapus($id_gudang = null){
    if (is_null($id_gudang)) $id_gudang = $this->input->post("id_gudang");
    return $this->db->delete($this->_table, array('id_gudang' => $id_gudang));
  }

}
