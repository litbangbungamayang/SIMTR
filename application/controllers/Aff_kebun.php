<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Aff_kebun extends CI_Controller{

  public function __construct(){
    parent:: __construct();
    if ($this->session->userdata('id_user') == false) redirect('login');
    $this->load->model("kelompoktani_model");
    $this->load->model("biayatma_model");
    $this->load->model("transaksitma_model");
    $this->load->model("transaksi_model");
    $this->load->model("bahan_model");
    $this->load->model("dokumen_model");
    $this->load->model("user_model");
    $this->load->helper('url');
    $this->load->helper('form');
    $this->load->helper('html');
    $this->load->helper('file');
    $this->simpg_address_live = "http://simpgbuma.ptpn7.com/index.php/api_bcn/";
    $this->simpg_address_local = "http://localhost/simpg/index.php/api_bcn/";
    $this->server_env = "LIVE";
  }

  public function index(){
    $kelompoktani = $this->kelompoktani_model;
    $dataKelompok = json_decode($kelompoktani->getKelompokById());
    $data['pageTitle'] = "";
    $data['content'] = $this->loadContent($dataKelompok);
    $data['script'] = $this->loadScript();
    $this->load->view('main_view', $data);
  }

  public function konfirmasi(){
    //var_dump($this->input->post());die();
    $kelompoktani = $this->kelompoktani_model;
    $dataKelompok = json_decode($kelompoktani->getKelompokById());
    //var_dump($dataKelompok); die();
    $dataSimpg = json_decode($kelompoktani->getDataPetakSimpg());
    $tebuBakar = json_decode($kelompoktani->getDataTebuBakar());
    //var_dump($tebuBakar); die();
    $data['pageTitle'] = "Konfirmasi Selesai Tebang";
    //$data['content'] = $this->loadKonfirmasi($dataKelompok);
    $dataContent =  array(
      "dataKelompok" => $dataKelompok,
      "dataSimpg" => $dataSimpg,
      "tebuBakar" => $tebuBakar
    );
    $data['content'] = $this->loadKonfirmasi($dataContent);
    $this->load->view('main_view', $data);
  }

  function loadScript(){
    return '$.getScript("'.base_url("/assets/app_js/Biaya_tma.js").'");';
  }

  function getCurl($request){
    $db_server = $request["db_server"];
    $url = str_replace(" ", "", $request["url"]);
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $db_server.$url,
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
    curl_close($curl);
    return $response; // output as json encoded
  }

  function getApiDataTimbangPeriodeGroup(){
    $tgl_timbang_awal = $this->input->get("tgl_timbang_awal");
    $tgl_timbang_akhir = $this->input->get("tgl_timbang_akhir");
    $tahun_giling = $this->input->get("tahun_giling");
    $id_afd = $this->session->userdata("afd");
    $db_server = "";
    if($this->server_env == "LOCAL"){
      $db_server = $this->simpg_address_local;
    } else {
      $db_server = $this->simpg_address_live;
    }
    $request = array("db_server"=>$db_server,
    "url"=>"getDataTimbangPeriodeGroup?tgl_timbang_awal=".$tgl_timbang_awal.
      "&tgl_timbang_akhir=".$tgl_timbang_akhir."&afd=".$id_afd."&tahun_giling=".$tahun_giling);
    $response = json_decode($this->getCurl($request));
    $dataResponse = [];
    if (sizeof($response) > 0){
      //DATA PER SPTA ---------------
      $req_spta = array("db_server"=>$db_server, "url"=>"getDataTimbangPerSpta?tgl_timbang_awal="
        .$tgl_timbang_awal."&tgl_timbang_akhir=".$tgl_timbang_akhir."&afd=".$id_afd);
      $response_spta = $this->getCurl($req_spta);
      $this->setSptaUtkProses($response_spta);
      //====================================
      $jml_tebu = 0;
      $jml_biaya = 0;
      for($i = 0; $i < sizeof($response); $i++){
        $dataKelompok = json_decode($this->kelompoktani_model->getKelompokByKodeBlok($response[$i]->kode_blok));
        $dataBiayaTma = json_decode($this->biayatma_model->getBiayaTmaByIdWilayah($dataKelompok->id_wilayah, $dataKelompok->zona));
        $dataElement = [
          "kode_blok" => $dataKelompok->kode_blok,
          "no_kontrak" => $dataKelompok->no_kontrak,
          "id_wilayah" => $dataKelompok->id_wilayah,
          "nama_wilayah" => $dataKelompok->nama_wilayah,
          "nama_kelompok" => $dataKelompok->nama_kelompok,
          "netto" => $response[$i]->netto,
          "tgl_timbang" => $response[$i]->tgl_timbang,
          "biaya" => (is_null($dataBiayaTma)) ? null : $dataBiayaTma->biaya,
          "jml_biaya" => (is_null($dataBiayaTma)) ? null : ($dataBiayaTma->biaya)*($response[$i]->netto)/1000,
          "tahun_giling" => $dataKelompok->tahun_giling
        ];
        array_push($dataResponse, $dataElement);
        $jml_tebu += $response[$i]->netto;
        $jml_biaya += (is_null($dataBiayaTma)) ? 0 : ($dataBiayaTma->biaya)*($response[$i]->netto)/1000;
      }
      $data_pbtma = array("jml_netto"=>$jml_tebu, "jml_biaya"=>$jml_biaya);
      $this->setNilaiPbtma($data_pbtma);
    }
    echo(json_encode($dataResponse));
  }

  public function loadContent($dataContent){
    $id_afd = $this->session->userdata("afd");
    $nama_asisten = json_decode($this->user_model->getNamaAsistenByAfd($id_afd))->nama_user;
    $nama_askep = json_decode($this->user_model->getNamaAskepByAfd($id_afd))->nama_user;
    setLocale(LC_TIME, 'id_ID.utf8');
    $hariIni = new DateTime();
    $hari = strftime('%A', $hariIni->getTimeStamp());
    $tanggal = strftime('%d %B %Y', $hariIni->getTimeStamp());
    $kategori = "";
    switch ($dataKelompok->kategori){
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
    <script>var id_afd = '.$id_afd.';</script>
    <div class="page">
      <div class="container">
        <div class="card">
          <div class="card-header">
            <div class="card-options">
              <a href="rdkk_all" class="btn btn-primary" onclick="" style="margin-right: 10px;"><i class="fe fe-corner-down-left"></i> Kembali </a>
              <a href="#" class="btn btn-primary" onclick="javascript:window.print();"><i class="fe fe-printer"></i> Cetak </a>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-12 text-center mb-6"><h3>BERITA ACARA SELESAI TEBANG</h3>Nomor BUMA/BA/AFF/069/2021</div>
            </div>
            <div class="row">
              <div class="col-12 mb-6">
                Pada hari ini, '.$hari.' tanggal '.$tanggal.' telah dinyatakan selesai tebang kelompok tani sebagai berikut:
              </div>
            </div>
            <div class="row mb-6" >
              <div class="col-4"><h5>
                Nama Kelompok <br>
                No Kontrak <br>
                Kategori <br>
                Luas Baku <br>
                Varietas <br>
                Masa Tanam <br>
                Taksasi Maret <br>
                Tebu Tertimbang <br>
                Rafaksi <br>
                Tebu Terhitung <br>
                Rafaksi % Tebu Tertimbang <br>
                Rafaksi % Takmar <br>
                Penalti Trash <br>
                Tebu Bakar <br>
                Mulai Tebang <br>
                Selesai Tebang <br>
              </h5></div>
              <div class="col-3"><h5>
                : '.$dataKelompok->nama_kelompok.' <br>
                : '.$dataKelompok->no_kontrak.' <br>
                : '.$kategori.' <br>
                : '.$dataKelompok->luas.' Ha <br>
                : '.$dataKelompok->nama_varietas.' <br>
                : '.$dataKelompok->mt.' <br>
                :  ton (Rata-rata ton/ha)<br>
                :  ton (Rata-rata ton/ha)<br>
                :  ton <br>
                :  ton <br>
                :  % <br>
                :  % <br>
                :  ton <br>
                :  ton <br>
                :  ...<br>
                :  ...<br>
              </h5></div>
            </div>
            <div class="row mb-6">
              <div class="col-12">
                Demikian Berita Acara ini dibuat untuk digunakan sebagaimana mestinya.
              </div>
            </div>
            <div class="row">
              <div class="col-6"></div>
              <div class="col-6 text-center">
                Bungayamang, '.$tanggal.'
              </div>
            </div>
            <div class="row">
              <div class="col-6 text-center">Mengetahui,</div>
            </div>
            <div class="row">
              <div class="col-6 text-center" style="height:120px">Asisten Manager Afd. '.$id_afd.'</div>
              <div class="col-6 text-center">Ketua Kelompok</div>
            </div>
            <div class="row">
              <div class="col-6 text-center">'.$nama_asisten.'</div>
              <div class="col-6 text-center">'.$dataKelompok->nama_kelompok.'</div>
            </div>
            <div class="row">
              <div class="col-12 text-center" style="height:120px">Menyetujui,<br>Manajer TR</div>
            </div>
            <div class="row">
              <div class="col-12 text-center" >'.$nama_askep.'</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    ';
    $loadingScreen = '';
    return $container.$loadingScreen;
  }

  public function loadKonfirmasi($dataContent){
    $id_afd = $this->session->userdata("afd");
    $dataKelompok = $dataContent['dataKelompok'];
    $dataSimpg = $dataContent['dataSimpg'][0];
    $tebuBakar = $dataContent['tebuBakar'][0];
    $kode_blok = "";
    $nama_asisten = json_decode($this->user_model->getNamaAsistenByAfd($id_afd))->nama_user;
    $nama_askep = json_decode($this->user_model->getNamaAskepByAfd($id_afd))->nama_user;
    setLocale(LC_TIME, 'id_ID.utf8');
    $hariIni = new DateTime();
    $hari = strftime('%A', $hariIni->getTimeStamp());
    $tanggal = strftime('%d %B %Y', $hariIni->getTimeStamp());
    $kategori = "";
    switch ($dataKelompok->kategori){
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
    <script>var id_afd = '.$id_afd.';</script>
    <div class="page">
      <div class="container">
        <div class="card">
          <div class="card-body">
            <div class="row" style="font-size:18px">
              <div class="col-lg-3">
                <div class="row"><label>Nama kelompok</label></div>
                <div class="row"><label>Nomor kontrak</label></div>
                <div class="row"><label>Kategori</label></div>
                <div class="row"><label>Luas baku</label></div>
                <div class="row"><label>Jenis tebu</label></div>
                <div class="row"><label>Masa tanam</label></div>
                <div class="row"><label>Taksasi Maret</label></div>
                <div class="row"><label>Berat tebu ditimbang</label></div>
                <div class="row"><label>Rafaksi tebu bakar</label></div>
              </div>
              <div class="col-lg-4">
                <div class="row">
                  <div>
                    <label for="nama_kelompok">:'.$dataKelompok->nama_kelompok.'</label><input class="ml-2 mb-1" type="checkbox"
                      id="nama_kelompok" style="vertical-align:middle"/>
                  </div>
                </div>
                <div class="row">
                  <div>
                    <label for="no_kontrak">:'.$dataKelompok->no_kontrak.'</label><input class="ml-2 mb-1" type="checkbox"
                      id="no_kontrak" style="vertical-align:middle"/>
                  </div>
                </div>
                <div class="row">
                  <div>
                    <label for="kategori">:'.$kategori.'</label><input class="ml-2 mb-1" type="checkbox"
                      id="kategori" style="vertical-align:middle"/>
                  </div>
                </div>
                <div class="row">
                  <div>
                    <label for="luas">:'.$dataKelompok->luas.' Ha</label><input class="ml-2 mb-1" type="checkbox"
                      id="luas" style="vertical-align:middle"/>
                  </div>
                </div>
                <div class="row">
                  <div>
                    <label for="varietas">:'.$dataKelompok->nama_varietas.'</label><input class="ml-2 mb-1" type="checkbox"
                      id="varietas" style="vertical-align:middle"/>
                  </div>
                </div>
                <div class="row">
                  <div>
                    <label for="masa_tanam">:'.$dataKelompok->mt.'</label><input class="ml-2 mb-1" type="checkbox"
                      id="masa_tanam" style="vertical-align:middle"/>
                  </div>
                </div>
                <div class="row">
                  <div>
                    <label for="ton_takmar">:'.($dataSimpg->taksasi_pandang*$dataSimpg->luas_tanam).' ton; Produktivitas :
                      '.$dataSimpg->taksasi_pandang.' ton/ha</label><input class="ml-2 mb-1" type="checkbox"
                      id="ton_takmar" style="vertical-align:middle"/>
                  </div>
                </div>
                <div class="row">
                  <div>
                    <label for="ton_timbang">:'.($dataSimpg->ton_tebu/1000).' ton; Produktivitas :
                      '.(($dataSimpg->ton_tebu/1000)/$dataSimpg->luas_tanam).' ton/ha</label><input class="ml-2 mb-1" type="checkbox"
                      id="ton_takmar" style="vertical-align:middle"/>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-options mt-6">
              <a href="rdkk_all" class="btn btn-primary" onclick="" style="margin-right: 10px;"><i class="fe fe-check-circle"></i> Validasi dan Buat Berita Acara </a>
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
