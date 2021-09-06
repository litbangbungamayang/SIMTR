<?php defined('BASEPATH') OR exit('No direct script access allowed');

class View_dokumen extends CI_Controller{
  public function __construct(){
    parent:: __construct();
    $this->load->model('kelompoktani_model');
    $this->load->model('wilayah_model');
    $this->load->model('petani_model');
    $this->load->model('dokumen_model');
    $this->load->helper('url');
    $this->load->helper('form');
    $this->load->helper('html');
  }

  public function index(){
    if ($this->session->userdata('id_user') == false){
      redirect('login');
    } else {
      $dataSkk = json_decode($this->kelompoktani_model->viewSkk());
      //var_dump($dataSkk);
      $data['pageTitle'] = "";
      $data['content'] = $this->loadContent($dataSkk);
      $this->load->view('main_view', $data);
    }
  }

  public function loadContent($dataKelompok){
    $content =
    '
    <div class="page">
      <div class="container">
        <div class="card">
        <div class="card-header">
          <div class="card-options">
            <a href="#" class="btn btn-primary" onclick="history.go(-1)" style="margin-right: 10px;"><i class="fe fe-corner-down-left"></i> Kembali </a>
            <a href="#" class="btn btn-primary" onclick="javascript:window.print();"><i class="fe fe-printer"></i> Cetak </a>
          </div>
        </div>
        <img src="data:image/jpg;base64,'.$dataKelompok[0]->scan_skk.'"/>
        </div>
      </div>
    </div>
    ';
    return $content;
  }

}
