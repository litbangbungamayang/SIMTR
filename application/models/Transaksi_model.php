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

  public function getAu58ByNoTransaksi(){
    $no_transaksi = $this->input->get("no_transaksi");
    $id_kelompok = $this->input->get("id_kelompok");
    $query = "select
      	KT.nama_kelompok, KT.no_kontrak,
          (case when KT.kategori = 1 then 'PC' when KT.kategori = 2 then 'RT1'
      		when KT.kategori = 3 then 'RT2'
              when KT.kategori = 4 then 'RT3' end) as kategori,
      	PT.luas, WIL.nama_wilayah, TRANS.no_transaksi, TRANS.tgl_transaksi, BAHAN.jenis_bahan, BAHAN.nama_bahan, TRANS.kuanta, BAHAN.satuan
      from tbl_simtr_kelompoktani KT
      join
      	(select distinct PT.id_kelompok, sum(PT.luas) as luas from tbl_simtr_petani PT
      		join tbl_simtr_kelompoktani KT on KT.id_kelompok = PT.id_kelompok
              where KT.id_kelompok = $id_kelompok) PT on PT.id_kelompok = KT.id_kelompok
      join tbl_simtr_transaksi TRANS on TRANS.id_kelompoktani = KT.id_kelompok
      join tbl_simtr_wilayah WIL on WIL.id_wilayah = KT.id_desa
      join tbl_simtr_bahan BAHAN on BAHAN.id_bahan = TRANS.id_bahan
      where TRANS.no_transaksi = '$no_transaksi'
      group by TRANS.id_transaksi";
      return json_encode($this->db->query($query)->result());
  }

  public function cekStokBahanByIdBahan($id_bahan = null){
    if (is_null($id_bahan)) $id_bahan = $this->input->get("id_bahan");
    $query =
    "
      select
         INV.id_bahan, BAHAN.nama_bahan, sum(case kode_transaksi when 1 then kuanta when 2 then -kuanta end) as total_kuanta,
         BAHAN.satuan, BAHAN.jenis_bahan
      from tbl_simtr_transaksi INV
      join tbl_simtr_bahan BAHAN on BAHAN.id_bahan = INV.id_bahan
      join tbl_simtr_umum UMUM on UMUM.tahun_giling = INV.tahun_giling
      where INV.id_bahan = $id_bahan
      group by id_bahan
    ";
    return json_encode($this->db->query($query)->result());
  }

  public function getTransaksiKeluarByIdKelompok(){
    $id_kelompok = $this->input->get("id_kelompok");
    $query =
    "
    select
      TRANS.id_transaksi, TRANS.id_kelompoktani, BAHAN.id_bahan, BAHAN.nama_bahan, BAHAN.satuan, TRANS.no_transaksi, TRANS.kuanta, TRANS.rupiah, TRANS.tgl_transaksi
    from tbl_simtr_transaksi TRANS
    join tbl_simtr_bahan BAHAN on BAHAN.id_bahan = TRANS.id_bahan
    where TRANS.id_kelompoktani = $id_kelompok and TRANS.kode_transaksi = 2  and BAHAN.jenis_bahan = 'PUPUK'
    ";
    return json_encode($this->db->query($query)->result());
  }

  public function getTransaksiByIdKelompokIdBahan($id_kelompok = null, $id_bahan = null){
    if (is_null($id_kelompok) || is_null($id_bahan)){
      $id_kelompok = $this->input->get("id_kelompok");
      $id_bahan = $this->input->get("id_bahan");
    }
    $query =
    "
      select sum(TRANS.kuanta) as kuanta
      from tbl_simtr_transaksi TRANS
      where TRANS.id_kelompoktani = $id_kelompok and TRANS.id_bahan = $id_bahan
    ";
    return json_encode($this->db->query($query)->row());
  }

  public function simpan($data_transaksi = null){
    if (is_null($data_transaksi)){
      $post = $this->input->post();
    } else {
      $post = $data_transaksi;
    }
    $this->id_bahan = $post["id_bahan"];
    $this->id_kelompoktani = $post["id_kelompoktani"];
    $this->id_vendor = $post["id_vendor"];
    $this->kode_transaksi = $post["kode_transaksi"];
    $this->no_transaksi = $post["no_transaksi"];
    $this->kuanta = $post["kuanta_bahan"];
    $this->rupiah = $post["rupiah_bahan"];
    $this->catatan = strtoupper($post["catatan"]);
    $this->tahun_giling = $post["tahun_giling"];
    $this->db->insert($this->_table, $this);
    return $this->db->insert_id();
  }

  public function getHargaSatuanByIdBahan($id_bahan = null){
    if (is_null($id_bahan)) $id_bahan = $this->input->get("id_bahan");
    $query =
    "
    select (jml_rupiah/jml_kuanta) as harga_unit from
      (select sum(kuanta) as jml_kuanta, sum(rupiah) as jml_rupiah from tbl_simtr_transaksi
        where kode_transaksi = 1 and id_bahan = $id_bahan) total
    ";
    return json_encode($this->db->query($query)->result());
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
