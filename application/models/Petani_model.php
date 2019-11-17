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

  public function rules_petani(){
    return [
      [
        "field" => "namaPetani",
        "label" => "Nama Petani",
        "rules" => "required",
        "errors" => ["required" => "Nama petani belum diinput"]
      ],
      [
        "field" => "fileGpxKebun",
        "label" => "Pilih file",
        "rules" => "required",
        "errors" => ["required" => "File gpx belum dipilih"]
      ]
    ];
  }

  public function getAll(){
    return $this->db->get($this->_table)->result();
  }
}
