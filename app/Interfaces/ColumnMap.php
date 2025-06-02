<?php

namespace App\Interfaces;

interface ColumnMap
{
    /**
     * Get the mapping of columns from the file to the database.
     *
     * @return array
     */
    public function columnMapping(): array;

    /**
     * Get the list of columns that are not required.
     *
     * @return array
     */
    public function nullableColumns(): array;
}