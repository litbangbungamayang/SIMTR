<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 */
class Wilayah_model extends CI_Model{

  private $_table = "tbl_simtr_wilayah";
  public $id_wilayah;
  public $nama_wilayah;
  public $level;

  public function getAll(){
    return $this->db->get($this->_table)->result();
  }

  public function getByNamaKabupaten($keyword_kabupaten){
    if (is_null($keyword_kabupaten)){
      $keyword_kabupaten = $this->input->post["keyword_kabupaten"];
    }
    //$kode_kabupaten = substr($this->db->like("nama_wilayah", $keyword_kabupaten)->get()->row()->id_wilayah,0,4);
    //$obj_desa = $this->db->from($this->_table)->like("id_wilayah", $kode_kabupaten, "after")->where("level", 4)->get()->result();
    //$obj_desa = $this->db->from($this->_table)->like("nama_wilayah", $keyword_kabupaten)->get()->result();
    $obj_desa = $this->db->from($this->_table)->where("level = 2 and nama_wilayah like '%".$keyword_kabupaten."%'")->get()->result();
    return json_encode($obj_desa);
  }

  public function getAllDesa(){
    return json_encode($this->db->from($this->_table)->where("level", 4)->get()->result());
  }

  public function getNamaKabupatenByIdDesa($idDesa){
    return json_encode($this->db->from($this->_table)->where("level = 2 AND id_wilayah like '".substr($idDesa, 0, 4)."%'")->get()->result());
  }

}
