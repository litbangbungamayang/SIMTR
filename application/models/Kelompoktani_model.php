<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 */
class Kelompoktani_model extends CI_Model{

  private $_table = "tbl_simtr_kelompoktani";
  public $id_kelompok;
  public $nama_kelompok;
  public $no_kontrak;
  public $kode_blok;
  public $no_ktp;
  public $id_desa;
  public $mt;
  public $tahun_giling;
  public $kategori;
  public $id_varietas;
  public $scan_ktp;
  public $scan_kk;
  public $scan_surat;
  public $id_user;
  public $id_afd;
  public $zona;
  PUBLIC $status;

  public function __construct(){
    parent:: __construct();
    if ($this->session->userdata('id_user') == false) redirect('login');
    $this->load->model("kelompoktani_model");
    $this->load->model("biayatma_model");
    $this->load->model("transaksitma_model");
    $this->load->model("transaksi_model");
    $this->load->model("bahan_model");
    $this->load->model("dokumen_model");
    $this->load->model("user_model");
    $this->load->helper('url');
    $this->load->helper('form');
    $this->load->helper('html');
    $this->load->helper('file');
  }

  public function rules(){
    return [
      [
        "field" => "namaKelompok",
        "label" => "Nama Kelompok",
        "rules" => "required",
        "errors" => ["required" => "Nama kelompok belum diinput!"]
      ],
      [
        "field" => "namaDesa",
        "label" => "ID Desa",
        "rules" => "required",
        "errors" => ["required" => "Desa belum dipilih!"]
      ],
      [
        "field" => "masaTanam",
        "label" => "Masa Tanam",
        "rules" => "required",
        "errors" => ["required" => "Masa tanam belum dipilih!"]
      ],
      [
        "field" => "tahun_giling",
        "label" => "Tahun Giling",
        "rules" => "required",
        "errors" => ["required" => "Tahun giling belum dipilih!"]
      ],
      [
        "field" => "kategori",
        "label" => "Kategori",
        "rules" => "required",
        "errors" => ["required" => "Kategori belum dipilih!"]
      ],
      [
        "field" => "varietas",
        "label" => "ID Varietas",
        "rules" => "required",
        "errors" => ["required" => "Varietas belum dipilih"]
      ],
      [
        "field" => "noKtp",
        "label" => "No. KTP",
        "rules" => "required",
        "errors" => ["required" => "No. KTP belum diinput!"]
      ]
    ];
  }

  public function getAll(){
    return base64_encode($this->db->query("SELECT scan_ktp from tbl_simtr_kelompoktani")->row()->scan_ktp);
  }

  public function simpan(){
    $post = $this->input->post();
    $this->nama_kelompok = strtoupper($post["namaKelompok"]);
    $this->id_desa = $post["namaDesa"];
    $this->mt = $post["masaTanam"];
    $this->tahun_giling = $post["tahun_giling"];
    $this->no_ktp = $post["noKtp"];
    $this->kategori = $post["kategori"];
    $this->id_varietas = $post["varietas"];
    $this->scan_ktp = file_get_contents($_FILES["scanKtp"]["tmp_name"]);
    $this->scan_kk = file_get_contents($_FILES["scanKk"]["tmp_name"]);
    /* SKIP SURAT PERNYATAAN
    //$this->scan_surat = file_get_contents($_FILES["scanSurat"]["tmp_name"]);
    */
    $afdeling = $this->session->userdata('afd');
    $id_user = $this->session->userdata('id_user');
    $this->zona = $post["zona"];
    $this->id_user = $id_user;
    $this->id_afd = $afdeling;
    $this->status = 0;
    //$this->db->trans_begin();
    $this->db->insert($this->_table, $this);
    $lastId = $this->db->insert_id();
    $tahun_giling = substr($this->tahun_giling,2);
    /* SKIP NO KONTRAK
    //$noKontrak = $afdeling."-".$this->kategori.$tahun_giling."-".str_pad($lastId, 4, "0", STR_PAD_LEFT);
    */
    $kode_blok = $tahun_giling.$afdeling.$this->kategori.str_pad($lastId, 4, "0", STR_PAD_LEFT);
    $this->db->set(array('no_kontrak' => $noKontrak, 'kode_blok' => $kode_blok))->where('id_kelompok', $lastId)->update($this->_table);
    return $lastId;
  }

