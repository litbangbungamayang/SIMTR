<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transaksi_bahan extends CI_Controller{
  public function __construct(){
    parent :: __construct();
    $this->load->model("bahan_model");
    $this->load->model("persediaan_model");
    $this->load->library('form_validation');
    $this->load->library('upload');
    $this->load->helper('url');
    $this->load->helper('form');
    $this->load->helper('html');
  }

  public function index(){
    if ($this->session->userdata('id_user') == false){
			redirect('login');
    } else {
      if ($this->session->flashdata('error_message') == ''){
        $this->session->set_flashdata('error_div', 'display: none');
      }
      if ($this->session->flashdata('notif_msg') == ''){
        $this->session->set_flashdata('notif_div', 'display: none');
      }
      $data['pageTitle'] = "Transaksi Bahan";
      //$data['content'] = $this->loadContent();
      //$data['script'] = $this->loadScript();
      $this->load->view('main_view', $data);
    }
  }

  public function loadScript(){
    return '$.getScript("'.base_url("/assets/app_js/Transaksi_bahan.js").'");';
  }

}
