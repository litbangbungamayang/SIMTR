<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transaksi_aktivitas extends CI_Controller{
  public function __construct(){
    parent :: __construct();
    if ($this->session->userdata('id_user') == false) redirect('login');
    $this->load->model("bahan_model");
    $this->load->model("transaksi_model");
    $this->load->model("aktivitas_model");
    $this->load->model("user_model");
    $this->load->helper('url');
    $this->load->helper('form');
    $this->load->helper('html');
    $this->load->library('ciqrcode');
  }

  public function index(){
    $no_transaksi = $this->input->get("no_transaksi");
    $id_kelompok = $this->input->get("id_kelompok");
    $dataTransaksi = json_decode($this->transaksi_model->getTransaksiAktivitasByNoTransaksi($no_transaksi, $id_kelompok));
    date_default_timezone_set('Asia/Jakarta');
    $data['pageTitle'] = "";
    $data['content'] = $this->loadContent($dataTransaksi);
    $this->load->view('main_view', $data);
  }

  public function getTransaksiAktivitasByIdKelompok(){
    echo $this->transaksi_model->getTransaksiAktivitasByIdKelompok();
  }

  public function generateQr($data){
    $params['data'] = $data;
    $params['level'] = 'H'; //H=High
    $params['size'] = 1;
    ob_start();
    $this->ciqrcode->generate($params);
    $qrcode = ob_get_contents();
    ob_end_clean();
    return $qrcode;
  }

  public function loadContent($dataTransaksi){
    $nama_asisten = json_decode($this->user_model->getNamaAsistenByAfd($dataTransaksi[0]->id_afd))->nama_user;
    $nama_askep = json_decode($this->user_model->getNamaAskepByAfd($dataTransaksi[0]->id_afd))->nama_user;
    $qrAsisten = '';
    $qrAskep = '';
    $opsiCetak = '';
    if(!is_null($dataTransaksi[0]->tgl_validasi_bagian)){
      $dataQr = site_url().'/Verifikasi?id_dokumen='.$dataTransaksi[0]->id_ppk.'&tgl_validasi_bagian='.$dataTransaksi[0]->tgl_validasi_bagian;
      $qrAsisten = $this->generateQr($dataQr);
      $qrAsisten = '<img src="data:image/png;base64,'.base64_encode($qrAsisten).'" />';
    }
    if(!is_null($dataTransaksi[0]->tgl_validasi_kasubbag)){
      $dataQr = site_url().'/Verifikasi?id_dokumen='.$dataTransaksi[0]->id_ppk.'&tgl_validasi_kasubbag='.$dataTransaksi[0]->tgl_validasi_kasubbag;
      $qrAskep = $this->generateQr($dataQr);
      $qrAskep = '<img src="data:image/png;base64,'.base64_encode($qrAskep).'" />';
    }
    $contentAktivitas = "";
    $nomor = 1;
    $jmlBiaya = 0;
    foreach($dataTransaksi as $aktivitas){
      $contentAktivitas .= '<tr><td style="text-align: center;">'.$nomor.'</td><td>'.$aktivitas->nama_aktivitas.'</td><td style="text-align: right;">'.
        number_format($aktivitas->kuanta,2,".",",").'</td><td style="text-align: right;">Rp '.
        number_format($aktivitas->biaya,0,".",",").'</td><td style="text-align: right;">Rp '.
        number_format($aktivitas->rupiah,0,".",",").'</td></tr>';
      $jmlBiaya = $jmlBiaya + $aktivitas->rupiah;
      $nomor ++;
    }
    $contentAktivitas = $contentAktivitas.'<tr><td style="text-align: center;"></td><td>JUMLAH</td><td style="text-align: right;"></td><td style="text-align: right;"></td>
      <td style="text-align: right;">Rp '.number_format($jmlBiaya,0,".",",").'</td></tr>';
    if($dataTransaksi[0]->jenis_aktivitas == "PERAWATAN"){
      $judul = "Permintaan Perawatan Kebun";
    } else {
      $judul = "Permintaan Bibit";
    }
    $container =
    '
      <div class="page">
        <div class="container">
          <div class="card">
            <div class="card-header">
              <div class="card-options">
                <a href="#" class="btn btn-primary" onclick="javascript:history.back();" style="margin-right: 10px;"><i class="fe fe-corner-down-left"></i> Kembali </a>
                '.$opsiCetak.'
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-6">
                  <p class="h3">'.$judul.'</p>
                  <p>Kelompok <b>'.$dataTransaksi[0]->nama_kelompok.' / '.$dataTransaksi[0]->no_kontrak.'</b><br>
                  Kategori '.$dataTransaksi[0]->kategori.'<br>Luas '.$dataTransaksi[0]->luas.' Ha / Desa '.$dataTransaksi[0]->nama_wilayah.'</p>
                </div>
                <div class="col-6 text-right">
                  <br><br><br>
                  No. Transaksi <strong>'.$dataTransaksi[0]->no_transaksi.'</strong><br>
                  Tgl. Transaksi <strong>'.date_format(date_create($dataTransaksi[0]->tgl_transaksi), "d-m-Y H:i:s").'</strong>
                </div>
              </div>
              <div class="table-responsive push">
                <table class="table table-bordered">
                  <tr>
                    <th class="text-center" style="width: 1%"></th>
                    <th>Uraian</th>
                    <th class="text-center" style="width: 20%">Luas diajukan (Ha.)</th>
                    <th class="text-right" style="width: 20%">Harga per Ha.</th>
                    <th class="text-right" style="width: 20%">Jumlah</th>
                  </tr>
                  '.$contentAktivitas.'
                </table>
              </div>
              <div class="row">
                <div class="col-4 text-center border" style="height: 120px">Diterima oleh<br>'.$dataTransaksi[0]->nama_kelompok.'</div>
                <div class="col-4 text-center border pb-5">Diminta oleh<br>'.$nama_asisten.'<br>'.$qrAsisten.'</div>
                <div class="col-4 text-center border">Disetujui oleh<br>'.$nama_askep.'<br>'.$qrAskep.'</div>
              <div>
              <div class="row px-3">
                <small>'.date("dmY-His").'</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    ';
    return $container;
  }
}
