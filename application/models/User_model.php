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

}
