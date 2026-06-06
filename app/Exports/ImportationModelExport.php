<?php

namespace App\Exports;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Writer\XLSX\Writer;

class ImportationModelExport
{
    public function download($filename = 'modelo_importacao.xlsx')
    {
        $writer = WriterEntityFactory::createXLSXWriter();
        $filePath = storage_path('app/public/' . $filename);

        $writer->openToFile($filePath);

        $headers = [
            'full_name',
            'cpf',
            'email',
            'phone',
            'date_of_birth',
            'group',
            'gender',
        ];

        $headerRow = WriterEntityFactory::createRowFromArray($headers);
        $writer->addRow($headerRow);

        $exampleRow = WriterEntityFactory::createRowFromArray(
            [
                'Teste',
                '000.000.000-00',
                'teste@teste.com',
                '00000000000',
                '01-01-1990',
                'adult',
                'M',
            ],
        );

        $writer->addRow($exampleRow);
        $writer->close();

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}