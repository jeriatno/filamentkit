<?php

namespace App\Traits;

trait StatusBadge
{
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the status field name.
     *
     * @return string
     */
    public function getStatusField(): string
    {
        return property_exists($this, 'statusField') ? $this->statusField : 'status';
    }

    /**
     * Get status text mapping.
     *
     * @return array
     */
    protected function getStatusText(): array
    {
        return property_exists($this, 'statusText') ? $this->statusText : [];
    }

    /**
     * Get status badge mapping.
     *
     * @return array
     */
    protected function getStatusBadge(): array
    {
        return property_exists($this, 'statusBadge') ? $this->statusBadge : [];
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the status label.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        $field = $this->getStatusField();
        $statusText = $this->getStatusText();

        return $statusText[$this->attributes[$field]] ?? 'Unknown';
    }

    /**
     * Get the status badge HTML.
     *
     * @return string
     */
    public function getStatusBadgeAttribute(): string
    {
        $field = $this->getStatusField();
        $statusText = $this->getStatusText();
        $statusBadge = $this->getStatusBadge();

        $badgeClass = $statusBadge[$this->attributes[$field]] ?? 'bg-default';
        return '<span class="badge ' . $badgeClass . '">' . $statusText[$this->attributes[$field]] . '</span>';
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    /**
     * @param $value
     * @return void
     */
    public function setStatusAttribute($value)
    {
        if (!array_key_exists($value, $this->getStatusText())) {
            throw new \InvalidArgumentException('Invalid status value');
        }
        $this->attributes['status'] = $value;
    }
}
