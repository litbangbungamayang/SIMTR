<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *
 */
class Dokumen_model extends CI_Model{

  private $_table = "tbl_dokumen";
  public $id_dokumen;
  public $no_dokumen;
  public $tipe_dokumen;
  public $id_bagian;
  public $id_subbagian;
  public $tgl_dokumen;
  public $kode_rekening;
  public $id_user;
  public $tgl_buat;
  public $tgl_validasi_bagian;
  public $tgl_validasi_tuk;
  public $tbl_validasi_gm;
  public $tgl_terima_tuk;
  public $tgl_terima_gm;
  public $tgl_reject_tuk;
  public $tgl_reject_gm;
  public $keterangan;

  public function rules(){
    return [
      [
        'field' => 'no_dokumen',
        'label' => 'no_dokumen',
        'rules' => 'required'
      ],
      [
        'field' => 'keterangan',
        'label' => 'keterangan',
        'rules' => 'required'
      ],
      [
        'field' => 'kode_rekening',
        'label' => 'kode_rekening',
        'rules' => 'required'
      ]
    ];
  }

  public function getAll(){
    return $this->db->get($this->_table)->result();
  }

  public function getById($id_dokumen){
    return $this->db->get_where($this->_table, ["id_dokumen" => $id_dokumen])->row();
  }

  public function getByBagian($id_bagian){
    return $this->db->get_where($this->_table, ["id_bagian" => $id_bagian])->row();
  }

  public function getBySubbagian($id_subbagian){
    return $this->db->get_where($this->_table, ["id_subbagian" => $id_subbagian])->row();
  }

  public function simpan(){
    $post = $this->input->post();
    $this->no_dokumen = $post["no_dokumen"];
    $this->tipe_dokumen = $post["tipe_dokumen"];
    $this->id_bagian = $post["id_bagian"];
    $this->id_subbagian = $post["id_subbagian"];
    $this->tgl_dokumen = $post["tgl_dokumen"];
    $this->kode_rekening = $post["kode_rekening"];
    $this->id_user = $post["id_user"];
    $this->tgl_buat = date('Y-m-d H:i:s');
    $this->keterangan = $post["keterangan"];
    $this->db->insert($this->_table, $this);
    return $this->db->insert_id();
  }

  public function update(){
    $post = $this->input->post();
    $this->id_dokumen = $post["id_dokumen"];
    $this->tgl_validasi_bagian = $post["tgl_validasi_bagian"];
    $this->tgl_validasi_tuk = $post["tgl_validasi_tuk"];
    $this->tgl_validasi_gm = $post["tgl_validasi_gm"];
    $this->tgl_terima_tuk = $post["tgl_terima_tuk"];
    $this->tgl_terima_gm = $post["tgl_terima_gm"];
    $this->db->update($this->_table, $this, array('id_dokumen' => $post['id_dokumen']));
  }

  public function hapus($id_dokumen){
    return $this->db->delete($this->_table, array('id_dokumen' => $post['id_dokumen']));
  }

}
