<?php

namespace App\Grid;

use Illuminate\Contracts\Support\Arrayable;

class GridActionCollection implements Arrayable
{

    private array $actions = [];


    final public function addAction(
        string $label,
        string $url,
        string $icon
    ): GridActionCollection
    {
        $this->actions[] = new GridAction($label, $icon,$url);
        return $this;
    }

    public function toArray(): array {
        return array_map(function (GridAction $action) {
            return $action->toArray();
        },$this->actions);
    }

}
