<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class barang extends CI_Controller {
public function __construct(){

parent::__construct();
$this->load->model(array('m_barang','m_user'));
date_default_timezone_set('Asia/Jakarta');
}
function getkodeunik() {
$q = $this->db->query("SELECT MAX(RIGHT(idBarang,2)) AS idmax FROM
barang");
$kd = ""; //kode awal
if($q->num_rows()>0){ //jika data ada
foreach($q->result() as $k){
$tmp = ((int)$k->idmax)+1; //string kode diset ke integer dan ditambahkan 1
$kd = sprintf("%02s",$tmp); //kode ambil 4 karakter terakhir
}
}else{ //jika data kosong diset ke kode awal
$kd = "01";
}
$kar = "B"; //karakter depan kodenya
//gabungkan string dengan kode yang telah dibuat tadi
return $kar.$kd;
}
function tambah()
{
if(!$this->session->userdata('level')=='Petugas') {
redirect('login');
}else{
if ($this->input->method()=='post') {
$this->m_barang->tambah();
$this->session->set_flashdata('info', 'Data berhasil ditambah');
redirect('barang/tambah');
}else{
$data['kodeunik'] = $this->m_barang->getkodeunik();
$this->load->view('petugas/header');
$this->load->view('petugas/tambahBarang',$data);
$this->load->view('petugas/footer');
}
}
}
public function barang()
{
if(!$this->session->userdata('level')=='Petugas') {
redirect('login');
}else{
$data['dataBarang'] = $this->m_barang->getBarang()->result();
$this->load->view('petugas/header');
$this->load->view('petugas/dataBarang',$data);
$this->load->view('petugas/footer');
}
}

function dataBarang()
{
if(!$this->session->userdata('level')=='Admin') {
redirect('login');
}else{
$data['admin'] = $this->m_user->selectAdmin()->row();
$data['dataBarang'] = $this->m_barang->getBarang()->result();
$this->load->view('admin/header',$data);
$this->load->view('admin/dataBarang');
$this->load->view('admin/footer');
}
}

public function ubahBarang($idBarang){
if(!$this->session->userdata('level')=='Admin') {
redirect('login');
}else{
if ($this->input->method()=='post') {
$this->m_barang->ubahBarang($idBarang);
$this->session->set_flashdata('info', 'Data berhasil diubah');
redirect('barang/barang');
}else{
$data['dataBarang'] = $this->m_barang->selectBarang($idBarang)->row();
$this->load->view('petugas/header');
$this->load->view('petugas/ubahBarang', $data);
$this->load->view('petugas/footer');
}
}
}
function ubah($idBarang){
if(!$this->session->userdata('level')=='Admin') {
redirect('login');
}else{
if ($this->input->method()=='post') {
$this->m_barang->ubah($idBarang);
$this->session->set_flashdata('info', 'Data berhasil diubah');
redirect('barang/dataBarang');
}else{
$data['admin'] = $this->m_user->selectAdmin()->row();
$data['dataBarang'] = $this->m_barang->selectBarang($idBarang)->row();
$this->load->view('admin/header', $data);
$this->load->view('admin/ubahBarang');
$this->load->view('admin/footer');
}
}
}


function tambahPenjualan(){
$data['kodeunik'] = $this->m_penjualan->getkodeunik();
$data['dataBarang'] = $this->m_barang->getBarang()->result();
if ($this->input->method()=='post') {
$this->m_penjualan->tambah();
redirect('penjualan/tambahPenjualan');
}else{
$this->load->view('petugas/header');
$this->load->view('petugas/tambahPenjualan',$data);
$this->load->view('petugas/footer');
}
}

public function stok()
{
if(!$this->session->userdata('level')=='Petugas') {
redirect('login');
}else{
$data['dataBarang'] = $this->m_barang->getBarang()->result();
$this->load->view('petugas/header');
$this->load->view('petugas/stok',$data);
$this->load->view('petugas/footer');
}
}

function export(){
if(!$this->session->userdata('level')=='Admin') {
redirect('login');
}else{
// Panggil class PHPExcel nya
$excel = new PHPExcel();
$path = $_SERVER['DOCUMENT_ROOT'] . '/assets/gambar/';
// Settingan awal fil excel
$excel->getProperties()->setCreator('XYZ')
->setLastModifiedBy('XYZ')
->setTitle("Data Barang")
->setSubject("Barang")
->setDescription("Laporan Semua Data Barang")
->setKeywords("Data Barang");
// Buat sebuah variabel untuk menampung pengaturan style dari header tabel
// Buat sebuah variabel untuk menampung pengaturan style dari header tabel
$style_col = array(
'fill' => array(
'type' => PHPExcel_Style_Fill::FILL_SOLID,
'color' => array('rgb'=>'E1E0F7'),
),

'font' => array('bold' => true), // Set font nya jadi bold
'alignment' => array(
'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 
'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER 
),
'borders' => array(
'outline' => array(
'style' => PHPExcel_Style_Border::BORDER_THIN,
),
),
);
// Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
$style_row = array(
'alignment' => array(
'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER 
),
'borders' => array(
'outline' => array(
'style' => PHPExcel_Style_Border::BORDER_THIN,
),
),
);
$excel->setActiveSheetIndex(0)->setCellValue('A1', "DATA BARANG");
$excel->getActiveSheet()->mergeCells('A1:E1'); 
$excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE); 
$excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15); 
$excel->getActiveSheet()->getStyle('A1')->getAlignment()-
setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
// Buat header tabel nya pada baris ke 3
$excel->setActiveSheetIndex(0)->setCellValue('A3', "NO"); 
$excel->setActiveSheetIndex(0)->setCellValue('B3', "Id Barang"); 
$excel->setActiveSheetIndex(0)->setCellValue('C3', "Nama Barang");
$excel->setActiveSheetIndex(0)->setCellValue('D3', "Harga"); 
$excel->setActiveSheetIndex(0)->setCellValue('E3', "Stok"); 


// Apply style header yang telah kita buat tadi ke masing-masing kolom header
$excel->getActiveSheet()->getStyle('A3')->applyFromArray($style_col);
$excel->getActiveSheet()->getStyle('B3')->applyFromArray($style_col);
$excel->getActiveSheet()->getStyle('C3')->applyFromArray($style_col);
$excel->getActiveSheet()->getStyle('D3')->applyFromArray($style_col);
$excel->getActiveSheet()->getStyle('E3')->applyFromArray($style_col);

$dataBarang = $this->m_barang->getBarang()->result();
$no = 1; // Untuk penomoran tabel, di awal set dengan 1
$numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
foreach($dataBarang as $data){ // Lakukan looping pada variabel barang
$excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $no);
$excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, $data->idBarang);
$excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, $data-
namaBarang);
$excel->setActiveSheetIndex(0)->setCellValue('D'.$numrow, 'Rp'. $data-
harga);
$excel->setActiveSheetIndex(0)->setCellValue('E'.$numrow, $data->stok);
// Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
$excel->getActiveSheet()->getStyle('A'.$numrow)-
applyFromArray($style_row);
$excel->getActiveSheet()->getStyle('B'.$numrow)-
applyFromArray($style_row);
$excel->getActiveSheet()->getStyle('C'.$numrow)-
applyFromArray($style_row);
$excel->getActiveSheet()->getStyle('D'.$numrow)-
applyFromArray($style_row);
$excel->getActiveSheet()->getStyle('E'.$numrow)-
applyFromArray($style_row);
$no++; // Tambah 1 setiap kali looping
$numrow++; // Tambah 1 setiap kali looping
}
// Set width kolom
$excel->getActiveSheet()->getColumnDimension('A')->setWidth(5); 
$excel->getActiveSheet()->getColumnDimension('B')->setWidth(15); 
$excel->getActiveSheet()->getColumnDimension('C')->setWidth(25); 
$excel->getActiveSheet()->getColumnDimension('D')->setWidth(20); 
$excel->getActiveSheet()->getColumnDimension('E')->setWidth(5); 

$excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
// Set orientasi kertas jadi LANDSCAPE
$excel->getActiveSheet()->getPageSetup()-
setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
// Set judul file excel nya
$excel->getActiveSheet(0)->setTitle("Laporan Data Barang");
$excel->setActiveSheetIndex(0);
// Proses file excel

header('Content-Type: application/vnd.openxmlformats-
officedocument.spreadsheetml.sheet');

header('Content-Disposition: attachment; filename="Data Barang.xlsx"'); 
header('Cache-Control: max-age=0');
$write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$write->save('php://output');
}
}
public function exportPDF(){
$data['dataBarang'] = $this->m_barang->getBarang()->result();
$tgl = date("Y/m/d");
$this->pdf->load_view('admin/laporan/laporanBarang',$data);
$this->pdf->render();
set_time_limit (500);
$this->pdf->stream("Laporan-Barang".$tgl.".pdf");
}
}
?>