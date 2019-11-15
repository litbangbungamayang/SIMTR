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

  public function getNamaDesa(){
    $wilayah = $this->wilayah_model;
    echo $wilayah->getAllDesa();
  }

  public function getNamaKabupaten(){
    $wilayah = $this->wilayah_model;
    if ($this->input->get('idDesa') !== NULL){
      $idDesa = $this->input->get('idDesa');
      echo $wilayah->getNamaKabupatenByIdDesa($idDesa);
    }
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
                  <div class="form-group" id="grNamaDesa">
                    <label class="form-label">Nama Desa</label>
                    <select name="namaDesa" id="namaDesa" class="custom-control custom-select" placeholder="">
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
                        <input type="file" accept=".gpx" class="custom-file-input" name="fileGpxKebun" id="fileGpxKebun">
                        <label class="custom-file-label" id="lblFileGpxKebun" name="lblFileGpxKebun">Pilih file</label>
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
    $scriptContent =
    '
      $.ajax({
        url: "Rdkk_add/getNamaDesa",
        type: "GET",
        dataType: "json",
        success: function(response){
          $("#namaDesa").selectize({
            valueField: "id_wilayah",
            labelField: "nama_wilayah",
            sortField: "nama_wilayah",
            searchField: "nama_wilayah",
            maxItems: 1,
            create: false,
            placeholder: "Pilih nama desa",
            options: response,
            render: {
              option: function (item, escape){
                var namaKab = $.ajax({
                  url: "Rdkk_add/getNamaKabupaten",
                  type: "GET",
                  data: "idDesa=" + escape(item.id_wilayah),
                  success: function(data){
                  }
                });
                return "<option value = escape(item.id_wilayah)>" + escape(item.nama_wilayah) + namaKab.nama_wilayah + "</option>";
              }
            },
            onBlur: function(){
              console.log($(this)[0].getValue());
            }
          });
        }
      });

      function readOpenLayers(gpxFile){
        var reader = new FileReader();
        reader.readAsText(gpxFile, "UTF-8");
        reader.onload = function (evt){
          var gpxFormat = new ol.format.GPX();
          var gpxFeatures = gpxFormat.readFeature(evt.target.result, {
            dataProjection: "EPSG:4326",
            featureProjection: "EPSG:4326"
          });
          var sourceProjection = gpxFormat.readProjection(evt.target.result);
          //console.log("Source proj. = " + sourceProjection.getCode());
          var geom = gpxFeatures.getGeometry();
          var poly = new ol.geom.Polygon(geom.getCoordinates());
          //console.log("Geom type = " + geom.getType());
          //console.log("Length = " + ol.sphere.getLength(geom));
          //console.log("Area = " + ol.sphere.getArea(geom));
          //console.log("Coordinates = " + geom.getCoordinates());
          //console.log("Poly Area = " + poly.getArea(poly)*1000000 + " Ha.");
          //console.log("Sphere Area = " + ol.sphere.getArea(poly, {projection: "EPSG:4326"})/10000 + " Ha.");
          //console.log("Poly Length = " + ol.sphere.getLength(poly, {projection: "EPSG:4326"}) + " m.");
          var luasLahan =  ol.sphere.getArea(poly, {
            projection: "EPSG:4326"
          });
          var petani = objPetani(
            null,
            null,
            $("#namaPetani").val(),
            luasLahan/10000,
            geom.getCoordinates()
          );
          $("#lblFileGpxKebun").text("Pilih file");
          $("#fileGpxKebun").val("");
          arrayPetani.push(petani);
          refreshData();
          console.log(arrayPetani);
          formAddPetani.reset();
        }
      }

      $("#dialogAddPetani").on("hide.bs.modal", function (e){
        $("#lblFileGpxKebun").text("Pilih file");
        $("#fileGpxKebun").val("");
      })

      var arrayPetani = [];
      var formAddPetani = $("#formAddPetani")[0];
      var objPetani = function(id_petani, id_kelompok, nama_petani, luas, arrayGPS){
        var obj = {};
        obj.id_petani = id_petani;
        obj.id_kelompok = id_kelompok;
        obj.nama_petani = nama_petani;
        obj.luas = luas;
        obj.gps = arrayGPS;
        return obj;
      }

      function refreshData(){
        tabelPetani = $("#tblPetani").DataTable();
        tabelPetani.clear();
        tabelPetani.rows.add(arrayPetani);
        tabelPetani.draw();
        return false;
      }

      $("#fileGpxKebun").change(function(e){
        var selectedFile = $(this)[0].files[0];
        var lblGpxKebun = $("#lblFileGpxKebun");
        lblGpxKebun.text(selectedFile.name);
        if (selectedFile.type != "application/gpx+xml"){
          alert("Invalid format!");
          lblGpxKebun.text("Pilih file");
          $("#fileGpxKebun").val("");
        }
      })

      $("#btnSimpanPetani").on("click", function(){
        if ($("#fileGpxKebun").val() != ""){
          var selectedFile = $("#fileGpxKebun")[0].files[0];
          readOpenLayers(selectedFile);
        }
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
          {data: "luas",
            render: function(data, type, row, meta){
              return data.toLocaleString(undefined, {maximumFractionDigits:2}) + " Ha"
            },
            className: "text-right"
          },
          {data: "button", render: function(data, type, row, meta){return \'<button type="button" class="btn btn-danger btn-sm" name="hapus" >Hapus</button>\'}}
        ],
        "footerCallback": function (row, data, start, end, display){
            var api = this.api(), data;
            var getIntVal = function (i){
              return typeof i === \'string\' ? i.replace(/Ha/g,\'\')*1 : typeof i === \'number\' ? i : 0;
            };
            total = api.column(2).data().reduce(function (a,b){
              return getIntVal(a) + getIntVal(b);
            },0);
            $(api.column(2).footer()).html(total.toLocaleString(undefined, {maximumFractionDigits: 2}) + " Ha");
        }
      });

      $("#tblPetani").on("click", "button[name=\"hapus\"]", function(e){
        var currentRow = $(this).closest("tr");
        var currentRowData = currentRow.find("td").slice(1,2).text();
        var index = arrayPetani.findIndex(function (item) {return item.nama_petani == currentRowData});
        console.log(index);
        arrayPetani.splice(index,1);
        currentRow.remove();
        //console.log(arrayPetani);
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
