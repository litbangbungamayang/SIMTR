<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Potongan_model extends CI_Model{

  private $_table = "tbl_simtr_potongan";
  public $tahun_giling;
  public $potongan_karung;
  public $potongan_tetes;
  public $potongan_admin;
  public $user;
  public $tgl_input;

  public function rules(){
    return [];
  }

  public function simpan(){
    $post = $this->input->post();
    $this->tahun_giling = $post["tahun_giling"];
    $this->potongan_karung = $post["potongan_karung"];
    $this->potongan_tetes = $post["potongan_tetes"];
    $this->potongan_admin = $post["potongan_admin"];
    $this->user = $this->session->userdata('id_user');
    $this->db->insert($this->_table, $this);
    return $this->db->insert_id();
  }

  public function edit(){
    //cek transaksi yang sudah ada
    $post = $this->input->post();
    $this->potongan_karung = $post["potongan_karung"];
    $this->potongan_tetes = $post["potongan_tetes"];
    $this->potongan_admin = $post["potongan_admin"];
    $this->user = $this->session->userdata('id_user');
    return $this->db->where("id_potongan", $post["id_potongan"])->update($this->_table, $this);
  }

  public function getPotonganById(){
    $id_potongan = $this->input->get("id_potongan");
    return json_encode($this->db->from("tbl_simtr_potongan")->where("id_potongan", $id_potongan)->get()->row());
  }

  public function getAllPotongan(){
    return json_encode($this->db->query("
      select * from tbl_simtr_potongan
    ")->result());
  }

  public function getPotonganByTahunGiling(){
    $tahun_giling = $this->input->get("tahun_giling");
    return json_encode($this->db->query("select * from tbl_simtr_potongan where tahun_giling = ?", array($tahun_giling))->result());
  }

  public function hapus(){
    $id_potongan = $this->input->post("id_potongan");
    return $this->db->delete($this->_table, array('id_potongan' => $id_potongan));
  }

}
