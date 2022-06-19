<?php

namespace ProtoneMedia\LaravelQueryBuilderInertiaJs;

use Illuminate\Contracts\Support\Arrayable;

class Column implements Arrayable
{
    public function __construct(
        public string $key,
        public string $label,
        public bool $canBeHidden,
        public bool $hidden,
        public bool $sortable,
        public bool|string $sorted
    ) {
    }

    public function toArray()
    {
        return [
            'key'           => $this->key,
            'label'         => $this->label,
            'can_be_hidden' => $this->canBeHidden,
            'hidden'        => $this->hidden,
            'sortable'      => $this->sortable,
            'sorted'        => $this->sorted,
        ];
    }
}
