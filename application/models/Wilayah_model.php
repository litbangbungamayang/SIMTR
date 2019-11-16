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

  public function getAllDesa(){
    return json_encode($this->db->from($this->_table)->where("level", 4)->get()->result());
  }

  public function getNamaKabupatenByIdDesa($idDesa){
    return json_encode($this->db->from($this->_table)->where("level = 2 AND id_wilayah like '".substr($idDesa, 0, 4)."%'")->get()->result());
  }

  public function getAllKabupaten(){
    return json_encode($this->db->from($this->_table)->where("level = 2")->get()->result());
  }

  public function getDesaByKabupaten($idKab){
    return json_encode($this->db->from($this->_table)->where("level = 4 AND id_wilayah like '".substr($idKab, 0, 4)."%'")->get()->result());
  }

  public function getKecByDesa($idDesa){
    return json_encode($this->db->from($this->_table)->where("level = 3 AND id_wilayah like '".substr($idDesa, 0, 6)."%'")->get()->result());
  }

}
