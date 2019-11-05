<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Dokumen extends CI_Controller{

  public function __construct(){
    parent :: __construct();
    $this->load->model("dokumen_model");
    $this->load->library('form_validation');
    $this->load->helper('url');
  }

  public function index(){
    $data['pageTitle'] = "List Dokumen";
    $data['content'] = $this->loadContent();
    $this->load->view('main_view', $data);
  }

  public function readDocStatus(){
    $listDokumen = $this->dokumen_model->getAll();
  }

  public function loadContent(){
    $listDokumen = $this->dokumen_model->getAll();
    $table_header =
    '<div class="col-12">
      <div class="card">
        <div class="table-responsive">
          <table class="table card-table table-vcenter text-nowrap datatable">
            <thead>
              <tr>
                <th class="w-1">No.</th>
                <th>Jenis Dokumen</th>
                <th>No. Dokumen</th>
                <th>Keterangan</th>
                <th>Status Bagian</th>
                <th>Status TUK</th>
                <th>Status GM</th>
                <th></th>
              </tr>
            </thead>
            <tbody>';
    $no = 1;
    $table_content = '';
    foreach($listDokumen as $dokumen):
      $validasi_bagian = '';
      $validasi_tuk = '';
      $validasi_gm = '';
      if (is_null($dokumen->tgl_validasi_bagian)){
        $validasi_bagian = '<span class="badge badge-warning" style="vertical-align:top;">DRAFT</span>';
      } else {
        $validasi_bagian = '<span class="badge badge-success" style="vertical-align:top">VALID</span>';
      }
      if (!is_null($dokumen->tgl_terima_tuk)){
        $validasi_tuk = '<span class="badge badge-default" style="vertical-align:top;">DITERIMA</span>';
        if (!is_null($dokumen->tgl_validasi_tuk)){
          $validasi_tuk = '<span class="badge badge-success" style="vertical-align:top;">VALID</span>';
        }
        if (!is_null($dokumen->tgl_reject_tuk)){
          $validasi_tuk = '<span class="badge badge-danger" style="vertical-align:top;">DITOLAK</span>';
        }
      }
      if (!is_null($dokumen->tgl_terima_gm)){
        $validasi_gm = '<span class="badge badge-default" style="vertical-align:top;">DITERIMA</span>';
        if (!is_null($dokumen->tgl_validasi_gm)){
          $validasi_gm = '<span class="badge badge-success" style="vertical-align:top;">VALID</span>';
        }
        if (!is_null($dokumen->tgl_reject_gm)){
          $validasi_gm = '<span class="badge badge-danger" style="vertical-align:top;">DITOLAK</span>';
        }
      }
      $table_content .=
      '
        <tr>
          <td>'.$no.'
          <td>'.$dokumen->tipe_dokumen.'</td>
          <td>'.$dokumen->no_dokumen.'</td>
          <td>'.$dokumen->keterangan.'</td>
          <td>'.$validasi_bagian.'</td>
          <td>'.$validasi_tuk.'</td>
          <td>'.$validasi_gm.'</td>
          <td><div class="dropdown"><button class="btn btn-secondary btn-sm">Actions</button></div></td>
        </tr>
      ';
      $no ++;
    endforeach;
    $table_footer =
            '</tbody>
          </table>
          <script>
            require(["datatables", "jquery"], function(datatable, $) {
                  $(".datatable").DataTable();
                });
          </script>
        </div>
      </div>
    </div>';

    return $table_header.$table_content.$table_footer;

  }
}
