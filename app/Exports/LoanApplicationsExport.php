<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class LoanApplicationsExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles, WithEvents
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        return $this->data;
    }

    // Define the headings
    public function headings(): array
    {
        return [
            'ID',
            'Loan Date',
            'Client',
            'Product',
            'Amount',
            'Score',
            'Status',
            'Created At',
            'Created By',
        ];
    }

    // Apply styles to the Excel file
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);
    }

     // Lock the sheet
     public function registerEvents(): array
     {
         return [
             AfterSheet::class => function(AfterSheet $event) {
                 $sheet = $event->sheet->getDelegate();
                 $sheet->getProtection()->setSheet(true);
                 $sheet->getProtection()->setPassword('1234');
             },
         ];
     }
}
