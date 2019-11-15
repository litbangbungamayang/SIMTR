<?php defined('BASEPATH') OR exit('No direct script access allowed');


  class Rdkk_list extends CI_Controller{

    public function __construct(){
      parent:: __construct();
    }

    public function index(){
      if ($this->session->userdata('id_user') == false){
  			redirect('login');
      } else {
        $data['pageTitle'] = "Penelusuran RDKK";
        $data['content'] = $this->loadContent();
        $data['script'] = "";
        $this->load->view('main_view', $data);
      }
    }

    function loadContent(){
      $container =
      '
      <div class="page">
        <div class="row">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"> Data RDKK </h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="table-responsive col-md-12">
                  <table id="tblPetani" class="table card-table table-vcenter text-nowrap datatable table-sm">
                    <thead>
                      <tr>
                        <th class="w-1">No.</th>
                        <th>Nama Kelompok</th>
                        <th>No. Kontrak</th>
                        <th>Desa</th>
                        <th>MT</th>
                        <th>Luas Total</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody id="dataPetani">
                    </tbody>
                  </table>
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