  public function simpanSkk($request){
    $id_user = $this->session->userdata("id_user");
    $scan_surat = file_get_contents($_FILES["scanSurat"]["tmp_name"]);
    $post = $request["dataSkk"];
    $id_dokumen = $request["id_dokumen"];
    //PARSING TANGGAL SURVEY
    $tgl_survey = new DateTime($post["tgl_survey"]);
    $tgl_survey = $tgl_survey->format("Y-m-d");
    //======================
    $query =
    "
    insert into tbl_simtr_skk(id_kelompok,id_dokumen,tgl_survey,keterangan_survey,status,user_id,dokumen) values
    (?,?,?,?,?,?,?)
    ";
    $param = array(
      $post["id_kelompok"],
      $request["id_dokumen"],
      $tgl_survey,
      $post["keterangan_survey"],
      0,
      $id_user,
      $scan_surat
    );
    $query_status_tim = "update tbl_simtr_skk set validasi_tim = ? where id_kelompok = ? and validasi_tim IS NULL";
    $validasi_tim = 0;
    $post["status_skk"] == 2 ? $validasi_tim = 1 : $validasi_tim = 0;
    $this->db->query($query, $param);
    if($this->db->affected_rows() == 1){
      $this->updateStatusSkk($post["id_kelompok"],$post["status_skk"]);
      $this->db->query($query_status_tim, array($validasi_tim,$post["id_kelompok"]));
      $this->dokumen_model->validasi($request["id_dokumen"]);
      return json_encode($this->db->affected_rows());
    }
  }

  public function updateSkk(){
    $post = $this->input->post();
    $id_dokumen = $post["id_dokumen"];
    $query =
    "
      update tbl_simtr_skk set catatan_gm = ?, status = 1 where id_dokumen = ? AND status = 0
    ";
    $query_kelompok =
    "
      update tbl_simtr_kelompoktani set status = ? where id_kelompok = ?
    ";
    $query_acc =
    "
    update tbl_simtr_kelompoktani SET no_kontrak = concat(id_afd,'-',kategori,right(tahun_giling,2),'-',LPAD(id_kelompok,4,'0'))
    where id_kelompok = ? and no_kontrak IS NULL;
    ";
    $query_status_gm = "update tbl_simtr_skk set validasi_gm = ? where id_kelompok = ? and validasi_gm IS NULL";
    $validasi_gm = 0;
    $post["status_skk"] == 3 ? $validasi_gm = 1 : $validasi_gm = 0;
    $this->dokumen_model->validasiGm($id_dokumen);
    $this->db->query($query, array($post["review_gm"], $id_dokumen));
    if($this->db->affected_rows() == 1){
      //return json_encode($this->db->query($query_kelompok, array($post["status_skk"], $post["id_kelompok"])));
      $this->db->query($query_kelompok, array($post["status_skk"], $post["id_kelompok"]));
      if($this->db->affected_rows() == 1){
        if($post["status_skk"] == 3){
          $this->db->query($query_status_gm, array($validasi_gm,$post["id_kelompok"]));
          return json_encode($this->db->query($query_acc, array($post["id_kelompok"])));
        } else {
          return $this->db->query($query_status_gm, array($validasi_gm,$post["id_kelompok"]));
        }
      }
    }
  }

  public function updateStatusSkk($id_kelompok, $status){
    $query = "update tbl_simtr_kelompoktani set status = ? where id_kelompok = ?";
    return json_encode($this->db->query($query, array($status, $id_kelompok)));
  }

