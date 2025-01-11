<?php

namespace App\Grid;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

class GridAction implements Arrayable
{

    private string $key;
    public function __construct(
        private string $label,
        private string $icon,
        private string $url,
    )
    {
        $this->key = Str::slug($this->label);
    }

    public function toArray(): array {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'icon' => $this->icon,
            'url' => $this->url,
        ];
    }
}
