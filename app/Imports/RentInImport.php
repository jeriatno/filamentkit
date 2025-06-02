<?php

namespace App\Imports;

use App\Interfaces\ColumnMap;
use App\Models\RentStock\RentStockCard;
use App\Models\RentStock\RentStockImport;
use App\Traits\Excelable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class RentInImport implements ToCollection, WithStartRow, WithHeadingRow, ColumnMap
{
    use Excelable;

    protected $type;
    protected $model;
    protected $request;
    protected $stockCard;
    protected $numericFields = ['qty', 'volume'];
    protected $dateFields = ['gi_date', 'dn_date', 'conf_date'];

    public function __construct($model, $request)
    {
        $this->model = $model;
        $this->request = $request;
        $this->stockCard = new RentStockCard();
        $this->type = RentStockImport::STOCKCARD;
    }

    public function columnMapping(): array
    {
        return [
            'delivery' => 'dn_number',
            'name_of_the_ship_to_party' => 'ship_to_party_name',
            'material' => 'part_number',
            'material_name' => 'part_number_desc',
            'qty' => 'qty',
            'volume' => 'volume',
            'unit' => 'unit',
            'gi_date' => 'gi_date',
            'delivdate' => 'dn_date',
            'to_staging_in' => 'to_staging_in',
            'conf_date' => 'conf_date',
            'conf_by' => 'conf_by',
            'days' => 'days',
        ];
    }

    public function nullableColumns(): array
    {
        return [];
    }

    public function collection(Collection $rows)
    {
        $columnMapping = $this->columnMapping();
        $nullableColumns = $this->nullableColumns();
        $validated = $this->validateHeader($rows, $columnMapping);
        if (!$validated) {
            $failures = $this->getFailures();
            throw new \Exception("File yang Anda masukkan salah. Proses Import dibatalkan! " . implode(', ', $failures->all()));
        }

        // Check exists
        $isExist = $this->model->findByDate($this->type, $this->request->date)->first();
        if($isExist) {
            $message = sprintf(
                "Data with Stock card date '%s' already exists.",
                $this->request->date
            );

            $this->failures[] = $message;
            return;
        }

        // Init recap stats
        $this->data = $this->model->create([
            'type' => RentStockImport::STOCKCARD,
            'period_from' => $this->request->date,
            'imported_at' => now(),
            'imported_by' => auth()->id()
        ]);

        foreach ($rows as $rowIndex => $row) {
            // Validate the row
            $validated = $this->validateImport($row, $columnMapping, $rowIndex, $nullableColumns);

            // Collect valid rows
            if ($validated) {
                $stockCard = $this->stockCard->where([
                    'dn_number' => $validated['dn_number'],
                    'dn_date' => $validated['dn_date'] ?? null,
                    'ship_to_party_name' => $validated['ship_to_party_name'] ?? null,
                    'part_number' => $validated['part_number'] ?? null,
                ])->first();

                if($stockCard) {
                    $stockCard->delete();
                }

                $this->stockCard->create(array_merge($validated, [
                    'rent_stock_import_id' => $this->data->id
                ]));

                $this->totalData++;
            }
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}
