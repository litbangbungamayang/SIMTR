<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Penjualan_gula extends CI_Controller{
  public function __construct(){
    parent:: __construct();
    if ($this->session->userdata('id_user') == false) redirect('login');
    $this->load->model("kelompoktani_model");
    $this->load->model("transaksi_model");
    $this->load->model("aktivitas_model");
    $this->load->model("bahan_model");
    $this->load->model("dokumen_model");
    $this->load->helper('url');
    $this->load->helper('form');
    $this->load->helper('html');
    $this->load->helper('file');
  }

  public function index(){
    if ($this->session->userdata('id_user') == false){
      redirect('login');
    } else {
      $data['pageTitle'] = "Penjualan Gula PTR";
      $data['content'] = $this->loadContent();
      $data['script'] = $this->loadScript();
      $this->load->view('main_view', $data);
    }
  }

  public function loadScript(){
    return '$.getScript("'.base_url("/assets/app_js/Penjualan_gula.js").'");';
  }

  public function getAllBasteb(){
    echo $this->transaksi_model->getAllBasteb();
  }

  public function validasiDokumen(){
    $this->dokumen_model->validasi();
  }

  public function validasiDokumenAskep(){
    $this->dokumen_model->validasiAskep();
  }

  public function getAllPembeli(){
    echo $this->transaksi_model->getAllPembeli();
  }

  public function addPembeli(){
    echo $this->transaksi_model->addPembeli();
    //var_dump($this->input->post());
  }

  public function simpan(){
    $arr_postData = array();
    $data = $this->input->post();
    $id_pembeli = $data["pembeli"];
    $arr_dokumen = $data["id_dokumen"];
    $arr_kuanta_gula = $data["input_gula"];
    $harga_jual = $data["harga_jual"];
    $tahun_giling = $data["tahun_giling"];
    $tipe_dokumen = "GPTR";
    $id_dokumen = $this->dokumen_model->simpan($tipe_dokumen, "-");
    $total_kuanta = 0;
    for($i = 0; $i < sizeof($arr_dokumen); $i++){
      $dataDokumen = json_decode($this->transaksi_model->getAllBasteb($tahun_giling,$arr_dokumen[$i]));
      $total_kuanta = $total_kuanta + $arr_kuanta_gula[$i];
      $postData = array(
        "id_bahan" => 0,
        "id_aktivitas" => 0,
        "id_kelompoktani" => $dataDokumen[0]->id_kelompok,
        "id_vendor" => 0,
        "kode_transaksi" => 3,
        "kuanta_bahan" => $arr_kuanta_gula[$i],
        "rupiah_bahan" => $arr_kuanta_gula[$i]*$harga_jual,
        "no_transaksi" => "TR-GPTR-".$dataDokumen[0]->id_kelompok."-".$tahun_giling."-".date("YmdHis"),
        "tahun_giling" => $tahun_giling,
        "catatan" => NULL
      );
      $arr_postData[] = $postData;
    }
    $dataPenjualan = array(
      "id_dokumen" => $id_dokumen,
      "id_pembeli" => $id_pembeli,
      "harga_jual" => $harga_jual,
      "total_kuanta" => $total_kuanta
    );
    if(sizeof($arr_postData) > 0){
      $this->db->trans_begin();
      foreach($arr_postData as $postData){
        $insert_id = $this->transaksi_model->simpan($postData);
        $this->transaksi_model->updateIdGptr($id_dokumen,$insert_id);
      }
      $this->transaksi_model->addPenjualanGula($dataPenjualan);
      if($this->db->trans_status()){
        $this->db->trans_commit();
      } else {
        $msg =  "Terdapat error transaksi mysql! Method getArrayPermintaanPerawatan.";
        $arrayErrorMsg[] = $msg;
        $this->db->trans_rollback();
      }
    }
    //var_dump($arr_postData);
    header('Location:'.base_url('index.php/Penjualan_gula'));
    //echo $this->transaksi_model->simpanPenjualanGula($request);
  }

  public function konfirmasi(){
    $data['pageTitle'] = "Penjualan Gula TR";
    $dataContent = $this->input->post();
    if(sizeof($dataContent) == 2){
      $data['content'] = $this->loadKonfirmasi($dataContent);
      $data['script'] = $this->loadScript();
      $this->load->view('main_view', $data);
    } else {
      header('Location:'.base_url('index.php/Surat_penjualan_gula'));
    }
  }

  public function loadKonfirmasi($dataContent){
    $tahun_giling = $dataContent["tahun_giling"];
    $dokumen = $dataContent["id_dokumen"];
    $arr_dokumen = array();
    $row_content = "";
    foreach($dokumen as $i => $dataDokumen){
      $dataDokumen = json_decode($this->transaksi_model->getAllBasteb($tahun_giling,$dokumen[$i]));
      $row_content .=
      '
        <tr>
          <th>'.($i+1).'<input type="hidden" id="id_dokumen_'.$dataDokumen[0]->id_dokumen.
              '" name="id_dokumen[]" value="'.$dataDokumen[0]->id_dokumen.'"</th>
          <input type="hidden" id="kuota_gula_'.$i.'" value="'.($dataDokumen[0]->kg_gula_90-$dataDokumen[0]->gula_terjual).'">
          <th>'.$dataDokumen[0]->nama_kelompok.'</th>
          <th>'.$dataDokumen[0]->no_kontrak.'</th>
          <th class="text-right" id="kg_gula_90_'.$i.'">'.$dataDokumen[0]->kg_gula_90.'</th>
          <th class="text-right" id="v_kuota_gula_'.$i.'">'.($dataDokumen[0]->kg_gula_90-$dataDokumen[0]->gula_terjual).'</th>
          <th class="text-right"><input type="text" id="input_gula_'.$i.'" name="input_gula[]"
              value="" onkeyup="cekInput(this)" onblur="cekSatuan(this)" style="direction: rtl;"></th>
        </tr>
      ';
    }
    $container =
    '
    <div class="page">
      <div class="row">
        <div class="card">
          <div class="card-body">
          <form id="frm_penjualanGula" action="'.base_url('index.php/Penjualan_gula/simpan').'" method="POST">
            <input type="hidden" name="tahun_giling" value="'.$tahun_giling.'">
            <div class="row">
              <div class="table-responsive col-12">
                <table id="tblListPenjualan" class="table table-card table-striped table-sm text-nowrap">
                  <thead>
                    <tr>
                      <th class="w-1">No.</th>
                      <th>Nama Kelompok</th>
                      <th>No. Kontrak</th>
                      <th class="text-right">Gula PTR 90% (kg)</th>
                      <th class="text-right">Gula Tersedia (kg)</th>
                      <th class="text-right">Gula Akan Dijual (kg)</th>
                    </tr>
                  </thead>
                  <tbody>
                  '.$row_content.'
                  </tbody>
                </table>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12 col-lg-3 mb-6">
                <label class="form-label">Harga jual gula per kilogram</label>
                <input type="text" class="form-control" name="harga_jual" id="harga_jual" style="direction: rtl;"">
              </div>
              <div class="col-md-12 col-lg-3 mb-6">
                <label class="form-label">Calon pembeli</label>
                <select type="text" class="custom-control custom-select" name="pembeli" id="txt_pembeli"
                  placeholder="Nama calon pembeli"></select>
              </div>
              <div class="col-md-12 col-lg-3 mb-6">
                <label class="form-label"><br></label>
                <div><button style="vertical-align:center" type="button" id="btnTambahPembeli" class="btn btn-outline-primary btn-sm" onclick="addPembeli()" > + Tambah data pembeli</button></div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12 col-lg-6">
                <div style="margin-bottom: 20px;"><button type="button" id="btnSimpanPenjualanGula" class="btn btn-outline-primary btn-sm" onclick="simpanPenjualanGula()" > + Konfirmasi Penjualan Gula</button></div>
              </div>
            </div>
          </form>
          </div>
        </div>
      </div>
    </div>
    ';
    $content_addPembeli =
    '
    <div class="modal fade" id="dialogAddPembeli">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Tambah Data Pembeli</h4>
            <button class="close" data-dismiss="modal" type="button"></button>
          </div>
          <div class="modal-body">
            <form id="formAddPembeli" action="'.site_url("Penjualan_gula/addPembeli").'" method="POST">
              <div class="row">
                <div class="col-md-12 col-lg-6">
                  <div class="form-group" id="grNamaKelompok">
                    <label class="form-label">Nama Pembeli</label>
                    <input type="text" style="text-transform: uppercase;" class="form-control" id="txtNamaPembeli" name="nama_pembeli">
                  </div>
                  <div class="form-group" id="grLuasDiminta"">
                    <label class="form-label">Alamat</label>
                    <input type="text" style="text-transform: uppercase;" class="form-control" id="txtAlamat" name="alamat">
                  </div>
                </div>
                <div class="col-md-12 col-lg-6">
                  <div class="form-group" id="grNoIdentitas">
                    <label class="form-label">No. Identitas</label>
                    <input type="text" style="text-transform: uppercase;" class="form-control" id="txtNoIdentitas" name="no_identitas">
                  </div>
                </div>
              </div>
              <div class="row">
                  <button type="button" id="btnSimpanPembeli" class="btn btn-primary btn-block" name="" onclick="simpanPembeli()"><i class="fe fe-save"></i> Simpan Data </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    ';
    return $container.$content_addPembeli;
  }

  public function loadContent(){
    $container =
    '
    <div class="page">
      <div class="row">
        <div class="card">
          <div class="card-body">
          <form id="frm_basteb" action="Penjualan_gula/konfirmasi" method="POST">
            <div class="row">
              <div class="table-responsive col-12">
                <table id="tblListBasteb" class="table table-card table-striped table-sm text-nowrap">
                  <thead>
                    <tr>
                      <th class="w-1">No.</th>
                      <th>Pilih</th>
                      <th>Nama Kelompok</th>
                      <th>No. Kontrak</th>
                      <th>Netto Tebu (ton)</th>
                      <th>Gula PTR 90% (kg)</th>
                      <th>Gula Terjual (kg)</th>
                      <th>Tetes PTR (kg)</th>
                      <th>Tetes Terjual (kg)</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12 col-lg-6">
                <div style="margin-bottom: 20px;"><button type="button" id="btnKonfirmasiPenjualanGula" class="btn btn-outline-primary btn-sm" onclick="konfirmasiPenjualanGula()" > + Buat Surat Permintaan Penjualan Gula</button></div>
              </div>
            </div>
          </form>
          </div>
        </div>
      </div>
    </div>
    ';
    return $container;
  }

  public function loadSurat($dataContent){
    setLocale(LC_TIME, 'id_ID.utf8');
    $id_afd = $this->session->userdata("afd");
    $tahun_giling = $dataContent["tahun_giling"];
    $dataKelompok = $dataContent["id_kelompok"];
    $hari = strftime('%A', getdate()[0]);
    $tanggal = strftime('%d', getdate()[0]);
    $bulan = strftime('%B', getdate()[0]);
    $tahun = strftime('%Y', getdate()[0]);
    $container = '
    <script>var id_afd = '.$id_afd.';</script>
    <div class="page">
      <div class="container">
        <div class="card">
          <div class="card-header">
            <div class="card-options">
              <a href="#" onclick="history.go(-1)" class="btn btn-primary" onclick="" style="margin-right: 10px;"><i class="fe fe-corner-down-left"></i> Kembali </a>
              <a href="#" class="btn btn-primary" onclick="javascript:window.print();"><i class="fe fe-printer"></i> Cetak </a>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-12 text-center mb-6"><h3>SURAT PERNYATAAN PENJUALAN TEBU RAKYAT</h3></div>
            </div>
            <div class="row">
              <div class="col-12">
                <div>
                  Pada hari ini, '.$hari.' tanggal '.$tanggal.' bulan '.$bulan.' tahun '.$tahun.',
                  kami para Ketua Kelompok Tani Tebu Rakyat yang bertandatangan dibawah ini menyatakan setuju dengan
                  harga penjualan gula 90% dari bagian Petani Tebu Rakyat sebesar Rp 10.500/kg.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    ';
    return $container;
  }
}

?>
