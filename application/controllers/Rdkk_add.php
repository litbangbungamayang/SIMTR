<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Rdkk_add extends CI_Controller{

  public function __construct(){
    parent :: __construct();
    $this->load->model("masatanam_model");
    $this->load->model("varietas_model");
    $this->load->model("petani_model");
    $this->load->library('form_validation');
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
              <form id="formAddPetani" action="'.site_url('Rdkk_add/addPetaniTemp').'" method="POST" enctype="multipart/form-data">
                <div class="row">
                  <div class="col-md-12 col-lg-6">
                    <div class="form-group" id="grNamaPetani">
                      <label class="form-label">Nama Petani</label>
                      <input type="text" class="form-control" id="namaPetani" name="namaPetani" placeholder="Nama Petani">
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
                <button type="button" id="btnSimpanPetani" class="btn btn-primary btn-block" name="submit" >Simpan data petani</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    ';
    return $content_header.$content_1.$content_2.$content_footer.$content_dialogAddPetani;
  }

  public function loadScript(){
    $scriptContent =
    '
      var arrayPetani = [];
      var formAddPetani = $("#formAddPetani")[0];
      var objPetani = function(id_petani, id_kelompok, nama_petani, luas, id_gpx){
        var obj = {};
        obj.id_petani = id_petani;
        obj.id_kelompok = id_kelompok;
        obj.nama_petani = nama_petani;
        obj.luas = luas;
        obj.id_gpx = id_gpx;
        return obj;
      }
      function refreshData(){
        tabelPetani = $("#tblPetani").DataTable();
        tabelPetani.clear();
        tabelPetani.rows.add(arrayPetani);
        tabelPetani.draw();
        console.log(arrayPetani);
        return false;
      }
      $("#btnSimpanPetanis").on("click", function(){
        form = $("#formAddPetani");
        $.ajax({
          type: "POST",
          url: "'.site_url('Rdkk_add/addPetaniTemp/').'",
          dataType: "text",
          data: form.serialize()
        });
        return false;
      });
      $("#btnSimpanPetani").on("click", function(){
        var petani = objPetani(
          null,
          null,
          $("#namaPetani").val(),
          0,
          null
        );
        arrayPetani.push(petani);
        refreshData();
        formAddPetani.reset();
        return false;
      });
      $("#tblPetani").DataTable({
        bFilter: false,
        bPaginate: false,
        bSort: false,
        bInfo: false,
        data: arrayPetani,
        columns : [
          {data: "no", render: function(data, type, row, meta){return meta.row + meta.settings._iDisplayStart + 1}},
          {data: "nama_petani"},
          {data: "luas"},
          {data: "button", render: function(data, type, row, meta){return \'<button type="button" class="btn btn-danger btn-sm" name="hapus" >Hapus</button>\'}}
        ]
      });
      $("#tblPetani").on("click", "button[name=\"hapus\"]", function(e){
        var currentRow = $(this).closest("tr");
        var currentRowData = currentRow.find("td").slice(1,2).text();
        var index = arrayPetani.findIndex(function (item) {return item.nama_petani == currentRowData});
        console.log(index);
        arrayPetani.splice(index,1);
        currentRow.remove();
        console.log(arrayPetani);
        refreshData();
      });

    ';
    return $scriptContent;
  }

  public function addPetaniTemp(){
    $petani = $this->petani_model;
    //ADD VALIDATION
    $addedData = $this->input->post();
    $petani->nama_petani = $addedData["namaPetani"];
    $arrayPetani = $this->arrayPetani;
    $coba = $this->coba;
    $coba ++;
    $arrayPetani[] = $petani;
    var_dump(json_encode($arrayPetani));
    var_dump($coba);
  }
}
