<?php

namespace App\Command;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-excel',
    description: 'Genera un archivo de prueba en Excel.'
)]
class TestExcelCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('🛠 Generando archivo Excel de prueba...');

        try {
            // Crear un nuevo archivo Excel
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'Es un mensaje de Prueba RivasDev');

            // Guardar en la carpeta "build"
            $filePath = 'build/test.xlsx';
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);

            $io->success("✅ Archivo Excel creado correctamente en: $filePath");
        } catch (\Exception $e) {
            $io->error('❌ Error al generar el archivo Excel: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}

