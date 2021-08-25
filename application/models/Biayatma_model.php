<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Biayatma_model extends CI_Model{

  private $_table = "tbl_simtr_biayatma";
  public $tahun_giling;
  public $id_wilayah;
  public $biaya;
  public $zona;
  public $deskripsi_zona;

  public function simpan($post = null){
    if(is_null($post))$post = $this->input->post();
    $this->id_wilayah = $post["id_wilayah"];
    $this->tahun_giling = $post["tahun_giling"];
    $this->biaya = $post["biaya"];
    $this->zona = $post["zona"];
    $this->deskripsi_zona = $post["desk_zona"];
    $this->db->insert($this->_table, $this);
    return $this->db->insert_id();
  }

  public function cekDuplikat($get = null){
    if(is_null($get))$get = $this->input->get();
    $tahun_giling = $get["tahun_giling"];
    $id_wilayah = $get["id_wilayah"];
    $zona = $get["zona"];
    $query = "select * from tbl_simtr_biayatma where tahun_giling = ? and id_wilayah = ? and zona = ?";
    return json_encode($this->db->query($query, array($tahun_giling, $id_wilayah, $zona))->row());
  }

  public function simpanAffKebun($request){
    $dataAffKebun = $request["dataAffKebun"];
    $id_dokumen = $request["id_dokumen"];
    $query = "insert into tbl_simtr_ba_tebang(id_kelompok, id_dokumen, nama_kelompok, kode_blok, ton_tebu_timbang, ton_taksasi_maret,
    luas_tebang, luas_baku, ton_rafaksi_bakar, ton_rafaksi_cs, ton_rafaksi_lain, ton_tebu_hitung, ton_tebu_takmar, ton_tebu_bakar, ton_hablur_ptr,
    awal_timbang, akhir_timbang) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ";
    $this->db->query($query, array(
      $dataAffKebun["val_id_kelompok"], $id_dokumen, $dataAffKebun["val_nama_kelompok"], $dataAffKebun["val_kode_blok"], $dataAffKebun["val_tonTimbang"],
      $dataAffKebun["val_tonTakmar"], $dataAffKebun["val_luasTebang"], $dataAffKebun["val_luas"], $dataAffKebun["val_tonRafaksiBakar"],
      $dataAffKebun["val_tonRafaksiCs"], $dataAffKebun["val_tonRafaksiLain"], $dataAffKebun["val_tonTebuHitung"],
      $dataAffKebun["val_tonTakmar"], $dataAffKebun["val_tonTebuBakar"], $dataAffKebun["val_tonHablurPtr"], $dataAffKebun["val_awalTebang"], $dataAffKebun["val_akhirTebang"]
    ));
    return json_encode($this->db->affected_rows());
  }

  public function cekBeritaAcaraTebang(){
    $kode_blok = $this->input->get("kode_blok");
    $query = "select * from tbl_simtr_ba_tebang where kode_blok = ?";
    return json_encode($this->db->query($query, array($kode_blok))->num_rows());
  }

  public function getBeritaAcaraTebangByKodeBlok(){
    $kode_blok = $this->input->post("kode_blok");
    $query = "
    select * from tbl_simtr_ba_tebang ba
      join tbl_dokumen dok on ba.id_dokumen = dok.id_dokumen
    where kode_blok = ?";
    return json_encode($this->db->query($query, array($kode_blok))->row());
  }

  public function getAllBiayaTma(){
    $tahun_giling = $this->input->get("tahun_giling");
    $query =
    "
    select
    	tma.id_biayatma,
    	tma.id_wilayah,
      left(tma.id_wilayah, 4) as id_kabupaten,
      concat('DESA ', wil.nama_wilayah, ' ', kec.nama_wilayah, ' ', kab.nama_wilayah) as deskripsi,
      kab.nama_wilayah as kabupaten,
      tma.biaya,
      tma.tahun_giling,
      tma.zona,
      tma.deskripsi_zona
    from tbl_simtr_biayatma tma
    join tbl_simtr_wilayah wil on tma.id_wilayah = wil.id_wilayah
    join tbl_simtr_wilayah kec on left(kec.id_wilayah, 6) = left(tma.id_wilayah, 6)
    join tbl_simtr_wilayah kab on left(kab.id_wilayah, 4) = left(tma.id_wilayah, 4)
    where kab.level = 2 and kec.level = 3 and tma.tahun_giling like concat('%', ?, '%')
    ";
    return json_encode($this->db->query($query, array($tahun_giling))->result());
  }

  public function getBiayaTmaById(){
    $id_biaya = $this->input->get("id_biayatma");
    $query =
    "
    select
    	tma.id_biayatma,
    	tma.id_wilayah,
      kab.id_wilayah as id_kabupaten,
      concat('DESA ', wil.nama_wilayah, ' ', kec.nama_wilayah) as deskripsi,
      kab.nama_wilayah as kabupaten,
      tma.biaya,
      tma.tahun_giling,
      tma.zona,
      tma.deskripsi_zona
    from tbl_simtr_biayatma tma
    join tbl_simtr_wilayah wil on tma.id_wilayah = wil.id_wilayah
    join tbl_simtr_wilayah kec on left(kec.id_wilayah, 6) = left(tma.id_wilayah, 6)
    join tbl_simtr_wilayah kab on left(kab.id_wilayah, 4) = left(tma.id_wilayah, 4)
    where kab.level = 2 and kec.level = 3 and tma.id_biayatma = ?
    ";
    return json_encode($this->db->query($query, array($id_biaya))->row());
  }

  public function getBiayaTmaByIdWilayah($id_wilayah = null, $zona = null){
    if(is_null($id_wilayah)||is_null($zona)){
      $id_wilayah = $this->input->get("id_wilayah");
      $zona = $this->input->get("zona");
    }
    $query =
    "
    select
    	tma.id_biayatma,
    	tma.id_wilayah,
      kab.id_wilayah as id_kabupaten,
      concat('DESA ', wil.nama_wilayah, ' ', kec.nama_wilayah) as deskripsi,
      kab.nama_wilayah as kabupaten,
      tma.biaya,
      tma.tahun_giling
    from tbl_simtr_biayatma tma
    join tbl_simtr_wilayah wil on tma.id_wilayah = wil.id_wilayah
    join tbl_simtr_wilayah kec on left(kec.id_wilayah, 6) = left(tma.id_wilayah, 6)
    join tbl_simtr_wilayah kab on left(kab.id_wilayah, 4) = left(tma.id_wilayah, 4)
    where kab.level = 2 and kec.level = 3 and tma.id_wilayah = ? and tma.zona = ?
    ";
    return json_encode($this->db->query($query, array($id_wilayah, $zona))->row());
  }

  public function editBiayaTma($post = null){
    if(is_null($post)) $post = $this->input->post();
    $query = "update tbl_simtr_biayatma set tahun_giling = ?, id_wilayah = ?, biaya = ? where id_biayatma = ?";
    $this->db->query($query, array($post["tahun_giling"], $post["id_wilayah"], $post["biaya"], $post["id_biayatma"]));
    return json_encode($this->db->affected_rows());
  }

  public function getTransaksiByIdBiayaTma($id_biayatma = null){
    if(is_null($id_biayatma))$id_biayatma = $this->input->get("id_biayatma");
    $query = "select * from tbl_simtr_transaksitma trn where id_biayatma = ?";
    return json_encode($this->db->query($query, array($id_biayatma))->result());
  }

  public function hapusData($post = null){
    if(is_null($post)) $post = $this->input->post();
    $query = "delete from tbl_simtr_biayatma where id_biayatma = ?";
    $this->db->query($query, array($post["id_biayatma"]));
    return json_encode($this->db->affected_rows());
  }

}
