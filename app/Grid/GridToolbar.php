<?php

namespace App\Grid;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @property-read GridActionCollection $actions
 */
class GridToolbar implements Arrayable
{
    private GridActionCollection $actions;

    public function __construct(){
        $this->actions = new GridActionCollection();
    }

    public function __get(string $name)
    {
        return match ($name) {
            'actions' => $this->actions,
            default => null,
        };
    }

    public function toArray(): array {
        return [
            'actions' => $this->actions->toArray(),
        ];
    }
}
