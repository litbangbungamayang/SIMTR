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
      $this->load->helper('file');
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
      echo $this->kelompoktani_model->getKelompokById();
    }

    function loadScript(){
      return '$.getScript("'.base_url("/assets/app_js/Rdkk_list.js").'");';
    }

    function loadContent(){
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
      $content_dialogAddPermintaanPupuk =
      '
      <div class="modal fade" id="dialogAddPermintaanPupuk">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Permintaan Pupuk</h4>
              <button class="close" data-dismiss="modal" type="button"></button>
            </div>
            <div class="modal-body">
              <form id="formAddPermintaanPupuk">
                <div class="row">
                  <div class="col-md-12 col-lg-12">
                    <label class="form-label">Daftar Transaksi Pupuk Kelompok</label>
                    <table id="tblPupuk" class="table card-table table-vcenter text-nowrap datatable table-lg">
                      <thead>
                        <tr>
                          <th class="w-1">No.</th>
                          <th>No. Transaksi</th>
                          <th>Tgl. Transaksi</th>
                          <th>Jenis Pupuk</th>
                          <th>Kuanta</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 col-lg-6">
                    <div class="form-group" id="grNamaKelompok" style="margin-top: 25px;">
                      <label class="form-label">Nama Kelompok</label>
                      <input type="text" style="text-transform: uppercase;" class="form-control" id="namaKelompok" disabled>
                    </div>
                    <div class="form-group" id="grJenisPupuk">
                      <label class="form-label">Jenis pupuk yang diminta</label>
                      <select name="jenis_bahan" id="jenis_pupuk" class="custom-control custom-select" placeholder="Pilih jenis pupuk">
                        <option value="">Pilih jenis pupuk</option>
                      </select>
                      <div class="invalid-feedback">Jenis pupuk belum dipilih!</div>
                    </div>
                  </div>
                  <div class="col-md-12 col-lg-6">
                    <div class="form-group" id="grLuas" style="margin-top: 25px;">
                      <label class="form-label">Luas Area</label>
                      <input type="text" style="text-transform: uppercase;" class="form-control" id="luas" disabled>
                    </div>
                    <div class="form-group" id="grLuasDiminta"">
                      <label class="form-label">Luas aplikasi pupuk</label>
                      <input type="text" style="text-transform: uppercase;" class="form-control" id="luas_aplikasi">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 col-lg-6">
                    <div><button type="button" id="btnTambahTransaksi" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#dialogAddTransaksi"> + Tambahkan Permintaan</button></div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 col-lg-12">
                    <table id="tblPermintaanPupuk" class="table card-table table-vcenter text-nowrap datatable table-lg">
                      <thead>
                        <tr>
                          <th class="w-1">No.</th>
                          <th>Jenis Pupuk</th>
                          <th>Luas Aplikasi</th>
                          <th>Kuanta</th>
                          <th>Kuanta Pembulatan</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      ';
      return $container.$content_dialogAddPermintaanPupuk;
    }

  }
?>