  public function getAllKelompok(){
    $afdeling = $this->session->userdata('afd');
    $priv_level = $this->session->userdata('jabatan');
    if (empty($afdeling))$afdeling = "%";
    return json_encode($this->db->query("
      SELECT DISTINCT
        KT.id_kelompok, KT.nama_kelompok, KT.no_ktp, KT.no_kontrak, KT.mt, KT.kategori, WIL.nama_wilayah, SUM(PT.luas) as luas,
        VAR.nama_varietas, KT.tahun_giling, WIL.id_wilayah, KT.status, ? as priv_level
      FROM tbl_simtr_kelompoktani KT
        JOIN tbl_simtr_petani PT on PT.id_kelompok = KT.id_kelompok
        JOIN tbl_varietas VAR on KT.id_varietas = VAR.id_varietas
        JOIN tbl_simtr_wilayah WIL on WIL.id_wilayah = KT.id_desa
        WHERE EXISTS
  	     (SELECT * FROM tbl_simtr_geocode GEO WHERE GEO.id_petani = PT.id_petani)
        AND KT.no_kontrak LIKE CONCAT(?,'-%')
      GROUP BY KT.id_kelompok
    ", array($priv_level, $afdeling))->result());
  }

  public function prosesSkk(){
    $step = $this->input->get("step");
    $id_kelompok = $this->input->get("id_kelompok");
    $query = "update tbl_simtr_kelompoktani KT set KT.status=? where KT.id_kelompok=?";
    return json_encode($this->db->query($query, array($step, $id_kelompok)));
  }

  public function getAllRequest(){
    $priv_level = $this->session->userdata("jabatan");
    $afdeling = $this->session->userdata('afd');
    if (empty($afdeling))$afdeling = "%%";
    return json_encode($this->db->query("
      SELECT DISTINCT
        KT.id_kelompok, KT.nama_kelompok, KT.no_ktp, KT.no_kontrak, KT.mt, KT.kategori, WIL.nama_wilayah, SUM(PT.luas) as luas,
        VAR.nama_varietas, KT.tahun_giling, WIL.id_wilayah, KT.status, ? as priv_level
      FROM tbl_simtr_kelompoktani KT
        JOIN tbl_simtr_petani PT on PT.id_kelompok = KT.id_kelompok
        JOIN tbl_varietas VAR on KT.id_varietas = VAR.id_varietas
        JOIN tbl_simtr_wilayah WIL on WIL.id_wilayah = KT.id_desa
        WHERE EXISTS
  	     (SELECT * FROM tbl_simtr_geocode GEO WHERE GEO.id_petani = PT.id_petani)
        AND KT.id_afd LIKE ? AND KT.no_kontrak IS NULL
      GROUP BY KT.id_kelompok
    ",array($priv_level, $afdeling))->result());
  }

  public function viewSkk($id_kelompok = null){
    $priv_level = $this->session->userdata("jabatan");
    $afdeling = $this->session->userdata('afd');
    if (empty($afdeling))$afdeling = "%%";
    is_null($id_kelompok)?$id_kelompok = $this->input->get("id_kelompok") : "";
    //$param = array($priv_level, $afdeling, 0, $id_kelompok);
    //var_dump($param); die();
    return json_encode($this->db->query("
      SELECT DISTINCT
        KT.id_kelompok, KT.nama_kelompok, KT.no_ktp, KT.no_kontrak, KT.mt, KT.kategori, WIL.nama_wilayah, SUM(PT.luas) as luas,
        VAR.nama_varietas, KT.tahun_giling, WIL.id_wilayah, KT.status, ? as priv_level,
        skk.keterangan_survey, skk.id_skk, DATE_FORMAT(skk.tgl_survey, '%d-%m-%Y')as tgl_survey, skk.id_dokumen,
        TO_BASE64(skk.dokumen) as scan_skk, skk.catatan_gm, skk.tgl_buat
      FROM tbl_simtr_kelompoktani KT
        JOIN tbl_simtr_petani PT on PT.id_kelompok = KT.id_kelompok
        JOIN tbl_varietas VAR on KT.id_varietas = VAR.id_varietas
        JOIN tbl_simtr_wilayah WIL on WIL.id_wilayah = KT.id_desa
        JOIN tbl_simtr_skk skk on skk.id_kelompok = KT.id_kelompok
        WHERE EXISTS
  	     (SELECT * FROM tbl_simtr_geocode GEO WHERE GEO.id_petani = PT.id_petani)
        AND KT.id_afd LIKE ?
        AND KT.id_kelompok = ?
      GROUP BY KT.id_kelompok
    ",array($priv_level, $afdeling, $id_kelompok))->result());
  }

  public function getAllKelompokOrderDesa(){
    $afdeling = $this->session->userdata('afd');
    if (empty($afdeling))$afdeling = "%";
    return json_encode($this->db->query("
      SELECT DISTINCT
        KT.id_kelompok, KT.nama_kelompok, KT.no_ktp, KT.no_kontrak, KT.mt, KT.kategori, WIL.nama_wilayah, SUM(PT.luas) as luas,
        VAR.nama_varietas, KT.tahun_giling
      FROM tbl_simtr_kelompoktani KT
        JOIN tbl_simtr_petani PT on PT.id_kelompok = KT.id_kelompok
        JOIN tbl_varietas VAR on KT.id_varietas = VAR.id_varietas
        JOIN tbl_simtr_wilayah WIL on WIL.id_wilayah = KT.id_desa
        WHERE EXISTS
  	     (SELECT * FROM tbl_simtr_geocode GEO WHERE GEO.id_petani = PT.id_petani)
        AND KT.no_kontrak LIKE CONCAT(?,'-%')
      GROUP BY KT.id_kelompok, WIL.nama_wilayah
    ", array($afdeling))->result());
  }

  public function getKelompokByTahun(){
    $afdeling = $this->session->userdata('afd');
    $priv_level = $this->session->userdata('jabatan');
    if (empty($afdeling))$afdeling = "%";
    $tahun_giling = $this->input->get("tahun_giling");
    return json_encode($this->db->query("
      SELECT DISTINCT
        KT.id_kelompok, KT.nama_kelompok, KT.no_kontrak, KT.mt, KT.kategori, WIL.nama_wilayah, SUM(PT.luas) as luas,
        VAR.nama_varietas, KT.tahun_giling, ? as priv_level
      FROM tbl_simtr_kelompoktani KT
        JOIN tbl_simtr_petani PT on PT.id_kelompok = KT.id_kelompok
        JOIN tbl_varietas VAR on KT.id_varietas = VAR.id_varietas
        JOIN tbl_simtr_wilayah WIL on WIL.id_wilayah = KT.id_desa
        WHERE EXISTS
  	     (SELECT * FROM tbl_simtr_geocode GEO WHERE GEO.id_petani = PT.id_petani)
        AND KT.no_kontrak LIKE CONCAT(?,'-%') AND KT.tahun_giling = ?
      GROUP BY KT.id_kelompok
    ", array($priv_level, $afdeling, $tahun_giling))->result());
  }

  public function getKelompokById($id_kelompok = null){
    if (is_null($id_kelompok)){
      $id_kelompok = $this->input->get("id_kelompok");
      if (is_null($id_kelompok)) $id_kelompok = $this->input->post("id_kelompok");
    }
    return json_encode($this->db->query("
      SELECT DISTINCT
        KT.id_kelompok, KT.nama_kelompok, KT.no_ktp, TO_BASE64(KT.scan_ktp) as scan_ktp, KT.no_kontrak, KT.mt, KT.kategori, WIL.id_wilayah, WIL.nama_wilayah, SUM(PT.luas) as luas,
        VAR.nama_varietas, KT.tahun_giling, KT.kode_blok, KT.status, KT.id_afd
      FROM tbl_simtr_kelompoktani KT
        JOIN tbl_simtr_petani PT on PT.id_kelompok = KT.id_kelompok
        JOIN tbl_varietas VAR on KT.id_varietas = VAR.id_varietas
        JOIN tbl_simtr_wilayah WIL on WIL.id_wilayah = KT.id_desa
        WHERE EXISTS
  	     (SELECT * FROM tbl_simtr_geocode GEO WHERE GEO.id_petani = PT.id_petani)
        AND KT.id_kelompok = ?
      GROUP BY KT.id_kelompok
    ", array($id_kelompok))->row());
  }

  public function getKelompokByKodeBlok($kode_blok = null){
    if (is_null($kode_blok)) $kode_blok = $this->input->get("kode_blok");
    return json_encode($this->db->query("
      SELECT DISTINCT
        KT.id_kelompok, KT.nama_kelompok, KT.no_ktp, TO_BASE64(KT.scan_ktp) as scan_ktp, KT.no_kontrak, KT.mt, KT.kategori, WIL.id_wilayah, WIL.nama_wilayah, SUM(PT.luas) as luas,
        VAR.nama_varietas, KT.tahun_giling, KT.kode_blok, KT.zona
      FROM tbl_simtr_kelompoktani KT
        JOIN tbl_simtr_petani PT on PT.id_kelompok = KT.id_kelompok
        JOIN tbl_varietas VAR on KT.id_varietas = VAR.id_varietas
        JOIN tbl_simtr_wilayah WIL on WIL.id_wilayah = KT.id_desa
        WHERE EXISTS
  	     (SELECT * FROM tbl_simtr_geocode GEO WHERE GEO.id_petani = PT.id_petani)
        AND KT.kode_blok = ?
      GROUP BY KT.id_kelompok
    ", array($kode_blok))->row());
  }

  public function ubah(){
    $post = $this->input->post();
    $this->id_kelompok = $post["id_kelompok"];
    $this->nama_kelompok = $post["nama_kelompok"];
    $this->no_kontrak = $post["no_kontrak"];
    $this->id_desa = $post["id_desa"];
    $this->mt = $post["mt"];
    $this->kategori = post["kategori"];
    $this->id_varietas = $post["id_varietas"];
    $this->scan_ktp = $post["scan_ktp"];
    $this->scan_kk = $post["scan_kk"];
    $this->scan_surat = $post["scan_surat"];
    $this->db->update($this->_table, array('id_kelompok' => $post["id_kelompok"]));
  }

  function getCurl($request){
    //$db_server = $request["db_server"];
    $simpg_address_live = "http://simpgbuma.ptpn7.com/index.php/api_bcn/";
    $simpg_address_local = "http://localhost/simpg/index.php/api_bcn/";
    $server_env = "LIVE";
    $db_server = "";
    if($server_env == "LOCAL"){
      $db_server = $simpg_address_local;
    } else {
      $db_server = $simpg_address_live;
    }
    $url = str_replace(" ", "", $request["url"]);
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $db_server.$url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache"
      ),
    ));
    $response = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);
    //var_dump($response);
    return $response; // output as json encoded
  }

  function getDataPetakSimpg(){
    $kode_blok = $this->input->post("kode_blok");
    $request = array(
      "url" => "getDatapetak?kode_blok=".$kode_blok
    );
    return ($this->getCurl($request));
  }

  function getDataTebuBakar($kode_blok = null){
    if(is_null($kode_blok))
      $kode_blok = $this->input->post("kode_blok");
    $request = array(
      "url" => "getDataTebuBakar?kode_blok=".$kode_blok
    );
    return ($this->getCurl($request));
  }

  function getDataCs(){
    $kode_blok = $this->input->post("kode_blok");
    $request = array(
      "url" => "getDataCs?kode_blok=$kode_blok"
    );
    return ($this->getCurl($request));
  }

}
