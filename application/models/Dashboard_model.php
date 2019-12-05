<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model{

  public function loadData(){
    $query = "select sum(luas) as luas, count(*) as jumlah from tbl_simtr_petani";
    $result = ($this->db->query($query)->result())[0];
    $total_petani = $result->jumlah;
    $total_luas = $result->luas;
    $query = "select count(*) as jumlah from tbl_simtr_kelompoktani";
    $total_kelompok = ($this->db->query($query)->result())[0]->jumlah;
    $result = array();
    $result["total_luas"] = $total_luas;
    $result["total_petani"] = $total_petani;
    $result["total_kelompok"] = $total_kelompok;
    return json_encode($result);
  }

  public function loadDataGudang(){
    $tahun_giling = "2020";
    $query =
    "select
	     INV.id_bahan, BAHAN.nama_bahan, sum(case kode_transaksi when 1 then kuanta_bahan when 2 then -kuanta_bahan end) as total_kuanta,
       BAHAN.satuan, BAHAN.jenis_bahan
    from tbl_simtr_persediaan INV
    join tbl_simtr_bahan BAHAN on BAHAN.id_bahan = INV.id_bahan
    where INV.tahun_giling = $tahun_giling
    group by id_bahan";
    $result = $this->db->query($query)->result();
    return json_encode($result);
  }

}
