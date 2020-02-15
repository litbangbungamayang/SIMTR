<?php defined('BASEPATH') OR exit('No direct script access allowed');

class List_tebu_masuk extends CI_Controller{
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
      $id_kelompok = $this->input->get("id_kelompok");
      echo $id_kelompok;
      $data['pageTitle'] = "Rincian Tebu Masuk (SIMPG)";
      $data['content'] = $this->loadContent();
      $data['script'] = $this->loadScript();
      $this->load->view('main_view', $data);
    }
  }

  public function loadScript(){
    return '$.getScript("'.base_url("/assets/app_js/List_tebu_masuk.js").'");';
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
                <table id="tblTebuMasukSkrg" class="table card-table table-vcenter text-nowrap datatable table-sm compact" style="width: 100%;">
                  <thead>
                    <tr>
                      <th class="w-1">No.</th>
                      <th>No. SPTA</th>
                      <th>Tgl. Timbang</th>
                      <th>No. Truk</th>
                      <th>Bruto</th>
                      <th>Tarra</th>
                      <th>Netto</th>
                      <th>Rafaksi</th>
                      <th>Berat Setelah Rafaksi</th>
                    </tr>
                  </thead>
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
