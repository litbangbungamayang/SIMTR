<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Biaya_perawatan extends CI_Controller{
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
      $data['pageTitle'] = "Rekapitulasi Biaya Perawatan";
      $data['content'] = $this->loadContent();
      $data['script'] = $this->loadScript();
      $this->load->view('main_view', $data);
    }
  }

  public function loadScript(){
    return '$.getScript("'.base_url("/assets/app_js/Biaya_perawatan.js").'");';
  }

  public function buatPbp(){
    $post = $this->input->post();
    $tgl_awal = $post["tgl_awal"];
    $tgl_akhir = $post["tgl_akhir"];
    $tipe_dokumen = $post["tipe_dokumen"];
    $this->db->trans_begin();
    $id_dokumen =  $this->dokumen_model->simpan($tipe_dokumen);
    $this->transaksi_model->postPbp($id_dokumen, $tgl_awal, $tgl_akhir);
    if($this->db->trans_status()){
      $this->db->trans_commit();
      echo "SUCCESS";
    } else {
      echo "FAILED";
    }
  }

  public function getAllData(){
    $this->transaksi_model->getAllTransaksi();
  }

  public function getTransaksiBahanByIdKelompokNamaBahanPeriode(){
    echo $this->transaksi_model->getTransaksiBahanByIdKelompokNamaBahanPeriode();
  }

  public function getRekapBiayaPerawatan(){
    echo $this->transaksi_model->getRekapBiayaPerawatan();
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
                <table id="tblListPerawatan" class="table table-responsive table-striped table-md text-nowrap compact">
                  <thead>
                    <tr>
                      <th class="w-1">No.</th>
                      <th>Nama Kelompok</th>
                      <th>No. Kontrak</th>
                      <th>Desa</th>
                      <th>Tgl. Transaksi</th>
                      <th>Luas</th>
                      <th>Jumlah Biaya Perawatan</th>
                    </tr>
                  </thead>
                  <tbody class="">
                  </tbody>
                  <tfoot class="bg-dark">
                    <tr>
                      <th class="w-1"></th>
                      <th><font color="white" size="3">TOTAL</font></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <div class="col-12">
                <div class="text-right">
                    <button id="btnBuatPbp" type="button" style="margin-right: 30px; width: 200px;" class="btn btn-outline-primary">Buat Pengajuan Biaya</button>
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
