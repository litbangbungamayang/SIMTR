<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Aktivitas_model extends CI_Model{

  private $_table = "tbl_aktivitas";
  public $tstr;
  public $tahun_giling;
  public $nama_aktivitas;
  public $biaya;

  public function simpan(){
    $post = $this->input->post();
    $this->nama_aktivitas = strtoupper($post["nama_aktivitas"]);
    $this->tstr = $post["tstr"];
    $this->tahun_giling = $post["tahun_giling"];
    $this->biaya = $post["biaya"];
    $this->db->insert($this->_table, $this);
    return $this->db->insert_id();
  }

  public function updateAktivitas(){
    $post = $this->input->post();
    $this->nama_aktivitas = strtoupper($post["nama_aktivitas"]);
    $this->tstr = $post["tstr"];
    $this->tahun_giling = $post["tahun_giling"];
    $this->biaya = $post["biaya"];
    return $this->db->where("id_aktivitas", $post["id_aktivitas"])->update($this->_table, $this);
  }

  public function hapusAktivitas($id_aktivitas = null){
    if (is_null($id_aktivitas)) $id_aktivitas = $this->input->post("id_aktivitas");
    return $this->db->delete($this->_table, array('id_aktivitas' => $id_aktivitas));
  }

  public function getAktivitasById(){
    $id_aktivitas = $this->input->get("id_aktivitas");
    $query = $this->db->select("*")->from($this->_table)->where("id_aktivitas", $id_aktivitas)->get();
    return json_encode($query->row());
  }

  public function getAllAktivitas(){
    $tstr = "TR";
    $query = $this->db->select("*")->from($this->_table)->where("tstr", $tstr)->get();
    return json_encode($query->result());
  }

  public function getAktivitasByTahunGiling(){
    $tahun_giling = $this->input->get("tahun_giling");
    $tstr = "TR";
    //return json_encode($this->db->query("select * from tbl_aktivitas where tahun_giling = $tahun_giling and tstr = '".$tstr."'")->result());
    $query = $this->db->select("*")->from($this->_table)->where("tstr", $tstr)->where("tahun_giling", $tahun_giling)->get();
    return json_encode($query->result());
  }

}