<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

  private $_table = "tbl_user";
  public $id_user;
  public $jabatan;
  public $nama_user;
  public $uname;
  public $pwd;

  public function login_rules(){
    return [
      [
        'field'=>'uname',
        'label'=>'Login Name',
        'rules'=>'required',
        'errors'=> ['required'=>'Username belum diinput']
      ],
      [
        'field'=>'pwd',
        'label'=>'Password',
        'rules'=>'required',
        'errors'=> ['required'=>'Password belum diinput']
      ]
    ];
  }

  public function signup_rules(){
    return [
      [
        'field'=>'jabatan',
        'label'=>'Jabatan',
        'rules'=>'required'
      ],
      [
        'field'=>'nama_user',
        'label'=>'User Name',
        'rules'=>'required|is_unique[tbl_user.nama_user]'
      ],
      [
        'field'=>'uname',
        'label'=>'Login Name',
        'rules'=>'required|alpha_numeric|is_unique[tbl_user.uname]'
      ]
    ];
  }

  public function login($uname, $pwd){
    return $this->db->get_where($this->_table, array('uname'=>$uname, 'pwd'=>$pwd), 1, 0)->row();
  }

  public function getNamaAsistenByAfd($id_afd = null){
    (is_null($id_afd) ? $id_afd = $this->input->get("id_afd") : null);
    $query =
    "
      select
        usr.nama_user
      from tbl_afd afd
      join tbl_user usr on afd.id_user = usr.id_user
      where afd.nama_afd = ? and usr.jabatan = ?
    ";
    return json_encode($this->db->query($query, array($id_afd, "Asisten Bagian"))->row());
  }

  public function getNamaAskepByAfd($id_afd = null){
    (is_null($id_afd) ? $id_afd = $this->input->get("id_afd") : null);
    $query =
    "
      select distinct
        usr.nama_user
      from tbl_user usr
      join tbl_subbagian subb on subb.id_kepala_subbagian = usr.id_user
      join tbl_afd afd on afd.id_subbagian = subb.id_subbagian
      where afd.nama_afd = ? and usr.jabatan = ?
    ";
    return json_encode($this->db->query($query, array($id_afd, "Kepala Sub Bagian"))->row());
  }

  public function getNamaGm(){
    return json_encode($this->db->query("select * from tbl_user where jabatan=?", array("GM"))->row());
  }

}
