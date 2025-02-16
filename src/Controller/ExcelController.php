<?php

namespace App\Controller;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExcelController extends AbstractController
{
    #[Route('/test-excel', name: 'test_excel')]
    public function testExcel(): Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hola Mundo');

        $filePath = $this->getParameter('kernel.project_dir') . '/public/test.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return $this->json([
            'message' => 'Archivo Excel creado correctamente',
            'download' => '/test.xlsx'
        ]);
    }
}
