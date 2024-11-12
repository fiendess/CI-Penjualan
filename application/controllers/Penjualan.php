<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Penjualan extends CI_Controller {
public function __construct(){
parent::__construct();


function penjualan()
{
$data['dataPenjualan'] = $this->m_penjualan->getPenjualanPetugas()->result();
$this->load->view('petugas/header');
$this->load->view('petugas/dataPenjualan',$data);
$this->load->view('petugas/footer');
}
}
function dataPenjualan()
{
if (!$this->session->userdata('level')=='Admin') {
redirect('login');
}else{
$data['admin'] = $this->m_user->selectAdmin()->row();
$data['dataPenjualan'] = $this->m_penjualan->getPenjualan()->result();


$this->load->view('admin/header',$data);
$this->load->view('admin/dataPenjualan');
$this->load->view('admin/footer');
}
}

function hapus($idPenjualan){
if (!$this->session->userdata('level')=='Admin') {
redirect('login');
}else{
$this->m_penjualan->hapus($idPenjualan);
$this->session->set_flashdata('info', 'SUKSESS : Berhasil di Hapus');
redirect('penjualan/dataPenjualan');
}
}

public function export() {
        if ($this->session->userdata('level') != 'Admin') {
            redirect('login');
        } else {
            // Panggil class PHPExcel
            $excel = new PHPExcel();

            // Settingan awal file excel
            $excel->getProperties()->setCreator('XYZ')
                ->setLastModifiedBy('XYZ')
                ->setTitle("Data Penjualan")
                ->setSubject("Penjualan")
                ->setDescription("Laporan Semua Data Penjualan")
                ->setKeywords("Data Penjualan");

            // Buat style untuk header tabel
            $style_col = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'E1E0F7')
                ),
                'font' => array('bold' => true),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'borders' => array(
                    'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );

            // Buat style untuk isi tabel
            $style_row = array(
                'alignment' => array(
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                ),
                'borders' => array(
                    'outline' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );

            // Judul tabel
            $excel->setActiveSheetIndex(0)->setCellValue('A1', "Data Penjualan");
            $excel->getActiveSheet()->mergeCells('A1:H1');
            $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE);
            $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15);
            $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $excel->setActiveSheetIndex(0)->setCellValue('A3', "Tanggal Cetak : " . date("d F Y"));

            // Header tabel
            $excel->setActiveSheetIndex(0)->setCellValue('A4', "NO");
            $excel->setActiveSheetIndex(0)->setCellValue('B4', "Id Penjualan");
            $excel->setActiveSheetIndex(0)->setCellValue('C4', "Nama Barang");
            $excel->setActiveSheetIndex(0)->setCellValue('D4', "Harga");
            $excel->setActiveSheetIndex(0)->setCellValue('E4', "Tgl Transaksi");
            $excel->setActiveSheetIndex(0)->setCellValue('F4', "QTY");
            $excel->setActiveSheetIndex(0)->setCellValue('G4', "Total");
            $excel->setActiveSheetIndex(0)->setCellValue('H4', "Petugas");

            // Apply style untuk header
            $excel->getActiveSheet()->getStyle('A4:H4')->applyFromArray($style_col);

            // Panggil data penjualan dari model
            $dataPenjualan = $this->m_penjualan->getPenjualan()->result();
            $no = 1; // Untuk penomoran tabel
            $numrow = 5; // Baris pertama untuk isi tabel

            foreach ($dataPenjualan as $data) {
                $excel->setActiveSheetIndex(0)->setCellValue('A' . $numrow, $no);
                $excel->setActiveSheetIndex(0)->setCellValue('B' . $numrow, $data->idPenjualan);
                $excel->setActiveSheetIndex(0)->setCellValue('C' . $numrow, $data->namaBarang);
                $excel->setActiveSheetIndex(0)->setCellValue('D' . $numrow, 'Rp' . $data->harga);
                $excel->setActiveSheetIndex(0)->setCellValue('E' . $numrow, date('d F Y', strtotime($data->tglTransaksi)));
                $excel->setActiveSheetIndex(0)->setCellValue('F' . $numrow, $data->qty);
                $excel->setActiveSheetIndex(0)->setCellValue('G' . $numrow, 'Rp ' . number_format($data->harga * $data->qty, 0, ',', '.'));
                $excel->setActiveSheetIndex(0)->setCellValue('H' . $numrow, $data->nama);

                // Apply style untuk isi tabel
                $excel->getActiveSheet()->getStyle('A' . $numrow . ':H' . $numrow)->applyFromArray($style_row);

                $no++;
                $numrow++;
            }

            // Set height semua kolom menjadi auto
            $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
            // Set orientasi kertas jadi LANDSCAPE
            $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
            // Set judul file excel
            $excel->getActiveSheet(0)->setTitle("Laporan Data Penjualan");
            $excel->setActiveSheetIndex(0);

            // Proses file excel untuk diunduh
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="Data Penjualan.xlsx"');
            header('Cache-Control: max-age=0');
            $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $write->save('php://output');
        }
    }
}

function exportPDF(){
$data['dataPenjualan'] = $this->m_penjualan->getPenjualan()->result();
$this->pdf->load_view('admin/laporan/laporanPenjualan',$data);


$tgl = date("d/m/Y");
$this->pdf->render();
$this->pdf->stream("Laporan-Penjualan_".$tgl.".pdf");
}
?>