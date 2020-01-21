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

  public function postPbma($id_dokumen = null, $tgl_awal = null, $tgl_akhir = null){
    if(!is_null($id_dokumen) && !is_null($tgl_awal) && !is_null($tgl_akhir)){
      $query =
      "update tbl_simtr_transaksi set id_pbma=? where tgl_transaksi >= ? and tgl_transaksi <= ?
      and kode_transaksi = 2 and kuanta = 0 and id_bahan <> 0 and rupiah <> 0 and catatan like 'BIAYA%'";
      return json_encode($this->db->query($query, array($id_dokumen, $tgl_awal, $tgl_akhir)));
    }
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
      where TRANS.no_transaksi = '$no_transaksi' and TRANS.kuanta > 0
      group by TRANS.id_transaksi";
      return json_encode($this->db->query($query)->result());
  }

  public function getTransaksiAktivitasByNoTransaksi($no_transaksi = null, $id_kelompok = null){
    if (is_null($no_transaksi) || is_null($id_kelompok)){
      $no_transaksi = $this->input->get("no_transaksi");
      $id_kelompok = $this->input->get("id_kelompok");
    }
    $query =
      "select
      	KT.nama_kelompok, KT.no_kontrak,
          (case when KT.kategori = 1 then 'PC' when KT.kategori = 2 then 'RT1'
      		when KT.kategori = 3 then 'RT2'
              when KT.kategori = 4 then 'RT3' end) as kategori,
      	PT.luas, WIL.nama_wilayah, TRANS.no_transaksi, TRANS.tgl_transaksi, AKT.nama_aktivitas, AKT.biaya, TRANS.kuanta, TRANS.rupiah
      from tbl_simtr_kelompoktani KT
      join
      	(select distinct PT.id_kelompok, sum(PT.luas) as luas from tbl_simtr_petani PT
      		join tbl_simtr_kelompoktani KT on KT.id_kelompok = PT.id_kelompok
              where KT.id_kelompok = $id_kelompok) PT on PT.id_kelompok = KT.id_kelompok
      join tbl_simtr_transaksi TRANS on TRANS.id_kelompoktani = KT.id_kelompok
      join tbl_simtr_wilayah WIL on WIL.id_wilayah = KT.id_desa
      join tbl_aktivitas AKT on AKT.id_aktivitas = TRANS.id_aktivitas
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
      TRANS.id_transaksi, TRANS.id_kelompoktani, BAHAN.id_bahan, BAHAN.nama_bahan, BAHAN.satuan,
      TRANS.no_transaksi, TRANS.kuanta, TRANS.rupiah, TRANS.tgl_transaksi, BAHAN.biaya_muat, BAHAN.biaya_angkut
    from tbl_simtr_transaksi TRANS
    join tbl_simtr_bahan BAHAN on BAHAN.id_bahan = TRANS.id_bahan
    where TRANS.id_kelompoktani = $id_kelompok and TRANS.kode_transaksi = 2  and BAHAN.jenis_bahan = 'PUPUK' and TRANS.kuanta > 0
    ";
    return json_encode($this->db->query($query)->result());
  }

  public function getTransaksiAktivitasByIdKelompok(){
    $id_kelompok = $this->input->get("id_kelompok");
    $query =
    "
    select
      TRANS.id_transaksi, TRANS.id_kelompoktani, AKTV.id_aktivitas, AKTV.nama_aktivitas, AKTV.biaya, TRANS.no_transaksi, TRANS.kuanta, TRANS.rupiah, TRANS.tgl_transaksi
    from tbl_simtr_transaksi TRANS
    join tbl_aktivitas AKTV on AKTV.id_aktivitas = TRANS.id_aktivitas
    where TRANS.id_kelompoktani = $id_kelompok and TRANS.kode_transaksi = 2
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

  public function getTransaksiByIdKelompokIdAktivitas($id_kelompok = null, $id_aktivitas = null){
    if (is_null($id_kelompok) || is_null($id_aktivitas)){
      $id_kelompok = $this->input->get("id_kelompok");
      $id_aktivitas = $this->input->get("id_aktivitas");
    }
    $query =
    "
    select sum(TRANS.kuanta) as kuanta
    from tbl_simtr_transaksi TRANS
    where TRANS.id_kelompoktani = $id_kelompok and TRANS.id_aktivitas = $id_aktivitas
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
    $this->id_aktivitas = $post["id_aktivitas"];
    $this->id_kelompoktani = $post["id_kelompoktani"];
    $this->id_vendor = $post["id_vendor"];
    $this->kode_transaksi = $post["kode_transaksi"];
    $this->no_transaksi = $post["no_transaksi"];
    $this->kuanta = $post["kuanta_bahan"];
    $this->rupiah = $post["rupiah_bahan"];
    $this->catatan = strtoupper($post["catatan"]);
    $this->tahun_giling = $post["tahun_giling"];
    $this->id_user = $this->session->userdata('id_user');
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

  public function getTransaksiBahanByIdKelompokNamaBahanPeriode($id_kelompok = null, $nama_bahan = null){
    if(is_null($id_kelompok) || is_null($nama_bahan)){
      $id_kelompok = $this->input->get("id_kelompok");
      $nama_bahan = $this->input->get("nama_bahan");
    }
    $query =
    "select
      TRANS.id_transaksi, TRANS.id_bahan, TRANS.id_kelompoktani, TRANS.no_transaksi,
      TRANS.kuanta, TRANS.rupiah, TRANS.tgl_transaksi, TRANS.tahun_giling, BHN.tahun_giling,
      BHN.satuan, BHN.biaya_muat, BHN.biaya_angkut
    from tbl_simtr_transaksi TRANS
    join tbl_simtr_bahan BHN on BHN.id_bahan = TRANS.id_bahan
    where BHN.nama_bahan = '".$nama_bahan."' and BHN.tahun_giling = TRANS.tahun_giling and
      TRANS.id_kelompoktani = $id_kelompok and TRANS.tgl_transaksi >= '2020-01-01' and
    	TRANS.tgl_transaksi <= '2020-01-15'";
    return json_encode($this->db->query($query)->result());
  }

  public function getRekapMuatAngkutPupuk($tgl_awal = null, $tgl_akhir = null, $tahun_giling = null){
    if(is_null($tgl_awal) || is_null($tgl_akhir) || is_null($tahun_giling)){
      $tgl_awal = $this->input->get("tgl_awal");
      $tgl_akhir = $this->input->get("tgl_akhir");
      $tahun_giling = $this->input->get("tahun_giling");
    }
    $query =
    "
    select
    	TRANS.id_transaksi, TRANS.id_bahan, TRANS.id_kelompoktani, TRANS.no_transaksi,
    	sum(TRANS.kuanta) as total_pupuk, TRANS.rupiah, TRANS.tgl_transaksi, TRANS.tahun_giling,
    	BHN.satuan,
      TRANS.kuanta*BHN.biaya_muat as biaya_muat,
      TRANS.kuanta*BHN.biaya_angkut as biaya_angkut
    from tbl_simtr_transaksi TRANS
    join tbl_simtr_bahan BHN on BHN.id_bahan = TRANS.id_bahan
    where BHN.tahun_giling = TRANS.tahun_giling and
    	TRANS.tgl_transaksi >= '$tgl_awal' and
    	TRANS.tgl_transaksi <= '$tgl_akhir' and
      TRANS.tahun_giling like '%$tahun_giling%' and TRANS.kuanta > 0
    group by TRANS.id_kelompoktani
    ";
    return json_encode($this->db->query($query)->result());
  }

  public function getRekapPupukByNamaBahan($tgl_awal = null, $tgl_akhir = null, $nama_bahan = null, $id_kelompok = null){
    if(is_null($id_kelompok) || is_null($tgl_awal) || is_null($tgl_akhir) || is_null($nama_bahan)){
      $id_kelompok = $this->input->get("id_kelompok");
      $tgl_awal = $this->input->get("tgl_awal");
      $tgl_akhir = $this->input->get("tgl_akhir");
      $nama_bahan = $this->input->get("nama_bahan");
    }
    $query =
    "select
      TRANS.id_transaksi, TRANS.id_bahan, TRANS.id_kelompoktani, TRANS.no_transaksi,
      TRANS.kuanta, TRANS.rupiah, TRANS.tgl_transaksi, TRANS.tahun_giling, BHN.tahun_giling,
      BHN.satuan, BHN.biaya_muat, BHN.biaya_angkut
    from tbl_simtr_transaksi TRANS
    join tbl_simtr_bahan BHN on BHN.id_bahan = TRANS.id_bahan
    where BHN.nama_bahan = '".$nama_bahan."' and
      TRANS.id_kelompoktani = $id_kelompok and
      TRANS.tgl_transaksi >= '$tgl_awal' and
    	TRANS.tgl_transaksi <= '$tgl_akhir'
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

  public function getRekapBiayaMuatAngkutPupuk($tgl_awal = null, $tgl_akhir = null, $tahun_giling = null){
    $afdeling = $this->session->userdata('afd');
    if(is_null($tgl_awal) || is_null($tgl_akhir) || is_null($tahun_giling)){
      $tgl_awal = $this->input->get("tgl_awal");
      $tgl_akhir = $this->input->get("tgl_akhir");
      $tahun_giling = $this->input->get("tahun_giling");
    }
    $query =
    "
    select
    	trans.id_kelompoktani,
      if (length(kt.nama_kelompok) > 20, concat(substring(kt.nama_kelompok,1,17), '...'), kt.nama_kelompok) as nama_kelompok,
      kt.no_kontrak,
      kt.tahun_giling,
      wil.nama_wilayah,
      date_format(trans.tgl_transaksi, '%d-%m-%Y') as tgl_transaksi,
      ( select
  			SUM(PT.luas) as luas
  		FROM tbl_simtr_kelompoktani kt
  			JOIN tbl_simtr_petani PT on PT.id_kelompok = kt.id_kelompok
  		WHERE EXISTS
    			(SELECT * FROM tbl_simtr_geocode GEO WHERE GEO.id_petani = PT.id_petani)
    		and kt.id_kelompok = trans.id_kelompoktani
    		group by kt.id_kelompok
    	) as luas,
      ( select sum(kuanta)
    		from tbl_simtr_transaksi trans_2
            join tbl_simtr_bahan bhn on trans_2.id_bahan = bhn.id_bahan
            where trans_2.id_kelompoktani = trans.id_kelompoktani
            and bhn.nama_bahan = 'UREA'
    	) as urea,
      ( select sum(kuanta)
    		from tbl_simtr_transaksi trans_2
            join tbl_simtr_bahan bhn on trans_2.id_bahan = bhn.id_bahan
            where trans_2.id_kelompoktani = trans.id_kelompoktani
            and bhn.nama_bahan = 'KCL'
    	) as kcl,
      ( select sum(kuanta)
    		from tbl_simtr_transaksi trans_2
            join tbl_simtr_bahan bhn on trans_2.id_bahan = bhn.id_bahan
            where trans_2.id_kelompoktani = trans.id_kelompoktani
            and bhn.nama_bahan = 'TSP'
    	) as tsp,
      ( select sum(kuanta)
    		from tbl_simtr_transaksi trans_2
            join tbl_simtr_bahan bhn on trans_2.id_bahan = bhn.id_bahan
            where trans_2.id_kelompoktani = trans.id_kelompoktani
            and trans_2.kuanta > 0
    	) as jml,
        ( select sum(rupiah) as rupiah
    		from tbl_simtr_transaksi
            where id_kelompoktani = trans.id_kelompoktani
            and catatan like '%BIAYA MUAT%'
    	) as biaya_muat,
      ( select sum(rupiah) as rupiah
    		from tbl_simtr_transaksi
            where id_kelompoktani = trans.id_kelompoktani
            and catatan like '%BIAYA ANGKUT%'
    	) as biaya_angkut,
      ( select sum(rupiah) as total_biaya
    		from tbl_simtr_transaksi
            where id_kelompoktani = trans.id_kelompoktani
            and catatan like '%BIAYA%'
    	) as total_biaya
    from tbl_simtr_transaksi trans
    join tbl_simtr_kelompoktani kt on trans.id_kelompoktani = kt.id_kelompok
    join tbl_simtr_wilayah wil on wil.id_wilayah = kt.id_desa
    where trans.kode_transaksi = 2 and trans.tgl_transaksi >= '$tgl_awal'
    and trans.tgl_transaksi <= '$tgl_akhir' and trans.tahun_giling like '%$tahun_giling%'
    and trans.id_pbma is null and kt.no_kontrak like '$afdeling-%' and trans.id_bahan <> 0
	  and kuanta = 0
    group by wil.nama_wilayah, trans.id_kelompoktani
    ";
    return json_encode($this->db->query($query)->result());
  }

}
