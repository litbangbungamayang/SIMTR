<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_koordinator extends CI_Controller{
  public function __construct(){
    parent :: __construct();
    //if ($this->session->userdata('id_user') == false) redirect('login');
    $this->load->model("koordinator_model");
    $this->load->model("kelompoktani_model");
    $this->load->model("transaksi_model");
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
      if ($this->session->flashdata('error_message') == ''){
        $this->session->set_flashdata('error_div', 'display: none');
      }
      if ($this->session->flashdata('notif_msg') == ''){
        $this->session->set_flashdata('notif_div', 'display: none');
      }
      $data['pageTitle'] = "Administrasi Koordinator Kelompok";
      $data['content'] = $this->loadContent();
      $data['script'] = $this->loadScript();
      $this->load->view('main_view', $data);
    }
  }

  public function loadScript(){
    return '$.getScript("'.base_url("/assets/app_js/Admin_koordinator.js").'");';
  }

  public function getAllKoordinator(){
    echo $this->koordinator_model->getAllKoordinator();
  }

  public function getKoordByTahunGiling(){
    echo $this->koordinator_model->getKoordByTahunGiling();
  }

  public function addKoordinator(){
    echo $this->koordinator_model->simpan();
  }

  public function getKoordById(){
    echo $this->koordinator_model->getKoordById();
  }

  public function getKelompokByIdKoord(){
    echo $this->kelompoktani_model->getKelompokByIdKoord();
  }

  public function editKoordinator(){
      if($this->koordinator_model->edit()){
        echo "Data berhasil diubah!";
      } else {
        echo "Gagal mengubah data!";
      }
  }

  public function hapus(){
    if(is_null(json_decode($this->kelompoktani_model->getKelompokByIdKoord()))){
      if($this->koordinator_model->hapus() == 1){
        echo "Data berhasil dihapus!";
      } else {
        echo "Gagal menghapus data!";
      }
    } else {
      echo "Terdapat Kelompok Tani dengan data koordinator tersebut. Penghapusan data tidak dapat dilakukan.";
    }
  }

  public function hapusBahan(){
    $id_bahan = $this->input->post("id_bahan");
    $transaksi = json_decode($this->transaksi_model->getTransaksiByIdBahan($id_bahan));
    //var_dump(sizeof($transaksi));
    if (sizeof($transaksi) == 0){
      if ($this->bahan_model->hapus($id_bahan)) echo "Data bahan berhasil dihapus!";
    } else {
      echo "Terdapat transaksi dengan nama bahan yang akan dihapus. Proses menghapus dihentikan.";
    }
  }

  public function loadContent(){
    $content_header =
    '
      <div class="page">
        <div class="row">
          <div class="card">
            <div style="'.$this->session->flashdata('error_div').'" class="card-body">
                <div class="alert alert-danger">'.$this->session->flashdata('error_message').'</div>
            </div>
            <div class="alert alert-success alert-dismissible" style="'.$this->session->flashdata('notif_div').'">'.$this->session->flashdata('notif_msg').'
              <a href="#" class="close" data-dismiss="alert" aria-label="close"></a>
            </div>
    ';
    $content_1 =
    '
            <div class="card-body">
              <div class="row" style="margin-bottom: 10px; margin-left: 0px">
                <button type="button" id="btnTambahKoord" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#dialogAddKoord"> + Tambah Data Koordinator</button>
              </div>
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="tblKoordinator" class="table card-table table-vcenter text-nowrap datatable table-sm">
                    <thead>
                      <tr>
                        <th class="w-1">No.</th>
                        <th>Tahun Giling</th>
                        <th>Nama Koordinator</th>
                        <th>No. Telepon</th>
                        <th class="text-center">Aksi</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
    ';
    $content_footer =
    '
          </div>
        </div>
      </div>
    ';
    //2020 s.d. 2025
    //-1 s.d. +4
    $currYear = strval(date("Y"));
    $optionText = "";
    for ($x = -1; $x <= 4; $x++){
      $optionText .= '<option value="'.($currYear + $x).'">'.($currYear + $x).'</option>';
    }
    $content_dialogAddBahan =
    '
      <div class="modal fade" id="dialogAddKoord">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Data Koordinator</h4>
              <button class="close" data-dismiss="modal" type="button"></button>
            </div>
            <div class="modal-body">
              <form action="" id="formAddKoord">
                <div class="row">
                  <div class="col-md-12 col-lg-6">
                    <div class="form-group" id="grTahunGiling">
                      <label class="form-label">Tahun Giling</label>
                      <select name="tahun_giling" id="tahun_giling" class="custom-control custom-select" placeholder="Pilih tahun giling">
                      '.$optionText.'
                      </select>
                      <div class="invalid-feedback">Tahun giling belum dipilih!</div>
                    </div>
                    <div class="form-group" id="grNama">
                      <label class="form-label">Nama Koordinator</label>
                      <input type="text" style="text-transform: uppercase;" class="form-control" id="nama_koordinator" name="nama_koordinator" placeholder="Nama Koordinator">
                      <div class="invalid-feedback" id="fbPotKarung">Nama koordinator harus diisi!</div>
                    </div>
                  </div>
                  <div class="col-md-12 col-lg-6">
                    <div class="form-group" id="grNoKtp">
                      <label class="form-label">Nomor KTP</label>
                      <input type="text" style="text-transform: uppercase;" class="form-control" id="no_ktp" name="no_ktp" placeholder="Nomor KTP">
                      <div class="invalid-feedback" id="fbPotKarung">Nomor KTP harus diisi!</div>
                    </div>
                    <div class="form-group" id="grTelp">
                      <label class="form-label">Nomor Telepon</label>
                      <input type="text" style="text-transform: uppercase;" class="form-control" id="nomor_telepon" name="nomor_telepon" placeholder="Nomor Telepon">
                      <div class="invalid-feedback" id="fbPotKarung">Nomor telepon harus diisi!</div>
                    </div>
                    <div class="form-group" id="grUploadKtp">
                      <div class="form-label">Scan Image KTP</div>
                      <div class="custom-file">
                        <input id="scanKtp" accept= ".jpeg,.jpg" type="file" class="custom-file-input '.(form_error('scanKtp') != NULL ? "is-invalid" : "").'" name="scanKtp">
                        <label id="lblScanKtp" class="custom-file-label">Pilih file</label>
                        <div style="" class="invalid-feedback" id="fbScanKtp">Scan KTP belum ada!</div>
                      </div>
                    </div>
                  </div>
                </div>
                <button type="button" id="btnSimpanKoord" class="btn btn-primary btn-block" name="" >Simpan data koordinator</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    ';
    return $content_header.$content_1.$content_footer.$content_dialogAddBahan;
  }
}
