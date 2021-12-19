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
      $this->load->model("wilayah_model");
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

    public function view(){
      if ($this->session->userdata('id_user') == false){
  			redirect('login');
      } else {
        $request = array(
          "status" => "view",
          "id_kelompok" => $this->input->get("id_kelompok")
        );
        $data['pageTitle'] = "Hasil Survey Kelayakan";
        $data['content'] = $this->loadContent($request);
        $data['script'] = $this->loadScript();
        $this->load->view('main_view', $data);
      }
    }

    public function viewBa(){
      if ($this->session->userdata('id_user') == false){
  			redirect('login');
      } else {
        //var_dump($this->input->get()); die();
        $data['pageTitle'] = "";
        $data['content'] = $this->viewBaSkk();
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

    public function update(){
      echo $this->kelompoktani_model->updateSkk();
      //var_dump($this->input->post());
    }

    function loadScript(){
      return '$.getScript("'.base_url("/assets/app_js/List_skk.js").'");';
    }

    function loadContent($request = array()){
      $priv_level = $this->session->userdata("jabatan");
      $kelompoktani = json_decode($this->kelompoktani_model->getKelompokById());
      //Area input asisten
      $toggle_area_asisten = "";
      $display_file = "";
      $v_tgl_survey = "";
      $v_txt_keterangan = "";
      //------------------
      //Area input GM
      $v_txt_gm = "";
      $display_gm = "display:none";
      $toggle_txt_gm = "";
      //------------------
      $display_button_ok = "";
      $display_button_reject = "";
      $display_label_rdkk = "display:none";
      $display_link_scan = "display:none";
      $thumbnail = "";
      $id_dokumen = "";
      $set_status_ok = 2;
      $set_status_reject = 4;
      $label_rdkk = "";
      $toggle_btn_print = "display:none";
      $id_print = "";
      $tag_color = "white";
      $status_rdkk = null;
      if(sizeof($request) > 0){
        $status = $request["status"];
        if($status == "view"){
          //Data assignment
          $dataSkk = json_decode($this->kelompoktani_model->viewSkk($request["id_kelompok"]));
          $id_print = $dataSkk[0]->id_kelompok;
          $v_txt_keterangan = $dataSkk[0]->keterangan_survey;
          $v_txt_gm = $dataSkk[0]->catatan_gm;
          $v_tgl_survey = $dataSkk[0]->tgl_survey;
          $status_rdkk = $dataSkk[0]->status;
          $id_dokumen = $dataSkk[0]->id_dokumen;
          //-----------------------------
          $display_file = "display:none";
          $toggle_area_asisten = "disabled";
          $display_link_scan = "";
          $thumbnail = $dataSkk[0]->scan_skk;
          //-----------------------------
          if($priv_level == "Asisten Bagian"){
            if($status_rdkk == 2 || $status_rdkk == 4){
              $display_button_ok = "display:none";
              $display_button_reject = "display:none";
              $display_gm = "display:none";
              $display_label_rdkk = "";
              if($status_rdkk == 2){
                $tag_color = "green";
                $label_rdkk = "SKK -> Areal Layak -> Menunggu validasi GM";
              } else {
                $tag_color = "red";
                $label_rdkk = "SKK -> Areal tidak layak -> Menunggu validasi GM";
              }
            } else {
              if($status_rdkk == 3 || $status_rdkk == 5){
                $display_button_ok = "display:none";
                $display_button_reject = "display:none";
                $display_gm = "";
                $toggle_txt_gm = "disabled";
                $toggle_btn_print = "";
              }
            }
          }
          if($priv_level == "GM"){
            if($status_rdkk == 2 || $status_rdkk == 4){
              $display_gm = "";
              $display_button_ok = "";
              $display_button_reject = "";
              $set_status_ok = 3;
              $set_status_reject = 5;
              $display_label_rdkk = "";
              if($status_rdkk == 2){
                $tag_color = "green";
                $label_rdkk = "SKK -> Areal Layak -> Menunggu validasi GM";
              } else {
                $tag_color = "red";
                $label_rdkk = "SKK -> Areal tidak layak -> Menunggu validasi GM";
                $display_button_ok = "display:none";
              }
            } else {
              if($status_rdkk == 3 || $status_rdkk == 5){
                $toggle_txt_gm = "disabled";
                $display_gm = "";
                $display_label_rdkk = "";
                $toggle_btn_print = "";
                $display_button_ok = "display:none";
                $display_button_reject = "display:none";
                if($status_rdkk == 3){
                  $tag_color = "green";
                  $label_rdkk = "Areal layak";
                } else {
                  $tag_color = "red";
                  $label_rdkk = "Areal tidak layak";
                }
              }
            }
          }
        }
      }
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
            <div class="card-header">
              <div class="card-options" style="'.$toggle_btn_print.'">
                <a href="'.site_url().'\Resume_skk\viewBa?id_kelompok='.$id_print.'" class="btn btn-primary"><i class="fe fe-printer"></i> Cetak </a>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-2">
                  <div class="row mb-2">Nama kelompok</div>
                  <div class="row mb-2">Kategori</div>
                  <div class="row mb-2">Luas terdaftar</div>
                  <div class="row mb-2">Jenis tebu</div>
                  <div class="row mb-2">Masa tanam</div>
                  <div class="row mb-2 mt-4">Tanggal survey</div>
                  <div class="row mb-2 mt-3" style="height: 170px">Resume survey</div>
                  <div class="row mb-2" style="'.$display_file.'">Scan dokumen</div>
                </div>
                <div class="col-7">
                  <div class="row mb-2" >: '.$kelompoktani->nama_kelompok.'</div>
                  <div class="row mb-2">: '.$kategori.'</div>
                  <div class="row mb-2">: '.$kelompoktani->luas.' ha</div>
                  <div class="row mb-2">: '.$kelompoktani->nama_varietas.'</div>
                  <div class="row mb-2">: '.$kelompoktani->mt.'</div>
                  <div class="row mb-2"><input autocomplete="off" type="text" class="form-control text-center" placeholder="Tanggal survey" id="tgl_survey"
                    name="tgl_survey" style="width: 120px; margin-left: 0px;" '.$toggle_area_asisten.' value="'.$v_tgl_survey.'"></div>
                  <div class="row mb-2"><textarea style="resize: none" class="form-control" rows="6" id="keterangan_survey" name="keterangan_survey" '.$toggle_area_asisten.'>'.$v_txt_keterangan.'</textarea></div>
                  <div class="row mb-2" style="'.$toggle_area_asisten.'">
                    <div class="custom-file" style="'.$display_file.'">
                      <input id="scanSurat" type="file" accept=".jpeg,.jpg" class="custom-file-input '
                        .(form_error('scanSurat') != NULL ? "is-invalid" : "").'" name="scanSurat">
                      <label class="custom-file-label" id="lblScanSurat">Pilih file</label>
                      <div style="" class="invalid-feedback" id="fbScanSurat">'.form_error('scanSurat').'</div>
                    </div>
                  </div>
                </div>
              </div>
              <input type="hidden" name="id_dokumen" id="id_dokumen" value="'.$id_dokumen.'">
              <div class="row" id="review_gm" style="'.$display_gm.'">
                <div class="col-2"><div class="row mb-2" style="height: 170px">Review GM</div></div>
                <div class="col-7"><div class="row mb-2"><textarea style="resize: none" class="form-control" rows="6" id="txt_review_gm" name="review_gm" '.$toggle_txt_gm.'>'.$v_txt_gm.'</textarea></div></div>
              </div>
              <div class="row" style="'.$display_link_scan.'">
                <div class="col-2"><div class="row mb-2">Scan Dokumen</div></div>
                <div class="col-7"><div class="row mb-2"><a class="btn btn-primary btn-sm" href="'.site_url().'\View_dokumen?id_kelompok='.$kelompoktani->id_kelompok.'">Lihat dokumen</a></div></div>
              </div>
              <div class="row mb-8" style="'.$display_label_rdkk.'">
                <div class="col-2"><div class="row mb-2">Status RDKK</div></div>
                <div class="col-7"><div class="row mb-2"><span class="tag tag-'.$tag_color.'">'.$label_rdkk.'</span></div></div>
              </div>
              <div class="row" style="">
                <div style="'.$display_button_ok.'"><a  href="#" class="btn btn-green" onclick="setRdkk('.$kelompoktani->id_kelompok.','.$set_status_ok.')" style="margin-right: 10px;"><i class="fe fe-check-circle"></i> Layak </a></div>
                <div style="'.$display_button_reject.'"><a href="#" class="btn btn-danger" onclick="setRdkk('.$kelompoktani->id_kelompok.','.$set_status_reject.')" style="margin-right: 10px;"><i class="fe fe-alert-circle"></i> Tidak Layak</a></div>
                <div><a href="#" onclick="history.go(-1)" class="btn btn-primary" style="margin-right: 10px;"><i class="fe fe-chevrons-left"></i> Kembali</a></div>

              </div>
            </div>
          </div>
        </div>
        </form>
      </div>
      ';
      return $container;
      //history.go(-1)
    }

    function viewBaSkk($request = array()){
      setLocale(LC_TIME, 'id_ID.utf8');
      $dataSkk = json_decode($this->kelompoktani_model->viewSkk($this->input->get("id_kelompok")))[0];
      $dataKelompok = json_decode($this->kelompoktani_model->getKelompokById());
      $namaKabupaten = json_decode($this->wilayah_model->getNamaKabupatenByIdDesa($dataKelompok->id_wilayah))[0];
      $nama_asisten = json_decode($this->user_model->getNamaAsistenByAfd($dataKelompok->id_afd))->nama_user;
      $nama_gm = json_decode($this->user_model->getNamaGm());
      $kelayakan = "";
      $dataKelompok->status == 3 ? $kelayakan = "LAYAK" : $kelayakan = "TIDAK LAYAK";
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
      $tahun_ba = date('Y',strtotime($dataSkk->tgl_survey));
      $tglbln_ba = strtotime($dataSkk->tgl_buat);
      $hari_ba = strftime('%A',$tglbln_ba);
      $tanggal_ba = strftime('%d',$tglbln_ba);
      $bulan_ba = strftime('%B',$tglbln_ba);
      $tgl_survey_label = strftime('%d %B %Y', $tglbln_ba);
      $container =
      '
      <div class="page">
        <div class="row">
          <div class="card" style="font-size:18px">
            <div class="card-header">
              <div class="card-options">
                <a href="#" class="btn btn-primary" onclick="history.go(-1)" style="margin-right: 10px;"><i class="fe fe-corner-down-left"></i> Kembali </a>
                <a href="#" class="btn btn-primary" onclick="javascript:window.print();"><i class="fe fe-printer"></i> Cetak </a>
              </div>
            </div>
            <div class="card-body">
              <div class="row" style="height:150px">
                <div class="col-12 text-center mb-6"><h3>BERITA ACARA<br>SURVEY KELAYAKAN KEMITRAAN</h3>Nomor BUMA/BA/SKK/'.
                $dataSkk->id_dokumen.'/'.$tahun_ba.'</div>
              </div>
              <div class="row mb-6">
                <div class="col-10">
                  <div class="row ml-4">
                    Pada hari ini, '.$hari_ba.', tanggal '.$tanggal_ba.' bulan '.$bulan_ba.' tahun '.$tahun_ba.' telah selesai dilakukan
                    survey kelayakan kemitraan atas nama:
                  </div>
                </div>
              </div>
              <div class="row mb-6 ml-4">
                <div class="col-2">
                  <div class="row"><div>Ketua kelompok</div></div>
                  <div class="row"><div>Luas terdaftar</div></div>
                  <div class="row"><div>Lokasi desa</div></div>
                  <div class="row"><div>Keterangan survey</div></div>
                </div>
                <div class="col-8">
                  <div class="row"><div>: '.$dataKelompok->nama_kelompok.'</div></div>
                  <div class="row"><div>: '.$dataKelompok->luas.' ha</div></div>
                  <div class="row"><div>: '.$dataKelompok->nama_wilayah.' Kabupaten '.$namaKabupaten->nama_wilayah.'</div></div>
                  <div class="row"><div>'.nl2br($dataSkk->keterangan_survey).'</div></div>
                </div>
              </div>
              <div class="row">
                <div class="col-10">
                  <div class="row ml-4">
                    <p>
                      Selanjutnya menyatakan bahwa kelompok tersebut diatas <strong>'.$kelayakan.'</strong> mengikuti program
                      kemitraan Tebu Rakyat di PT Buma Cima Nusantara Unit Bungamayang.
                    </p><br>
                    <p>
                      Demikian Berita Acara ini dibuat dengan sebenarnya, untuk digunakan sebagaimana mestinya.
                    </p>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-10 text-right">Bungamayang, '.$tgl_survey_label.'</div>
              </div>
              <div class="row" style="height:190px">
                <div class="col-7 text-center">TIM SURVEY</div>
                <div class="col-3 text-center">Asisten Afdeling '.$dataKelompok->id_afd.'</div>
              </div>
              <div class="row">
                <div class="col-7">
                  <div class="row">
                    <div class="col-4 text-center">Manager QA</div>
                    <div class="col-4 text-center">Manager TR</div>
                    <div class="col-4 text-center">Asisten TS</div>
                  </div>
                </div>
                <div class="col-3 text-center">'.$nama_asisten.'</div>
              </div>
              <div class="row mt-4" style="height:170px">
                <div class="col-10 text-center">General Manager</div>
              </div>
              <div class="row">
                <div class="col-10 text-center">'.$nama_gm->nama_user.'</div>
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
