<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Rdkk_add extends CI_Controller{

  public function __construct(){
    parent :: __construct();
    $this->load->model("masatanam_model");
    $this->load->model("varietas_model");
    $this->load->library('form_validation');
    $this->load->helper('url');
  }

  public function index(){
    if ($this->session->userdata('id_user') == false){
			redirect('login');
    } else {
      $data['pageTitle'] = "Pendaftaran RDKK";
      $data['content'] = $this->loadContent();
      $this->load->view('main_view', $data);
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
                  <div class="form-group" id="grNamaDesa">
                    <label class="form-label">Nama Desa</label>
                    <select name="namaDesa" id="namaDesa" class="custom-control custom-select" placeholder="Pilih desa">
                      <option value="">Pilih desa</option>
                      <option value="1">Isorejo</option>
                      <option value="4">Negara Tulang Bawang</option>
                      <option value="3">Tanah Abang</option>
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
                      <input type="file" class="custom-file-input" name="scanKtp">
                      <label class="custom-file-label">Pilih file</label>
                    </div>
                  </div>
                  <div class="form-group" id="grUploadKk">
                    <div class="form-label">Scan Image KK</div>
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" name="scanKk">
                      <label class="custom-file-label">Pilih file</label>
                    </div>
                  </div>
                  <div class="form-group" id="grUploadPernyataan">
                    <div class="form-label">Scan Image Surat Pernyataan</div>
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" name="scanPernyataan">
                      <label class="custom-file-label">Pilih file</label>
                    </div>
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
                  <table class="table card-table table-vcenter text-nowrap datatable table-sm">
                    <thead>
                      <tr>
                        <th class="w-1">No.</th>
                        <th>Nama Petani</th>
                        <th>Luas Areal</th>
                      </tr>
                    </thead>
                    <tbody id="dataPetani">
                    </tbody>
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
              <form id="formAddPetani">
                <div class="row">
                  <div class="col-md-12 col-lg-6">
                    <div class="form-group" id="grNamaPetani">
                      <label class="form-label">Nama Petani</label>
                      <input type="text" class="form-control" id="namaKelompok" name="namaKelompok" placeholder="Nama Petani">
                    </div>
                    <div class="form-group" id="grUploadPeta">
                      <div class="form-label">File GPX area kebun</div>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" name="fileGpxKebun">
                        <label class="custom-file-label">Pilih file</label>
                      </div>
                    </div>
                  </div>
                </div>
                <button type="button" id="btnNext" class="btn btn-primary btn-block" onclick="">Simpan data</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    ';
    return $content_header.$content_1.$content_2.$content_footer.$content_dialogAddPetani;

  }
}
