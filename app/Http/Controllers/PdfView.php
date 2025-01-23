<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PdfView extends Controller
{
    private function convertToPdf($filePath, $type)
    {
        ob_start(); // Commencer à capturer la sortie

        // Configuration pour Dompdf
        $rendererLibraryPath = base_path('vendor/dompdf/dompdf');
        \PhpOffice\PhpWord\Settings::setPdfRenderer(\PhpOffice\PhpWord\Settings::PDF_RENDERER_DOMPDF, $rendererLibraryPath);

        if (in_array($type, ['doc', 'docx'])) {
            // Conversion Word -> PDF
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
            $writer->save('php://output');
        } elseif (in_array($type, ['xls', 'xlsx'])) {
            // Conversion Excel -> PDF
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Dompdf');
            $writer->save('php://output');
        } elseif (in_array($type, ['csv'])) {
            // Conversion CSV -> PDF
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Dompdf');
            $writer->save('php://output');
        }

        return ob_get_clean();
    }

    public function index($id)
    {
        $document = Document::findOrFail($id);
        $filePath = storage_path('app/public/' . $document->filename);
        $fileType = $document->type;

        // Vérifiez si le type de fichier nécessite une conversion
        if (in_array($document->type, ['doc', 'docx', 'xls', 'xlsx', 'csv'])) {

            $pdfContent = $this->convertToPdf($filePath, $fileType);

            // Vous pouvez stocker le fichier PDF généré dans un dossier temporaire pour l'afficher
            $tempPdfPath = storage_path('app/public/preview_' . $document->id . '.pdf');
            file_put_contents($tempPdfPath, $pdfContent);

            return view('PDF.pdfview', compact('document', 'tempPdfPath'));
        }

        return view('PDF.pdfview', compact('document'));
    }
}
