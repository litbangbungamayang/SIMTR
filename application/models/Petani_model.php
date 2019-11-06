<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 */
class Petani_model extends CI_Model{

  private $_table = "tbl_simtr_petani";
  public $id_petani;
  public $id_kelompok;
  public $nama_petani;
  public $luas;
  public $id_gpx;

  public function getAll(){
    return $this->db->get($this->_table)->result();
  }
}
