<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Rdkk_add extends CI_Controller{

  public function __construct(){
    parent :: __construct();
    $this->load->model("wilayah_model");
    $this->load->model("kelompoktani_model");
    $this->load->model("masatanam_model");
    $this->load->model("varietas_model");
    $this->load->model("petani_model");
    $this->load->library('form_validation');
    $this->load->library('upload');
    $this->load->helper('url');
    $arrayPetani = array();
  }

  public function index(){
    if ($this->session->userdata('id_user') == false){
			redirect('login');
    } else {
      $data['pageTitle'] = "Pendaftaran RDKK";
      $data['content'] = $this->loadContent();
      $data['script'] = $this->loadScript();
      $data['before_script'] = $this->loadBeforeScript();
      $this->load->view('main_view', $data);
    }
  }

  public function cekModel(){
    $wilayah = $this->wilayah_model;
    echo $wilayah->getNamaKabupatenByIdDesa("120106AD");
  }

  public function getDesaByKabupaten(){
    $wilayah = $this->wilayah_model;
    echo $wilayah->getDesaByKabupaten($this->input->get("idKab"));
  }

  public function getAllKabupaten(){
    $wilayah = $this->wilayah_model;
    echo $wilayah->getAllKabupaten();
  }

  public function getKecByDesa(){
    $wilayah = $this->wilayah_model;
    echo $wilayah->getKecByDesa($this->input->get("idDesa"));
  }

  public function tambahData(){
    $kelompoktani = $this->kelompoktani_model;
    $validation = $this->form_validation;
    $validation->set_rules($kelompoktani->rules());
    if ($validation->run()){
      $kelompoktani->simpan();
    }
  }

  public function loadContent(){
    $listMasaTanam = $this->masatanam_model->getAll();
    $listVarietas = $this->varietas_model->getAll();
    $loadListVarietas = '';
    $loadListMasaTanam = '';
    foreach($listMasaTanam as $masaTanam):
      $loadListMasaTanam .= '<option value="'.$masaTanam->masa_tanam.'">'.$masaTanam->masa_tanam.'</option>';
    endforeach;
    foreach($listVarietas as $varietas):
      $loadListVarietas .= '<option value="'.$varietas->id_varietas.'">'.$varietas->nama_varietas.'</option>';
    endforeach;
    $content_header =
    '
      <div class="page">
        <div class="row">
          <form action="" method="post" class="card">
            <div class="card-header">
              <h3 class="card-title"> Data Kelompok Tani </h3>
            </div>

    ';
    $content_1 =
    '
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 col-lg-4">
                  <div class="form-group" id="grNamaKelompok">
                    <label class="form-label">Nama Kelompok</label>
                    <input type="text" class="form-control" id="namaKelompok" name="namaKelompok" placeholder="Nama Kelompok Tani">
                  </div>
                  <div class="form-group" id="grKab">
                    <label class="form-label">Kabupaten</label>
                    <select name="namaKab" id="namaKab" class="custom-control custom-select" placeholder="">
                    </select>
                  </div>
                  <div class="form-group" id="grNamaDesa">
                    <label class="form-label">Nama Desa<i id="iconLoading" style="margin-left: 10px" class="fa fa-spinner fa-spin"></i></label>
                    <select name="namaDesa" id="namaDesa" class="custom-control custom-select loading" placeholder="">
                    </select>
                  </div>
                  <div class="form-group" id="grMasaTanam">
                    <label class="form-label">Masa Tanam</label>
                    <select name="masaTanam" id="masaTanam" class="custom-control custom-select" placeholder="Pilih masa tanam">
                      <option value="">Pilih masa tanam</option>
                      '.$loadListMasaTanam.'
                    </select>
                  </div>
                  <div class="form-group" id="grVarietas">
                    <label class="form-label">Varietas</label>
                    <select name="varietas" id="varietas" class="custom-control custom-select" placeholder="Pilih varietas">
                      <option value="">Pilih varietas</option>
                      '.$loadListVarietas.'
                    </select>
                  </div>
                </div>

                <div class="col-md-6 col-lg-4">
                  <div class="form-group" id="grUploadKtp">
                    <div class="form-label">Scan Image KTP</div>
                    <div class="custom-file">
                      <input id="scanKtp" accept= ".jpeg,.jpg" type="file" class="custom-file-input" name="scanKtp">
                      <label id="lblScanKtp" class="custom-file-label">Pilih file</label>
                      <div style="display: none" class="invalid-feedback" id="fbScanKtp"></div>
                    </div>
                  </div>
                  <div class="form-group" id="grUploadKk">
                    <div class="form-label">Scan Image KK</div>
                    <div class="custom-file">
                      <input id="scanKk" type="file" class="custom-file-input" name="scanKk">
                      <label class="custom-file-label" id="lblScanKk">Pilih file</label>
                      <div style="display: none" class="invalid-feedback" id="fbScanKk"></div>
                    </div>
                  </div>
                  <div class="form-group" id="grUploadPernyataan">
                    <div class="form-label">Scan Image Surat Pernyataan</div>
                    <div class="custom-file">
                      <input id="scanSurat" type="file" class="custom-file-input" name="scanPernyataan">
                      <label class="custom-file-label" id="lblScanSurat">Pilih file</label>
                      <div style="display: none" class="invalid-feedback" id="fbScanSurat"></div>
                    </div>
                  </div>
                  <div class="form-group" id="grWarning">
                    <div class="alert alert-primary">File yang diterima berupa file .jpeg atau .jpg dengan ukuran <b>maks. 500kB</b></div>
                  </div>
                  <div class="form-group" id="grWarning">
                    <div class="alert alert-danger"><b>Perhatikan penulisan nama kelompok / petani !</b> Tidak diperkenankan menggunakan tanda baca "." (titik) untuk menyingkat nama.</div>
                  </div>
                </div>
              </div>
            </div>
    ';
    $content_2 =
    '
            <div class="card-header">
              <h3 class="card-title"> Data Petani </h3>
            </div>
            <div class="card-body">
              <div class="row" style="margin-bottom: 10px; margin-left: 0px">
                <button type="button" id="btnTambahPetani" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#dialogAddPetani"> + Tambah Petani</button>
              </div>
              <div class="row"></div>
              <div class="row">
                <div class="table-responsive col-md-6">
                  <table id="tblPetani" class="table card-table table-vcenter text-nowrap datatable table-sm">
                    <thead>
                      <tr>
                        <th class="w-1">No.</th>
                        <th>Nama Petani</th>
                        <th>Luas Areal</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody id="dataPetani">
                    </tbody>
                    <tfoot id="footerPetani">
                      <tr>
                        <th class="w-1"></th>
                        <th>Total luas</th>
                        <th></th>
                        <th></th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
    ';
    $content_footer =
    '
            <div class="card-footer text-right">
              <div class="d-flex">
                <button type="button" id="btnNext" class="btn btn-primary ml-auto" onclick="">Simpan data</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    ';
    $content_dialogAddPetani =
    '
      <div class="modal fade" id="dialogAddPetani">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Tambah data petani</h4>
              <button class="close" data-dismiss="modal" type="button"></button>
            </div>
            <div class="modal-body">
              <div class="alert alert-danger" id="errMsg">
              </div>
              <form id="formAddPetani" action="'.site_url('Rdkk_add/addPetaniTemp').'" method="POST" enctype="multipart/form-data">
                <div class="row">
                  <div class="col-md-12 col-lg-6">
                    <div class="form-group" id="grNamaPetani">
                      <label class="form-label">Nama Petani</label>
                      <input type="text" class="form-control" id="namaPetani" name="namaPetani" placeholder="Nama Petani">
                      <div class="invalid-feedback" id="fbNamaPetani">Nama petani belum diinput!</div>
                    </div>
                    <div class="form-group" id="grUploadPeta">
                      <div class="form-label">File GPX area kebun</div>
                      <div class="custom-file">
                        <input type="file" accept=".gpx" class="custom-file-input" name="fileGpxKebun" id="fileGpxKebun">
                        <label class="custom-file-label" id="lblFileGpxKebun" name="lblFileGpxKebun">Pilih file</label>
                        <div class="invalid-feedback" id="fbFileGpx"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <button type="button" id="btnSimpanPetani" class="btn btn-primary btn-block" name="submit" >Simpan data petani</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    ';
    return $content_header.$content_1.$content_2.$content_footer.$content_dialogAddPetani;
  }

  public function readGpxValue(){
    include('./assets/geoPHP/geoPHP.inc');
    $uploadedData = $_FILES['gpx']['tmp_name'];
    var_dump($uploadedData);
    $gpx = simplexml_load_file($uploadedData);
    //var_dump($gpx);
    $gpxValue = file_get_contents($uploadedData);
    $geometry = geoPHP::load($gpxValue,'gpx');
    var_dump($geometry->area());
    echo json_encode("output OK");
  }

  public function loadBeforeScript(){
    return
    '
    ';
  }

  public function loadScript(){
    return '$.getScript("'.base_url("/assets/app_js/Rdkk_add.js").'");';
  }

  public function addPetaniTemp(){
    $petani = $this->petani_model;
    //var_dump("Masuk");
    //ADD VALIDATION
    $validation = $this->form_validation;
    $validation->set_rules($petani->rules_petani());
    if ($validation->run()){

    }
  }
}
