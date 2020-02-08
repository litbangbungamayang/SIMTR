<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Biayatma_model extends CI_Model{

  private $_table = "tbl_simtr_biayatma";
  public $tahun_giling;
  public $id_wilayah;
  public $biaya;

  public function simpan(){
    $post = $this->input->post();
    $this->id_wilayah = $post["id_wilayah"];
    $this->tahun_giling = $post["tahun_giling"];
    $this->biaya = $post["biaya"];
    $this->db->insert($this->_table, $this);
    return $this->db->insert_id();
  }

  public function cekDuplikat(){
    $tahun_giling = $this->input->get("tahun_giling");
    $id_wilayah = $this->input->get("id_wilayah");
    $query = "select * from tbl_simtr_biayatma tahun_giling = ? and id_wilayah = ?";
  }

  public function getAllBiayaTma(){
    $tahun_giling = $this->input->get("tahun_giling");
    $query =
    "
    select
    	tma.id_biayatma,
    	tma.id_wilayah,
      concat('DESA ', wil.nama_wilayah, ' ', kec.nama_wilayah) as deskripsi,
      kab.nama_wilayah as kabupaten,
      tma.biaya,
      tma.tahun_giling
    from tbl_simtr_biayatma tma
    join tbl_simtr_wilayah wil on tma.id_wilayah = wil.id_wilayah
    join tbl_simtr_wilayah kec on left(kec.id_wilayah, 6) = left(tma.id_wilayah, 6)
    join tbl_simtr_wilayah kab on left(kab.id_wilayah, 4) = left(tma.id_wilayah, 4)
    where kab.level = 2 and kec.level = 3 and tma.tahun_giling like concat('%', ?, '%')
    ";
    return json_encode($this->db->query($query, array($tahun_giling))->result());
  }

}
