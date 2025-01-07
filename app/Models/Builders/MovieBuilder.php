<?php

namespace App\Models\Builders;

use App\Enums\MovieTypeEnum;
use Illuminate\Database\Eloquent\Builder;

class MovieBuilder extends Builder
{

    /**
     * @param bool $value
     * @return $this
     */
    public function active(bool $value = true): static
    {
        return $this->where('is_active', $value);
    }

    public function movies(): static
    {
        return $this->group(MovieTypeEnum::MOVIE->value);
    }

    public function serials(): static
    {
        return $this->group(MovieTypeEnum::SERIES->value);
    }
}
