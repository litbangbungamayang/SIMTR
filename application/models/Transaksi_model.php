<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transaksi_model extends CI_Model{

  private $_table = "tbl_simtr_transaksi";
  public $id_bahan;
  public $id_kelompoktani;
  public $id_vendor;
  public $kode_transaksi;
  public $kuanta;
  public $rupiah;
  public $tgl_transaksi;
  public $catatan;
  public $tahun_giling;

  public function getTransaksiByIdBahan($id_bahan = null){
    if (is_null($id_bahan)) $id_bahan = $this->input->get("id_bahan");
    return json_encode($this->db->query("select * from tbl_simtr_transaksi where id_bahan = ".$id_bahan)->result());
  }

  public function getTransaksiByIdVendor($id_vendor = null){
    if (is_null($id_vendor)) $id_vendor = $this->input->get("id_vendor");
    return json_encode($this->db->query("select * from tbl_simtr_transaksi where id_vendor = ".$id_vendor)->result());
  }

  public function getTransaksiByIdAktivitas($id_aktivitas = null){
    if (is_null($id_aktivitas)) $id_aktivitas = $this->input->get("id_aktivitas");
    return json_encode($this->db->select("*")->from($this->_table)->where("id_aktivitas", $id_aktivitas)->get()->result());
  }

  public function getTransaksiMasukByTahunGiling(){
    $tahun_giling = $this->input->get("tahun_giling");
    $query =
    "select
	     INV.id_transaksi, BAHAN.nama_bahan, INV.kode_transaksi, INV.kuanta, BAHAN.satuan,
       INV.rupiah, INV.tgl_transaksi, INV.tahun_giling, VENDOR.nama_vendor, INV.catatan
    from tbl_simtr_transaksi INV
    join tbl_simtr_vendor VENDOR on VENDOR.id_vendor = INV.id_vendor
    join tbl_simtr_bahan BAHAN on BAHAN.id_bahan = INV.id_bahan
    where INV.kode_transaksi = 1 AND INV.tahun_giling = ".$tahun_giling;
    return json_encode($this->db->query($query)->result());
  }

  public function getTransaksiKeluarByIdKelompok(){
    $id_kelompok = $this->input->get("id_kelompok");
    $query =
    "
    select
      TRANS.id_transaksi, TRANS.id_kelompoktani, BAHAN.nama_bahan, BAHAN.satuan, TRANS.no_transaksi, TRANS.kuanta, TRANS.rupiah, TRANS.tgl_transaksi
    from tbl_simtr_transaksi TRANS
    join tbl_simtr_bahan BAHAN on BAHAN.id_bahan = TRANS.id_bahan
    where TRANS.id_kelompoktani = $id_kelompok and TRANS.kode_transaksi = 2  and BAHAN.jenis_bahan = 'PUPUK'
    ";
    return json_encode($this->db->query($query)->result());
  }

  public function simpan(){
    $post = $this->input->post();
    $this->id_bahan = $post["id_bahan"];
    $this->id_kelompoktani = $post["id_kelompoktani"];
    $this->id_vendor = $post["id_vendor"];
    $this->kode_transaksi = $post["kode_transaksi"];
    $this->kuanta = $post["kuanta_bahan"];
    $this->rupiah = $post["rupiah_bahan"];
    $this->catatan = strtoupper($post["catatan"]);
    $this->tahun_giling = $post["tahun_giling"];
    $this->db->insert($this->_table, $this);
    return $this->db->insert_id();
  }

  public function getTransaksiByKode($kode_transaksi = null){
    if (is_null($kode_transaksi)) $kode_transaksi = $this->input->get("kode_transaksi");
    $query =
    "select
	     INV.id_transaksi, BAHAN.nama_bahan, INV.kode_transaksi, INV.kuanta, BAHAN.satuan,
       INV.rupiah, INV.tgl_transaksi, INV.tahun_giling, VENDOR.nama_vendor, INV.catatan
    from tbl_simtr_transaksi INV
    join tbl_simtr_vendor VENDOR on VENDOR.id_vendor = INV.id_vendor
    join tbl_simtr_bahan BAHAN on BAHAN.id_bahan = INV.id_bahan
    where INV.kode_transaksi = ".$kode_transaksi;
    return json_encode($this->db->query($query)->result());
  }

}
