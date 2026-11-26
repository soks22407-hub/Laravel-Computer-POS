<?php

namespace App\Exports;

use App\Models\PurchaseRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PurchaseRequestExport implements FromCollection, WithHeadings, WithStyles
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function collection()
    {
        $pr = PurchaseRequest::with(['employee', 'items'])->findOrFail($this->id);

        return $pr->items->map(function ($item) use ($pr) {
            return [
                'Request ID'         => $pr->id,
                'Employee'           => $pr->employee->name,
                'Product Name'       => $item->product_name,
                'Variant'            => $item->variant ?? '—',
                'Current Quantity'   => $item->current_quantity,
                'Requested Quantity' => $item->requested_quantity,
                'Reason'             => $item->reason ?? '—',
                'Status'             => strtoupper($pr->status),
                'Date'               => $pr->created_at->format('d/m/Y'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Request ID', 'Employee', 'Product Name', 'Variant',
            'Current Qty', 'Requested Qty', 'Reason', 'Status', 'Date'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}