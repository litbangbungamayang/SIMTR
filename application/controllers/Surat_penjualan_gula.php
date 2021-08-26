<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Surat_penjualan_gula extends CI_Controller{
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
      $data['pageTitle'] = "Surat Penjualan Gula PTR";
      $data['content'] = $this->loadContent();
      $data['script'] = $this->loadScript();
      $this->load->view('main_view', $data);
    }
  }

  public function loadScript(){
    return '$.getScript("'.base_url("/assets/app_js/Surat_penjualan_gula.js").'");';
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

  public function simpan(){
    var_dump($this->input->post());
  }

  public function konfirmasi(){
    $data['pageTitle'] = "Penjualan Gula TR";
    $dataContent = $this->input->post();
    $data['content'] = $this->loadKonfirmasi($dataContent);
    $data['script'] = $this->loadScript();
    $this->load->view('main_view', $data);
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
          <th>'.($i+1).'<input type="hidden" name="id_dokumen[]" value="'.$dataDokumen[0]->id_dokumen.'"</th>
          <th>'.$dataDokumen[0]->nama_kelompok.'</th>
          <th>'.$dataDokumen[0]->no_kontrak.'</th>
          <th class="text-right">'.$dataDokumen[0]->kg_gula_90.'</th>
          <th class="text-right"><input type="text" id="input_gula_'.$i.'" name="input_gula[]" value=""></th>
        </tr>
      ';
    }
    $container =
    '
    <div class="page">
      <div class="row">
        <div class="card">
          <div class="card-body">
          <form id="frm_penjualanGula" action="'.base_url('index.php/Surat_penjualan_gula/simpan').'" method="POST">
            <div class="row">
              <div class="table-responsive col-12">
                <table id="tblListPenjualan" class="table table-card table-striped table-sm text-nowrap">
                  <thead>
                    <tr>
                      <th class="w-1">No.</th>
                      <th>Nama Kelompok</th>
                      <th>No. Kontrak</th>
                      <th class="text-right">Gula PTR 90% (kg)</th>
                      <th class="text-right">Gula Terjual (kg)</th>
                    </tr>
                  </thead>
                  <tbody>
                  '.$row_content.'
                  </tbody>
                </table>
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
    return $container;
  }

  public function loadContent(){
    $container =
    '
    <div class="page">
      <div class="row">
        <div class="card">
          <div class="card-body">
          <form id="frm_basteb" action="Surat_penjualan_gula/konfirmasi" method="POST">
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
