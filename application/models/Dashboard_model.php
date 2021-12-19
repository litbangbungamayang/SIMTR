<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model{

  public function loadData(){
    $query = "
      select sum(luas) as luas, count(*) as jumlah from tbl_simtr_petani ptn
      join tbl_simtr_kelompoktani kk on kk.id_kelompok = ptn.id_kelompok
      where kk.tahun_giling = ?
    ";
    $result = ($this->db->query($query,[date("Y")])->result())[0];
    $total_petani = $result->jumlah;
    $total_luas = $result->luas;
    $query = "select luas_digiling_tr as luas_tr, tebu_digiling_tr as tebu_tr, rend_tr from tbl_mon_target
              where pg=? and kategori=? and status=?";
    $result_rkap = ($this->db->query($query,["buma", "rkap", "1"])->result())[0];
    $query = "select count(*) as jumlah from tbl_simtr_kelompoktani where tahun_giling = ?";
    $total_kelompok = ($this->db->query($query, [date("Y")])->result())[0]->jumlah;
    $result = array();
    $result["total_luas"] = $total_luas;
    $result["total_petani"] = $total_petani;
    $result["total_kelompok"] = $total_kelompok;
    $result["luas_tr"] = $result_rkap->luas_tr;
    $result["tebu_tr"] = $result_rkap->tebu_tr;
    $result["rend_tr"] = $result_rkap->rend_tr;
    return json_encode($result);
  }

  public function loadDataGudang(){
    $query =
    "select
	     INV.id_bahan, BAHAN.nama_bahan, sum(case kode_transaksi when 1 then kuanta when 2 then -kuanta end) as total_kuanta,
       BAHAN.satuan, BAHAN.jenis_bahan
    from tbl_simtr_transaksi INV
    join tbl_simtr_bahan BAHAN on BAHAN.id_bahan = INV.id_bahan
    group by id_bahan";
    $result = $this->db->query($query)->result();
    return json_encode($result);
  }

}
