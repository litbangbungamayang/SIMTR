<?php defined('BASEPATH') OR exit('No direct script access allowed');


  class List_skk extends CI_Controller{

    public function __construct(){
      parent:: __construct();
      if ($this->session->userdata('id_user') == false) redirect('login');
      $this->load->model("kelompoktani_model");
      $this->load->model("transaksi_model");
      $this->load->model("aktivitas_model");
      $this->load->model("bahan_model");
      $this->load->model("dokumen_model");
      $this->load->library('form_validation');
      $this->load->library('upload');
      $this->load->helper('url');
      $this->load->helper('form');
      $this->load->helper('html');
      $this->load->helper('file');
    }

    public function index(){
      if ($this->session->userdata('id_user') == false){
  			redirect('login');
      } else {
        $data['pageTitle'] = "Permohonan Survey Kelayakan";
        $data['content'] = $this->loadContent();
        $data['script'] = $this->loadScript();
        $this->load->view('main_view', $data);
      }
    }

    public function getAllRequest(){
      echo $this->kelompoktani_model->getAllRequest();
    }

    public function proses(){
      echo $this->kelompoktani_model->prosesSkk();
    }

    public function getKelompokByTahun(){
      echo $this->kelompoktani_model->getKelompokByTahun();
    }

    public function getKelompokById(){
      echo $this->kelompoktani_model->getKelompokById();
    }

    public function getKelompokByKodeBlok(){
      echo $this->kelompoktani_model->getKelompokByKodeBlok();
    }

    public function getRequestByTahunGiling(){
      echo $this->kelompoktani_model->getRequestByTahunGiling();
    }

    function loadScript(){
      return '$.getScript("'.base_url("/assets/app_js/List_skk.js").'");';
    }

    function loadContent(){
      $priv_level = $this->session->userdata("jabatan");
      $container =
      '
      <script>var priv_level = "'.$priv_level.'";</script>
      <div class="page">
        <div class="row">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="tblList" class="table card-table table-vcenter text-nowrap datatable table-md compact">
                    <thead>
                      <tr>
                        <th class="w-1">No.</th>
                        <th>Nama Kelompok</th>
                        <th>Kategori</th>
                        <th>KTG</th>
                        <th>Desa</th>
                        <th>MT</th>
                        <th>Varietas</th>
                        <th>Luas</th>
                        <th>Status</th>
                        <th>Aksi</th>
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
