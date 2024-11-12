<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Include Composer autoload untuk Dompdf
require_once(APPPATH . '../vendor/autoload.php'); // Pastikan path benar mengarah ke file autoload Composer

use Dompdf\Dompdf;
use Dompdf\Options;

class Pdf extends Dompdf
{
    public function __construct()
    {
        // Set opsi DomPDF
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        
        // Inisialisasi DomPDF dengan opsi
        parent::__construct($options);
    }

    protected function ci()
    {
        return get_instance();
    }

    public function load_view($view, $data = array())
    {
        // Muat view sebagai HTML untuk diproses oleh Dompdf
        $html = $this->ci()->load->view($view, $data, TRUE);
        $this->loadHtml($html);
        
        // Set opsi kertas jika diperlukan (opsional)
        $this->setPaper('A4', 'portrait');
    }

    public function render_pdf($filename = "document.pdf", $stream = TRUE)
    {
        // Render HTML menjadi PDF
        $this->render();

        // Stream atau simpan file PDF
        if ($stream) {
            // Output PDF ke browser tanpa mendownload
            $this->stream($filename, array("Attachment" => 0));
        } else {
            // Save the PDF to a file path if needed
            file_put_contents(APPPATH . "pdfs/" . $filename, $this->output());
        }
    }
}
