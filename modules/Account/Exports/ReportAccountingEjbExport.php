<?php

namespace Modules\Account\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ReportAccountingEjbExport extends DefaultValueBinder implements FromArray, WithHeadings, WithColumnFormatting, WithCustomValueBinder, WithStrictNullComparison, ShouldAutoSize
{
    use Exportable;

    protected $records;

    public function data($data)
    {
        $this->records = $data['records'];

        return $this;
    }

    public function headings(): array
    {
        return [
            'Ruc',
            'tipo documento',
            'serie documento',
            'numero documento',
            'fecha documento',
            'fecha vencimiento',
            'moneda',
            'importe igv',
            'importe documento',
            'importe inafecto',
            'importe ISC',
            'Otros',
            'impuesto Bolsas',
            'Cuenta de venta',
            'Fecha documento Ref',
            'Tipo documento Ref',
            'Serie documento Ref',
            'Numero documento Ref',
            'Centro Costo',
            'subdiario',
            'cuenta por Cobrar',
            'glosa de comprobante',
            'Anexo de venta',
            'Comprobante',
            'Anexo Auxiliar',
            'Dato Adicional',
            'Cuenta Pago Automatico',
            'Numero Documento Pago Automatico',
            'Importe de Pago Automatico',
        ];
    }

    public function array(): array
    {
        return $this->records->map(function ($row) {
            return [
                (string) $row['customer_number'],
                $row['document_type'],
                $row['series'],
                (string) $row['number'],
                $row['date_of_issue_excel'],
                $row['date_of_due_excel'],
                $row['currency_type_id'],
                $row['total_igv'],
                $row['total'],
                $row['total_unaffected'],
                $row['total_isc'],
                $row['others'],
                $row['total_plastic_bag_taxes'],
                $row['income_account'],
                $row['ref_date_excel'],
                $row['ref_document_type'],
                $row['ref_series'],
                (string) $row['ref_number'],
                $row['cost_center'],
                (string) $row['subdiary'],
                $row['receivable'],
                $row['gloss'],
                '',
                '',
                '',
                '',
                $row['automatic_payment_account'],
                $row['automatic_payment_document_number'],
                $row['automatic_payment_amount'],
            ];
        })->toArray();
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'R' => NumberFormat::FORMAT_TEXT,
            'T' => NumberFormat::FORMAT_TEXT,
            'U' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        if (in_array($cell->getColumn(), ['A', 'D', 'R', 'T'])) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }
}