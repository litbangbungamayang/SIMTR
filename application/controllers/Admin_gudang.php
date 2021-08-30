<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_gudang extends CI_Controller{
  public function __construct(){
    parent :: __construct();
    $this->load->model("vendor_model");
    $this->load->model("gudang_model");
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
      $data['pageTitle'] = "Pengelolaan Gudang Material";
      $data['content'] = $this->loadContent();
      $data['script'] = $this->loadScript();
      $this->load->view('main_view', $data);
    }
  }

  public function loadScript(){
    return '$.getScript("'.base_url("/assets/app_js/Admin_gudang.js").'");';
  }

  public function getAllGudang(){
    echo $this->gudang_model->getAllGudang();
  }

  public function getGudangById(){
      echo $this->gudang_model->getGudangById();
  }

  public function editGudang(){
    $this->gudang_model->edit();
  }

  public function addGudang(){
    $this->gudang_model->simpan();
  }

  public function hapusGudang(){
    $id_gudang = $this->input->post("id_gudang");
    $transaksi = json_decode($this->transaksi_model->getTransaksiByIdGudang($id_gudang));
    if (sizeof($transaksi) == 0){
      if ($this->gudang_model->hapus($id_gudang)) echo "Data gudang berhasil dihapus!";
    } else {
      echo "Terdapat transaksi dengan gudang yang akan dihapus. Proses menghapus dibatalkan.";
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
                <button type="button" id="btnTambahGudang" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#dialogAddGudang"> + Tambah Gudang Material</button>
              </div>
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="tblGudang" class="table card-table table-vcenter text-nowrap datatable table-sm">
                    <thead>
                      <tr>
                        <th class="w-1">No.</th>
                        <th>Nama Gudang</th>
                        <th>Lokasi</th>
                        <th>Status</th>
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
    $content_dialogAddGudang =
    '
      <div class="modal fade" id="dialogAddGudang">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Data Gudang Material</h4>
              <button class="close" data-dismiss="modal" type="button"></button>
            </div>
            <div class="modal-body">
              <form id="formAddGudang">
                <div class="row mb-6">
                  <div class="col-md-12 col-lg-6">
                    <div class="form-group" id="grNamaGudang">
                      <label class="form-label">Nama Gudang</label>
                      <input type="text" style="text-transform: uppercase;" class="form-control" id="nama_gudang" name="nama_gudang" placeholder="Nama Gudang">
                      <div class="invalid-feedback" id="fbNamaVendor">Nama gudang belum diinput!</div>
                    </div>
                    <div class="form-group" id="grLokasiGudang">
                      <label class="form-label">Lokasi</label>
                      <input type="text" style="text-transform: uppercase;" class="form-control" id="lokasi_gudang" name="lokasi_gudang" placeholder="Lokasi">
                      <div class="invalid-feedback" id="fbNamaVendor">Lokasi gudang belum diinput!</div>
                    </div>
                  </div>
                  <div class="col-md-12 col-lg-6">
                    <div class="form-group" id="grDeskripsi">
                      <label class="form-label">Deskripsi</label>
                      <input type="text" style="text-transform: uppercase;" class="form-control" id="deskripsi" name="deskripsi" placeholder="Deskripsi">
                      <div class="invalid-feedback" id="fbAlamatVendor"></div>
                    </div>
                    <div class="form-group" id="grStatus">
                      <label class="form-label">Status gudang</label>
                      <select class="form-select" name="status" id="status_gudang" placeholder="Pilih status gudang">
                        <option value="">Pilih status gudang</option>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak aktif</option>
                      </select>
                      <div class="invalid-feedback" id="fbNamaKontak">Status gudang belum dipilih!</div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <button type="button" id="btnSimpanGudang" class="btn btn-primary btn-block" name="" >Simpan data gudang</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    ';
    return $content_header.$content_1.$content_footer.$content_dialogAddGudang;
  }

}
