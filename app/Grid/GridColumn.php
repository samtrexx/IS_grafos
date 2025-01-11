<?php

namespace App\Grid;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

class GridColumn implements Arrayable
{

    private string $label;
    public function __construct(
        private string $column,
        private string $table,
        private GridColumnCollection $collection
    )
    {
        $this->label = Str::ucfirst(Str::replace('_', ' ', $this->column));
    }

    public function getLabel(): string {
        return $this->label;
    }

    public function toArray(): array {
        return [
            'column' => $this->column,
            'label' => $this->label,
            'table' => $this->table,
        ];
    }
}
