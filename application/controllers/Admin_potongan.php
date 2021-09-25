<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_potongan extends CI_Controller{
  public function __construct(){
    parent :: __construct();
    //if ($this->session->userdata('id_user') == false) redirect('login');
    $this->load->model("potongan_model");
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
      $data['pageTitle'] = "Administrasi Bahan";
      $data['content'] = $this->loadContent();
      $data['script'] = $this->loadScript();
      $this->load->view('main_view', $data);
    }
  }

  public function loadScript(){
    return '$.getScript("'.base_url("/assets/app_js/Admin_potongan.js").'");';
  }

  public function getAllPotongan(){
    echo $this->potongan_model->getAllPotongan();
  }

  public function getPotonganByTahunGiling(){
    echo $this->potongan_model->getPotonganByTahunGiling();
  }

  public function addPotongan(){
    echo $this->potongan_model->simpan();
  }

  public function getPotonganById(){
    echo $this->potongan_model->getPotonganById();
  }

  public function editPotongan(){
    if(is_null(json_decode($this->transaksi_model->getTransaksiByIdPotongan()))){
      if($this->potongan_model->edit() == 1){
        echo "Data berhasil diubah!";
      } else {
        echo "Gagal mengubah data!";
      }
    } else {
      echo "Terdapat transaksi dengan data potongan tersebut. Pengubahan tidak dapat dilakukan.";
    }
  }

  public function hapus(){
    if(is_null(json_decode($this->transaksi_model->getTransaksiByIdPotongan()))){
      if($this->potongan_model->hapus() == 1){
        echo "Data berhasil dihapus!";
      } else {
        echo "Gagal menghapus data!";
      }
    } else {
      echo "Terdapat transaksi dengan data potongan tersebut. Penghapusan data tidak dapat dilakukan.";
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
                <button type="button" id="btnTambahPotongan" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#dialogAddPotongan"> + Tambah Data Potongan</button>
              </div>
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="tblPotongan" class="table card-table table-vcenter text-nowrap datatable table-sm">
                    <thead>
                      <tr>
                        <th class="w-1">No.</th>
                        <th>Tahun Giling</th>
                        <th>Pot. Karung</th>
                        <th>Pot. Tetes</th>
                        <th>Pot. Admin</th>
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
      <div class="modal fade" id="dialogAddPotongan">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Data Potongan</h4>
              <button class="close" data-dismiss="modal" type="button"></button>
            </div>
            <div class="modal-body">
              <form id="formAddPotongan">
                <div class="row">
                  <div class="col-md-12 col-lg-6">
                    <div class="form-group" id="grTahunGiling">
                      <label class="form-label">Tahun Giling</label>
                      <select name="tahun_giling" id="tahun_giling" class="custom-control custom-select" placeholder="Pilih tahun giling">
                      '.$optionText.'
                      </select>
                      <div class="invalid-feedback">Tahun giling belum dipilih!</div>
                    </div>
                    <div class="form-group" id="grPotKarung">
                      <label class="form-label">Potongan karung (Rp/karung)</label>
                      <input type="text" style="text-transform: uppercase; text-align: right;" class="form-control" id="potongan_karung" name="potongan_karung" placeholder="Potongan karung">
                      <div class="invalid-feedback" id="fbPotKarung">Potongan karung belum dibuat!</div>
                    </div>
                    <div class="form-group" id="grPotTetes">
                      <label class="form-label">Potongan tetes (Rp/ton tetes)</label>
                      <input type="text" style="text-transform: uppercase; text-align: right;" class="form-control" id="potongan_tetes" name="potongan_tetes" placeholder="Potongan tetes">
                      <div class="invalid-feedback" id="fbPotKarung">Potongan tetes belum dibuat!</div>
                    </div>
                    <div class="form-group" id="grPotAdmin">
                      <label class="form-label">Potongan adminstrasi (Rp/ha)</label>
                      <input type="text" style="text-transform: uppercase; text-align: right;" class="form-control" id="potongan_admin" name="potongan_admin" placeholder="Potongan admin">
                      <div class="invalid-feedback" id="fbPotKarung">Potongan admin belum dibuat!</div>
                    </div>
                  </div>
                </div>
                <button type="button" id="btnSimpanPotongan" class="btn btn-primary btn-block" name="" >Simpan data potongan</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    ';
    return $content_header.$content_1.$content_footer.$content_dialogAddBahan;
  }
}
