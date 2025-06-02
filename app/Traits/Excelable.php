<?php

namespace App\Traits;

use App\Utils\DateFormatter;
use App\Utils\NumFormatter;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

trait Excelable
{
    private $data;
    protected $failures = [];
    protected $headerRange;
    private $totalData = 0;
    private $totalError = 0;
    private $totalInserted = 0;
    private $totalDuplicates = 0;
    private $output;
    private $progressBar;

    /*
     * Set header range based on the count of headings
     */
    /**
     * @param  array  $headings
     * @return void
     */
    public function setHeaderRange(array $headings)
    {
        $columnCount = count($headings); // Get the count of headings
        $this->headerRange = 'A1:' . $this->getColumnLetter($columnCount) . '1';
    }

    /**
     * @return mixed
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set style for headers
                $sheet->getStyle($this->headerRange)->getFont()->setSize(13)->setBold(true);
                $sheet->getStyle($this->headerRange)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FFD966');

                // Enable text wrapping for all columns
                $this->applyTextWrapping($sheet);
            },
        ];
    }

    /*
     * Apply text wrapping to the header range
     */
    /**
     * @param $sheet
     * @return void
     */
    protected function applyTextWrapping($sheet)
    {
        $sheet->getStyle($this->headerRange)->getAlignment()->setWrapText(true);
    }

    /*
     * Convert column count to Excel column letter
     */
    /**
     * @param $columnCount
     * @return string
     */
    protected function getColumnLetter($columnCount): string
    {
        $letter = '';
        while ($columnCount > 0) {
            $columnCount--;
            $letter = chr($columnCount % 26 + 65) . $letter;
            $columnCount = intval($columnCount / 26);
        }
        return $letter;
    }


    /**
     * Validates the header row of the imported file.
     * @param $rows
     * @param $columnMapping
     * @return bool
     */
    public function validateHeader($rows, $columnMapping): bool
    {
        $headerRow = $rows->first()->keys()->filter(function ($key) {
            return $key !== ""; // Exclude empty strings
        })->values();

        $headerRowArray = $headerRow->toArray();
        $expectedColumns = array_keys($columnMapping);

        // Check if the number of columns matches
        if (count($headerRowArray) !== count($expectedColumns)) {
            $this->failures[] = "The number of columns in the import file does not match the expected number.";
            return false;
        }

        // Check if the columns are in the correct order
        foreach ($headerRowArray as $index => $columnName) {

            if (!array_key_exists($columnName, $columnMapping)) {
                $this->failures[] = "Column '{$columnName}' is not found in the expected template.";
                return false;
            }

            if ($expectedColumns[$index] !== $columnName) {
                $this->failures[] = "Column '{$columnName}' is not in the correct order. Expected order: " . implode(', ', $expectedColumns);
                return false;
            }
        }

        return true;
    }

    /**
     * Validate data and check for empty fields
     * @param  $rows
     * @param  array  $columnMapping
     * @param $rowIndex
     * @param  bool  $nullable
     * @return array|null
     */
    public function validateImport($rows, array $columnMapping, $rowIndex, bool $nullable = true): ?array
    {
        $emptyFields = [];
        $rows = $rows->toArray();

        foreach ($columnMapping as $fileColumn => $dbColumn) {
            if($nullable) {
                if ($rows[$fileColumn] === "" || $rows[$fileColumn] == null) {
                    $emptyFields[$dbColumn][] = $this->startRow() + $rowIndex;
                }
            }
        }

        // If there are empty fields, add to failures
        if (!empty($emptyFields)) {
            $message = "Row with key has empty fields:\n";
            foreach ($emptyFields as $field => $row) {
                $message .= "- {$field} (row: " . implode(',', $row) . ")\n";
            }
            $this->failures[] = $message;
            return null;
        }

        // If everything is valid, return formatted data
        return $this->formatData($rows, $columnMapping, $rowIndex);
    }

    /**
     * Format data before inserting into the database, e.g., converting dates
     * @param  array  $rows
     * @param  array  $columnMapping
     * @param $rowIndex
     * @return array
     */
    private function formatData(array $rows, array $columnMapping, $rowIndex): array
    {
        $formattedData = [];

        foreach ($columnMapping as $fileColumn => $dbColumn) {
            // Check if the value is supposed to be numeric
            if (in_array($dbColumn, $this->numericFields)) {
                if (!is_numeric($rows[$fileColumn])) {
                    $row = $this->startRow() + $rowIndex;
                    throw new \InvalidArgumentException("Value for '{$fileColumn}' at row '{$row}' is not numeric.");
                }
                $formattedData[$dbColumn] = NumFormatter::toAmount($rows[$fileColumn]);
            }
            // Check if the value is supposed to be a date
            elseif (in_array($dbColumn, $this->dateFields)) {
                if ($rows[$fileColumn] !== null && !$this->isValidExcelDate($rows[$fileColumn])) {
                    $row = $this->startRow() + $rowIndex;
                    throw new \InvalidArgumentException("Value for '{$fileColumn}' at row '{$row}' is not a valid Excel date. The expected format is m/d/Y (e.g., 1/1/2024)");
                }

                if ($rows[$fileColumn] !== null) {
                    $formattedData[$dbColumn] = DateFormatter::convertExcelDate($rows[$fileColumn]);
                }
            }
            // Default case for other fields
            else {
                $formattedData[$dbColumn] = $rows[$fileColumn];
            }
        }

        return $formattedData;
    }

    /**
     * Check if a value is a valid Excel date
     * @param mixed $excelDate
     * @return bool
     */
    private function isValidExcelDate($excelDate): bool
    {
        // Check if the value is numeric and greater than or equal to 0
        return is_numeric($excelDate) && $excelDate >= 0;
    }

    /*
     * Retrieve failure messages
     */
    public function handleFailures($import)
    {
        $failures = $import->getFailures();
        if ($failures->isNotEmpty()) {
            $failureMessages = implode(', ', $failures->toArray());
            throw new \Exception($failureMessages);
        }
    }

    protected function isValidRow($row): bool
    {
        return $row->filter(function ($value) {
            return !is_null($value);
        })->isNotEmpty();
    }

    /**
     * Get all failure messages
     */
    public function getFailures(): Collection
    {
        return collect($this->failures)->map(function($failure) {
            return $failure;
        });
    }

    /**
     * @return void
     */
    public function initProgress(): void
    {
        $this->output = new OutputStyle(new ArrayInput([]), new ConsoleOutput());
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getTotalData(): int
    {
        return $this->totalData;
    }

    /**
     * @return int
     */
    public function getTotalInserted(): int
    {
        return $this->totalInserted;
    }

    /**
     * @return int
     */
    public function getTotalDuplicates(): int
    {
        return $this->totalDuplicates;
    }

    /**
     * @return int
     */
    public function getTotalError(): int
    {
        return $this->totalError;
    }
}
