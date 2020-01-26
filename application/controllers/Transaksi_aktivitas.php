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

  public function loadContent($dataTransaksi){
    $nama_asisten = json_decode($this->user_model->getNamaAsistenByAfd($dataTransaksi[0]->id_afd))->nama_user;
    $nama_askep = json_decode($this->user_model->getNamaAskepByAfd($dataTransaksi[0]->id_afd))->nama_user;

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
    $container =
    '
      <style>
          @media screen
        {
          .noPrint{}
          .noScreen{display:none;}
        }

          @media print
        {
          .noPrint{display:none;}
          .noScreen{}
        }
      </style>
      <div class="page">
        <div class="container">
          <div class="card">
            <div class="card-header">
              <div class="card-options">
                <a href="Rdkk_list" class="btn btn-primary" onclick="" style="margin-right: 10px;"><i class="fe fe-corner-down-left"></i> Kembali </a>
                <a href="#" class="btn btn-primary" onclick="javascript:window.print();"><i class="fe fe-printer"></i> Cetak </a>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-6">
                  <p class="h3">Permintaan Perawatan Kebun</p>
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
                <div class="col-4 text-center border">Diterima oleh<br>'.$dataTransaksi[0]->nama_kelompok.'</div>
                <div class="col-4 text-center border pb-8">Diminta oleh<br>'.$nama_asisten.'</div>
                <div class="col-4 text-center border">Disetujui oleh<br>'.$nama_askep.'</div>
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
