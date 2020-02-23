<?php defined('BASEPATH') OR exit('No direct script access allowed');


class List_tebu_masuk extends CI_Controller{

  public function __construct(){
    parent:: __construct();
    if ($this->session->userdata('id_user') == false) redirect('login');
    $this->load->model("kelompoktani_model");
    $this->load->model("transaksi_model");
    $this->load->model("bahan_model");
    $this->load->helper('url');
    $this->load->helper('form');
    $this->load->helper('html');
    $this->load->helper('file');
  }

  public function index(){
    $data['pageTitle'] = "Rincian Tebu Masuk (SIMPG)";
    $data['content'] = $this->loadContent();
    $data['script'] = $this->loadScript();
    $this->load->view('main_view', $data);
  }

  function loadScript(){
    return '$.getScript("'.base_url("/assets/app_js/List_tebu_masuk.js").'");';
  }

  public function loadContent(){
    $id_afd = $this->session->userdata("afd");
    $container =
    '
    <script>var id_afd = '.$id_afd.';</script>
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
                      <th>Kode Blok</th>
                      <th>Nama Kelompok & No. Kontrak</th>
                      <th>Netto</th>
                      <th>Tgl. Timbang</th>
                    </tr>
                  </thead>
                </table>
              </div>
              <div class="col-12">
                <div class="text-right">
                    <button id="btnTransferTma" type="button" style="margin-right: 30px; width: 200px;" class="btn btn-outline-primary">Transfer data SIMPG</button>
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
