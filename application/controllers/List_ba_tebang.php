<?php defined('BASEPATH') OR exit('No direct script access allowed');

class List_ba_tebang extends CI_Controller{
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
      $data['pageTitle'] = "Penelusuran Berita Acara Selesai Tebang";
      $data['content'] = $this->loadContent();
      $data['script'] = $this->loadScript();
      $this->load->view('main_view', $data);
    }
  }

  public function loadScript(){
    return '$.getScript("'.base_url("/assets/app_js/List_ba_tebang.js").'");';
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

  public function loadContent(){
    $container =
    '
    <div class="page">
      <div class="row">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="table-responsive col-12">
                <table id="tblListBasteb" class="table table-card table-striped table-sm text-nowrap">
                  <thead>
                    <tr>
                      <th class="w-1">No.</th>
                      <th>No. Dokumen</th>
                      <th>Nama Kelompok</th>
                      <th>No. Kontrak</th>
                      <th>Netto Tebu (ton)</th>
                      <th>Hablur PTR (ton)</th>
                      <th>Tgl. Dibuat</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
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