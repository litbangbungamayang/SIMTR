<?php defined('BASEPATH') OR exit('No direct script access allowed');


  class Rdkk_list extends CI_Controller{

    public function __construct(){
      parent:: __construct();
      //if ($this->session->userdata('id_user') == false) redirect('login');
      $this->load->model("kelompoktani_model");
      $this->load->library('form_validation');
      $this->load->library('upload');
      $this->load->helper('url');
      $this->load->helper('form');
      $this->load->helper('html');
    }

    public function index(){
      if ($this->session->userdata('id_user') == false){
  			redirect('login');
      } else {
        $data['pageTitle'] = "Penelusuran Data Kelompok Tani";
        $data['content'] = $this->loadContent();
        $data['script'] = $this->loadScript();
        $this->load->view('main_view', $data);
      }
    }

    public function test(){
      $kelompoktani = $this->kelompoktani_model;
      echo $kelompoktani->getAllKelompok();
    }

    public function getAllKelompok(){
      $kelompoktani = $this->kelompoktani_model;
      echo $kelompoktani->getAllKelompok();
    }

    public function getKelompokByTahun(){
      echo $this->kelompoktani_model->getKelompokByTahun();
    }

    public function getKelompokById(){
      //if ($this->session->userdata('id_user') == false) redirect('login');
      $kelompoktani = $this->kelompoktani_model;
      $idKelompok = $this->input->get('idKelompok');
      $dataKelompok = $kelompoktani->getKelompokById($idKelompok);
      var_dump($dataKelompok);
      return $dataKelompok;
    }

    function loadScript(){
      return '$.getScript("'.base_url("/assets/app_js/Rdkk_list.js").'");';
    }

    function loadContent(){
      $currYear = strval(date("Y"));
      $optionYear = "";
      for ($x = $currYear; $x <= $currYear + 4; $x++){
        $optionYear .= '<option value="'.$x.'">'.$x.'</option>';
      }
      $container =
      '
      <div class="page">
        <div class="row">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="tblList" class="table card-table table-vcenter text-nowrap datatable table-lg">
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
                        <th></th>
                      </tr>
                    </thead>
                    <tbody id="dataPetani">
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
