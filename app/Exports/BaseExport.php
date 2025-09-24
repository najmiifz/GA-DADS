<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class BaseExport implements WithHeadings, WithStyles, WithEvents
{
    // Child classes must implement headings() and collection() or array() depending on use
    abstract public function headings(): array;

    // Default styles: header bold, align center-left
    public function styles(Worksheet $sheet)
    {
        // Header row style (row 1)
        $sheet->getStyle('1')->getFont()->setBold(true);
        $sheet->getStyle('1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                // Freeze the header row
                $sheet->freezePane('A2');
                // Enable auto-filter on header row
                $highestCol = $sheet->getHighestColumn();
                $sheet->setAutoFilter("A1:" . $highestCol . "1");
                // Auto-size columns
                foreach (range('A', $sheet->getHighestColumn()) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                // Add thin borders around used range
                $highestRow = $sheet->getHighestRow();
                $highestCol = $sheet->getHighestColumn();
                $range = 'A1:' . $highestCol . $highestRow;
                $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                // Header background fill
                $sheet->getStyle('A1:' . $highestCol . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFEFEFEF');
            }
        ];
    }
}
