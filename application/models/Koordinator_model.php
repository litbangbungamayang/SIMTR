<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Koordinator_model extends CI_Model{

  private $_table = "tbl_simtr_koordinator";
  public $nama_koordinator;
  public $no_ktp;
  public $nomor_telepon;
  public $scan_ktp;
  public $tahun_giling;

  public function rules(){
    return [];
  }

  public function simpan(){
    $post = $this->input->post();
    $scan_ktp = file_get_contents($_FILES["scanKtp"]["tmp_name"]);
    $this->tahun_giling = $post["tahun_giling"];
    $this->nama_koordinator = $post["nama_koordinator"];
    $this->no_ktp = $post["no_ktp"];
    $this->nomor_telepon = $post["nomor_telepon"];
    $this->scan_ktp = $scan_ktp;
    $this->db->insert($this->_table, $this);
    return $this->db->insert_id();
  }

  public function edit(){
    //cek transaksi yang sudah ada
    $nomor_telepon = $this->input->post("nomor_telepon");
    $id_koordinator = $this->input->post("id_koordinator");
    $query = "update tbl_simtr_koordinator set nomor_telepon = ? where id_koordinator = ?";
    return json_encode($this->db->query($query, array($nomor_telepon, $id_koordinator)));
  }

  public function getKoordById($id_koordinator = null){
    (is_null($id_koordinator)) ? $id_koordinator = $this->input->get("id_koordinator") : "";
    $query = "select id_koordinator, nama_koordinator, nomor_telepon, no_ktp, tahun_giling, TO_BASE64(scan_ktp), tahun_giling
              from tbl_simtr_koordinator";
    return (json_encode($this->db->query($query)->row()));
  }

  public function getAllKoordinator(){
    return json_encode($this->db->query("
      select id_koordinator, nama_koordinator, nomor_telepon, no_ktp, tahun_giling, TO_BASE64(scan_ktp), tahun_giling
      from tbl_simtr_koordinator
    ")->result());
  }

  public function getKoordByTahunGiling($tahun_giling = null){
    (is_null($tahun_giling)) ? $tahun_giling = $this->input->get("tahun_giling") : "";
    $result = ($this->db->query("select nama_koordinator, nomor_telepon, no_ktp, tahun_giling, TO_BASE64(scan_ktp)
      from tbl_simtr_koordinator where tahun_giling = ?", array($tahun_giling))->result());
    //var_dump($result); die();
    return json_encode($result);
  }

  public function hapus(){
    $id_potongan = $this->input->post("id_potongan");
    return $this->db->delete($this->_table, array('id_potongan' => $id_potongan));
  }

}
