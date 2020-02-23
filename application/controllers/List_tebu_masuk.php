<?php defined('BASEPATH') OR exit('No direct script access allowed');


class List_tebu_masuk extends CI_Controller{

  public function __construct(){
    parent:: __construct();
    if ($this->session->userdata('id_user') == false) redirect('login');
    $this->load->model("kelompoktani_model");
    $this->load->model("biayatma_model");
    $this->load->model("bahan_model");
    $this->load->helper('url');
    $this->load->helper('form');
    $this->load->helper('html');
    $this->load->helper('file');
  }

  public function index(){
    $data['pageTitle'] = "Rincian Tebu Masuk (SIMPG)";
    $data['content'] = $this->loadContent();
    $data['script'] = $this->loadScript();
    $this->load->view('main_view', $data);
  }

  function loadScript(){
    return '$.getScript("'.base_url("/assets/app_js/List_tebu_masuk.js").'");';
  }

  function getApiDataTimbangPeriodeGroup(){
    $tgl_timbang_awal = $this->input->get("tgl_timbang_awal");
    $tgl_timbang_akhir = $this->input->get("tgl_timbang_akhir");
    $id_afd = $this->session->userdata("afd");
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "http://localhost/simpg/index.php/api_buma/getDataTimbangPeriodeGroup?tgl_timbang_awal=".$tgl_timbang_awal."&tgl_timbang_akhir=".$tgl_timbang_akhir."&afd=".$id_afd,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache"
      ),
    ));
    $response = curl_exec($curl);
    $error = curl_error($curl);
    $response = json_decode($response);
    $dataResponse = [];
    curl_close($curl);
    for($i = 0; $i < sizeof($response); $i++){
      $dataKelompok = json_decode($this->kelompoktani_model->getKelompokByKodeBlok($response[$i]->kode_blok));
      $dataBiayaTma = json_decode($this->biayatma_model->getBiayaTmaByIdWilayah($dataKelompok->id_wilayah));
      $dataElement = [
        "kode_blok" => $dataKelompok->kode_blok,
        "no_kontrak" => $dataKelompok->no_kontrak,
        "id_wilayah" => $dataKelompok->id_wilayah,
        "nama_wilayah" => $dataKelompok->nama_wilayah,
        "nama_kelompok" => $dataKelompok->nama_kelompok,
        "netto" => $response[$i]->netto,
        "tgl_timbang" => $response[$i]->tgl_timbang,
        "biaya" => (is_null($dataBiayaTma)) ? null : $dataBiayaTma->biaya
      ];
      array_push($dataResponse, $dataElement);
    }
    print_r(json_encode($dataResponse));
  }

  public function loadContent(){
    $id_afd = $this->session->userdata("afd");
    $container =
    '
    <script>var id_afd = '.$id_afd.';</script>
    <div class="page">
      <div class="row">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="table-responsive col-12">
                <table id="tblTebuMasukSkrg" class="table card-table table-vcenter text-nowrap datatable table-sm compact" style="width: 100%;">
                  <thead>
                    <tr>
                      <th class="w-1">No.</th>
                      <th>Kode Blok</th>
                      <th>No. Kontrak</th>
                      <th>Nama Kelompok</th>
                      <th>Wilayah</th>
                      <th>Netto</th>
                      <th>Tgl. Timbang</th>
                      <th>Biaya TMA</th>
                    </tr>
                  </thead>
                </table>
              </div>
              <div class="col-12">
                <div class="text-right">
                    <button id="btnTransferTma" type="button" style="margin-right: 30px; width: 200px;" class="btn btn-outline-primary">Transfer data SIMPG</button>
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
