<?php defined('BASEPATH') OR exit('No direct script access allowed');


  class Resume_skk extends CI_Controller{

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
        $data['pageTitle'] = "Hasil Survey Kelayakan";
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

    public function simpan(){
      $tipe_dokumen = "SKK";
      $id_dokumen = $this->dokumen_model->simpan($tipe_dokumen, "-");
      if(!is_null($id_dokumen)){
        $dataSkk = ($this->input->post());
        $request = array(
          "dataSkk" => $dataSkk,
          "id_dokumen" => $id_dokumen
        );
      }
      echo $this->kelompoktani_model->simpanSkk($request);
    }

    function loadScript(){
      return '$.getScript("'.base_url("/assets/app_js/List_skk.js").'");';
    }

    function loadContent(){
      $priv_level = $this->session->userdata("jabatan");
      $kelompoktani = json_decode($this->kelompoktani_model->getKelompokById());
      $kategori = "";
      switch ($kelompoktani->kategori){
        case 1 :
          $kategori = "PC";
          break;
        case 2 :
          $kategori = "RT 1";
          break;
        case 3 :
          $kategori = "RT 2";
          break;
        case 4 :
          $kategori = "RT 3";
          break;
      }
      $container =
      '
      <div class="page">
        <form action="" id="frmSurvey">
        <div class="row">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-2">
                  <div class="row mb-2">Nama kelompok</div>
                  <div class="row mb-2">Kategori</div>
                  <div class="row mb-2">Luas terdaftar</div>
                  <div class="row mb-2">Jenis tebu</div>
                  <div class="row mb-2">Masa tanam</div>
                  <div class="row mb-2 mt-4">Tanggal survey</div>
                  <div class="row mb-2" style="height: 170px">Resume survey</div>
                  <div class="row mb-2">Scan dokumen</div>
                </div>
                <div class="col-7">
                  <div class="row mb-2" >: '.$kelompoktani->nama_kelompok.'</div>
                  <div class="row mb-2">: '.$kategori.'</div>
                  <div class="row mb-2">: '.$kelompoktani->luas.' ha</div>
                  <div class="row mb-2">: '.$kelompoktani->nama_varietas.'</div>
                  <div class="row mb-2">: '.$kelompoktani->mt.'</div>
                  <div class="row mb-2"><input autocomplete="off" type="text" class="form-control text-center" placeholder="Tanggal survey" id="tgl_survey" name="tgl_survey" style="width: 120px; margin-left: 0px;"></div>
                  <div class="row mb-2"><textarea style="resize: none" class="form-control" rows="6" id="keterangan_survey" name="keterangan_survey"></textarea></div>
                  <div class="row mb-8">
                    <div class="custom-file">
                      <input id="scanSurat" type="file" accept=".jpeg,.jpg" class="custom-file-input '.(form_error('scanSurat') != NULL ? "is-invalid" : "").'" name="scanSurat">
                      <label class="custom-file-label" id="lblScanSurat">Pilih file</label>
                      <div style="" class="invalid-feedback" id="fbScanSurat">'.form_error('scanSurat').'</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row" id="review_gm" style="'.($priv_level == 'GM' ? '' : 'display:none').'">
                <div class="col-2"><div class="row mb-2" style="height: 170px">Review GM</div></div>
                <div class="col-7"><div class="row mb-2"><textarea style="resize: none" class="form-control" rows="6" id="txt_review_gm" name="review_gm"></textarea></div></div>
              </div>
              <div class="row">
                <a href="#" class="btn btn-primary" onclick="setRdkk('.$kelompoktani->id_kelompok.','."2".')" style="margin-right: 10px;"><i class="fe fe-check-circle"></i> Setujui RDKK</a>
                <a href="#" class="btn btn-danger" onclick="setRdkk('.$kelompoktani->id_kelompok.','."4".')" style="margin-right: 10px;"><i class="fe fe-delete"></i> Batalkan RDKK</a>
              </div>
            </div>
          </div>
        </div>
        </form>
      </div>
      ';
      return $container;
    }
  }
?>
