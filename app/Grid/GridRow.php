<?php

namespace App\Grid;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

class GridRow implements Arrayable
{

    private GridActionCollection $actions;
    public function __construct(
        private Model $rowitem,
        private GridRowCollection $collection
    ) {}

    public function toArray(): array {
        $actions = $this->collection->actions->toArray();
        $formatedActions = [];
        foreach ($actions as $action) {
            $route = $action['url'];
            $keys = [];
            preg_match_all('/(?<=;)[^;]+(?=;)/', $route, $keys);
            $keys = $keys[0];
            foreach ($keys as $key) {
                $val = $this->rowitem->{$key};
                $route = str_replace(";{$key};",$val,$route);
            }
            $action['url'] = $route;
            $formatedActions[] = $action;
        }
        return [
            'data' => $this->rowitem->toArray(),
            'actions' => $formatedActions,
        ];
    }
}
