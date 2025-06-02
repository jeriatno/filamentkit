<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HasAttachments
{
    /**
     * Store multiple attachments dynamically based on the model and request data.
     *
     * @param  Request  $request  The incoming HTTP request containing files.
     * @param  string|null  $number  A unique identifier for the files (e.g., document number).
     * @return array  An array of saved file paths and their original names.
     */
    public function attach(Request $request, ?string $number = null): array
    {
        $attachments = [];

        foreach ($this->attachmentFields as $field) {
            $file = $request->file($field);
            if ($file) {
                $attachments[$field] = $this->processFile($file, $number);
            }
        }

        return $attachments;
    }

    /**
     * Store a single file and generate its metadata.
     *
     * @param  string  $file  The uploaded file instance.
     * @param  string|null  $number  A unique identifier for the file (e.g., document number).
     * @return array  An array containing the saved file path and its original name.
     */
    public function attachFile($file, ?string $number = null): array
    {
        return $file ? $this->processFile($file, $number) : [];
    }

    /**
     * Process a file by generating a unique name, checking for duplicates, and saving it.
     *
     * @param  string $file  The uploaded file instance.
     * @param  string|null  $number  A unique identifier for the file (e.g., document number).
     * @return array  An array containing the saved file path and its original name.
     */
    private function processFile($file, ?string $number = null): array
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slug = Str::slug($originalName);
        $extension = $file->getClientOriginalExtension();

        // Generate initial file name
        $fileName = ($this->docNumberFile ?? $number) . '_' . $slug . '.' . $extension;

        // Check for duplicates and generate a unique name if necessary
        $fullPath = $this->storagePath . '/' . $fileName;
        if (Storage::disk('public')->exists($fullPath)) {
            $uniqueId = now()->timestamp . Str::random(5);
            $fileName = ($this->docNumberFile ?? $number) . '_' . $slug . '_' . $uniqueId . '.' . $extension;
        }

        // Save the file
        $path = $file->storeAs($this->storagePath, $fileName, 'public');

        return [
            'path' => $path,
            'name' => $originalName . '.' . $extension,
        ];
    }

    /**
     * Accessor to get doc number
     * @return mixed|null
     */
    public function getDocNumberFileAttribute()
    {
        return $this->attributes[$this->docNo ?? 'doc_no'] ?? null;
    }
}

