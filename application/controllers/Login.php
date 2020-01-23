<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

  public function __construct(){
    parent::__construct();
    $this->load->model("user_model");
    $this->load->model("afd_model");
    $this->load->library('form_validation');
    $this->load->helper('url');
  }

  public function index(){
    $this->load->view('login_view');
  }

  public function logging_in(){
    $user = $this->user_model;
    $afd = $this->afd_model;
    $validation = $this->form_validation;
    $validation->set_rules($user->login_rules());
    if ($validation->run()){
      $uname = $this->input->post('uname');
      $pwd = md5($this->input->post('pwd'));
      $salted = $uname.$pwd;
      $hashedPwd = hash('sha256', $salted);
      $loggedUser = $user->login($uname,$hashedPwd);
      if ($loggedUser !== NULL){
        $nama_afd = $afd->getAfdByIdUser($loggedUser->id_user)->nama_afd;
        $id_afd = $this->afd_model->getAfdByIdUser($loggedUser->id_user)->id_afd;
        $this->session->set_userdata((array) $loggedUser);
        $this->session->set_userdata('afd',$nama_afd);
        $this->session->set_userdata('id_afd', $id_afd);
      } else {

      }
      redirect('/');
    } else {
      $this->load->view('login_view');
    }
  }

}
