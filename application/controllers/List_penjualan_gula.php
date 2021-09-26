<?php defined('BASEPATH') OR exit('No direct script access allowed');

class List_penjualan_gula extends CI_Controller{
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
      $data['pageTitle'] = "Daftar Penjualan Gula PTR";
      $data['content'] = $this->loadContent();
      $data['script'] = $this->loadScript();
      $this->load->view('main_view', $data);
    }
  }

  public function loadScript(){
    return '$.getScript("'.base_url("/assets/app_js/List_penjualan_gula.js").'");';
  }

  public function getAllPenjualanGula(){
    echo $this->transaksi_model->getAllPenjualanGula();
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
                <table id="tblListPenjualan" class="table card-table table-vcenter text-nowrap datatable table-md compact">
                  <thead>
                    <tr>
                      <th class="w-1">No.</th>
                      <th>No. Dokumen</th>
                      <th>Nama Kelompok</th>
                      <th>No. Kontrak</th>
                      <th>Kuanta (kg)</th>
                      <th>Nilai (Rp)</th>
                      <th>Tgl. Dokumen</th>
                      <th>Lihat Dokumen</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
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
