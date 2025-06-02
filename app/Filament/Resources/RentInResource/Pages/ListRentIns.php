<?php

namespace App\Filament\Resources\RentInResource\Pages;

use App\Filament\Resources\RentInResource;
use App\Imports\RentInImport;
use App\Imports\TaskAssignImport;
use App\Models\Rent\RentIn;
use App\Models\Task\TaskAssign;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListRentIns extends ListRecords
{
    protected static string $resource = RentInResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('importData')
                ->icon('heroicon-s-arrow-up-tray')
                ->label('Import Data')
                ->color('gray')
                ->modalHeading(new \Illuminate\Support\HtmlString('<p class="bulk-assign" style="font-size:18px; font-weight:700">Bulk Upload Rent In<span>*</span></p>'))
                ->modalDescription(new \Illuminate\Support\HtmlString(
                    '<a class="bulk-import-download-link" href="' . route('rent-ins.download-template') . '" target="_blank" style="color:#8A2785; font-weight:600; font-size:14px"><i class="fa fa-import"></i> Download template XLS/XLSX file</a>'
                ))
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('Upload File')
                        ->hiddenLabel()
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel'
                        ])
                        ->helperText('Only .xls and .xlsx files are allowed.')
                        ->name('file')
                        ->rules(['required', 'mimes:xls,xlsx'])
                        ->required()
                ])
                ->action(function (array $data) {
                    $import = new RentInImport(new RentIn(), request());

                    try {
                        \Maatwebsite\Excel\Facades\Excel::import($import, $data['file']);
                        $errors = $import->getAllErrors();
                        $duplicates = $import->getDuplicateRows();
                        $unauthorized = $import->getUnauthorizedRows();
                        $summary = $import->getImportSummary();

                        // Display error messages
                        if (!empty($errors)) {
                            $errorList = "<div style='max-width: 600px; text-align: left; font-size: 11px;'>
                            <ul style='margin: 0; list-style-type:square'>";
                            foreach ($errors as $error) {
                                $errorList .= "<li style='padding-bottom: 6px; line-height: 115%;'>{$error}</li>";
                            }
                            $errorList .= "</ul></div>";

                            Notification::make()
                                ->title('Data Failed to Import')
                                ->icon('heroicon-s-exclamation-triangle')
                                ->iconColor('danger')
                                ->body($errorList)
                                ->danger()
                                ->send();
                        } else {

                            $summary = $import->getImportSummary();
                            $importedCount = $summary['imported'] ?? 0;

                            if ($importedCount > 0) {
                                Notification::make()
                                    ->title('Success')
                                    ->body('Data imported successfully.')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('No Records Imported')
                                    ->body('No records were imported.')
                                    ->warning()
                                    ->send();
                            }

                            // Display duplicate messages
                            if (!empty($duplicates)) {
                                $duplicateList = "<div style='max-width: 600px; max-height: 360px; overflow-y: auto; text-align: left; font-size: 11px; border-radius: 4px;'>
                                <ul style='margin: 0; list-style-type: square; padding-left: 20px'>";
                                foreach ($duplicates as $duplicate) {
                                    $duplicateList .= "<li style='padding-bottom: 6px; line-height: 115%;'>{$duplicate}</li>";
                                }
                                $duplicateList .= "</ul></div>";

                                Notification::make()
                                    ->title('Some Entries Were Skipped')
                                    ->icon('heroicon-s-information-circle')
                                    ->iconColor('warning')
                                    ->body($duplicateList)
                                    ->warning()
                                    ->send();
                            }

                            // Display summary messages
                            if (!empty($summary)) {
                                $summaryList = "<div style='max-width: 600px; text-align: left; font-size: 11px;'>
                                <ul style='margin: 0; list-style-type:square'>";

                                foreach ($summary as $key => $value) {
                                    // Convert keys to readable text
                                    $label = match ($key) {
                                        'imported' => 'Imported Records',
                                        'skipped' => 'Skipped Records',
                                        'unauthorized' => 'Unauthorized Records',
                                        'total' => 'Total Processed',
                                        default => ucfirst($key),
                                    };

                                    $summaryList .= "<li style='padding-bottom: 6px; line-height: 115%;'><strong>{$label}:</strong> {$value}</li>";
                                }

                                $summaryList .= "</ul></div>";

                                Notification::make()
                                    ->title('Import Summary')
                                    ->icon('heroicon-s-information-circle')
                                    ->iconColor('info')
                                    ->body($summaryList)
                                    ->info()
                                    ->send();
                            }
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error')
                            ->body('Failed to process the file. ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\CreateAction::make()
                ->icon('heroicon-s-plus-circle')
                ->label('New Rent In')
                ->iconSize('w-5 h-5'),
        ];
    }
}
