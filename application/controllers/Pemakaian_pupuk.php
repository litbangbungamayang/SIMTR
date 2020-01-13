<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pemakaian_pupuk extends CI_Controller{
  public function __construct(){
    parent:: __construct();
    if ($this->session->userdata('id_user') == false) redirect('login');
    $this->load->model("kelompoktani_model");
    $this->load->model("transaksi_model");
    $this->load->model("aktivitas_model");
    $this->load->model("bahan_model");
    $this->load->helper('url');
    $this->load->helper('form');
    $this->load->helper('html');
    $this->load->helper('file');
  }

  public function index(){
    if ($this->session->userdata('id_user') == false){
      redirect('login');
    } else {
      $data['pageTitle'] = "Rekap Pemakaian Pupuk Kelompok Tani";
      $data['content'] = $this->loadContent();
      $data['script'] = $this->loadScript();
      $this->load->view('main_view', $data);
    }
  }

  public function loadScript(){
    return '$.getScript("'.base_url("/assets/app_js/Rekap_pupuk.js").'");';
  }

  public function getAllData(){
    
  }

  public function loadContent(){
    $container =
    '
    <div class="page">
      <div class="row">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="tblListPupuk" class="table card-table table-vcenter text-nowrap datatable table-lg">
                  <thead>
                    <tr>
                      <th class="w-1">No.</th>
                      <th>Nama Kelompok</th>
                      <th>No. Kontrak</th>
                      <th>Tahun Giling</th>
                      <th>Desa</th>
                      <th>MT</th>
                      <th>Varietas</th>
                      <th>Luas</th>
                      <th>Pupuk Urea</th>
                      <th>Pupuk TSP</th>
                      <th>Pupuk KCL</th>
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
