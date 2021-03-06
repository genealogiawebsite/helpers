<?php

namespace LaravelEnso\Helpers\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable as ScoutSearchable;

trait Searchable
{
    use ScoutSearchable;

    public function save(array $options = [])
    {
        if ($this->shouldPerformSearchSyncing()) {
            return parent::save($options);
        }

        static::disableSearchSyncing();
        $result = parent::save($options);
        static::enableSearchSyncing();

        return $result;
    }

    public function shouldPerformSearchSyncing()
    {
        $dirtyKeys = array_keys($this->getDirty());

        return (new Collection($this->toSearchableArray()))->keys()
            ->map(fn ($key) => Str::snake($key))
            ->intersect($dirtyKeys)->isNotEmpty();
    }
}
