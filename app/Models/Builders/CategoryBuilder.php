<?php

namespace App\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

class CategoryBuilder extends Builder
{

    /**
     * @param bool $value
     * @return $this
     */
    public function active(bool $value = true): static
    {
        return $this->where('is_active', $value);
    }
}
