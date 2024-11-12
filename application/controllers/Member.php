<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class member extends CI_Controller {
public function __construct(){
parent::__construct();
$this->load->model(array('m_member','m_user'));
date_default_timezone_set('Asia/Jakarta');
}
public function dataMember()
{
if (!$this->session->userdata('level')=='Admin') {
redirect('login');
}else{
$data['admin'] = $this->m_user->selectAdmin()->row();
$data['member'] = $this->m_member->getMember()->result();
$this->load->view('admin/header',$data);
$this->load->view('admin/dataMember');
$this->load->view('admin/footer');
}
}
public function import()
{
$data['admin'] = $this->m_user->selectAdmin()-
row();
if (!$this->session->userdata('level')=='Admin') {
redirect('login');
}else{
if(isset($_POST['preview'])){ // Jika user menekan

// lakukan upload file dengan memanggil

$upload = $this->m_member->upload_file($this->filename);
if($upload['result'] == "success"){ // Jika

// Load plugin PHPExcel nya
$excelreader = new

PHPExcel_Reader_Excel2007();
$loadexcel = $excelreader->load('excel/'.$this->filename.'.xlsx'); // Load file yang tadi

$sheet = $loadexcel->getActiveSheet()->toArray(null, true, true ,true);

$data['sheet'] = $sheet;
}else{ // Jika proses upload gagal
$data['upload_error'] = $upload['error'];

$this->load->view('admin/header',$data);
$this->load->view('admin/tambahmember');
$this->load->view('admin/footer');
}
}
$data['member'] = $this->m_member->getMember()->result();
$this->load->view('admin/header',$data);
$this->load->view('admin/tambahmember');
$this->load->view('admin/footer');
}
}
public function tambah(){
$excelreader = new PHPExcel_Reader_Excel2007();
$loadexcel = $excelreader->load('excel/'.$this->filename.'.xlsx'); // Load file yang telah diupload ke

$sheet = $loadexcel->getActiveSheet()->toArray(null,
true, true ,true);
// Buat sebuah variabel array untuk menampung array
$data = [];
$numrow = 1;
foreach($sheet as $row){
// Cek $numrow apakah lebih dari 1
// Artinya karena baris pertama adalah nama-nama

if($numrow > 1){
// Kita push (add) array data ke variabel


array_push($data, [
'idMember'=>"", // Insert data id dari

'nama'=>$row['B'], // Insert data nama

'jk'=>$row['C'], // Insert data jenis

'alamat'=>$row['D'], // Insert data

]);
}
$numrow++; // Tambah 1 setiap kali looping
}
$this->m_member->tambah($data);
$this->session->set_flashdata('info', 'Data berhasil
ditambah');
redirect("member/dataMember"); 
}
}
?>