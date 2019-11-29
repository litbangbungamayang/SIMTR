<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Persediaan_model extends CI_Model{

  private $_table = "tbl_simtr_persediaan";
  public $id_transaksi;
  public $id_bahan;
  public $kode_transaksi;
  public $kuanta_bahan;
  public $rupiah_bahan;
  public $tgl_transaksi;
  public $catatan;
  public $tahun_giling;

  public function getTransaksiByIdBahan($id_bahan = null){
    if (is_null($id_bahan)) $id_bahan = $this->input->get("id_bahan");
    return json_encode($this->db->query("select * from tbl_simtr_persediaan where id_bahan = ".$id_bahan)->result());
  }

}
