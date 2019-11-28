<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bahan_model extends CI_Model{

  private $_table = "tbl_simtr_bahan";
  public $nama_bahan;
  public $jenis_bahan;
  public $satuan;

  public function rules(){
    return [
      [
        "field"=>"nama_bahan",
        "label"=>"Nama Bahan",
        "rules"=>"required",
        "errors"=>["required"=>"Nama bahan belum diinput!"]
      ],
      [
        "field"=>"jenis_bahan",
        "label"=>"Jenis Bahan",
        "rules"=>"required",
        "errors"=>["required"=>"Jenis bahan belum dipilih!"]
      ],
      [
        "field"=>"satuan",
        "label"=>"Satuan",
        "rules"=>"required",
        "errors"=>["required"=>"Satuan belum dipilih!"]
      ]
    ];
  }

  public function simpan(){
    $post = $this->input->post();
    $this->nama_bahan = strtoupper($post["nama_bahan"]);
    $this->jenis_bahan = $post["jenis_bahan"];
    $this->satuan = $post["satuan"];
    $this->db->insert($this->_table, $this);
    return $this->db->insert_id();
  }

  public function getAllBahan(){
    return json_encode($this->db->query("
      SELECT * FROM tbl_simtr_bahan
    ")->result());
  }

  public function hapus(){
    return $this->db->delete($this->_table, array('id_kelompok' => $post["id_kelompok"]));
  }

}
