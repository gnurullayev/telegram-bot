<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
abstract class BaseRepository
{

    /**
     * @return class-string<Model|TModel>
     */
    abstract public static function modelClass(): string;

    /**
     * @param array $data
     * @return Model|TModel
     */
    public function store(array $data)
    {
        $class = static::modelClass();
        return $class::create($data);
    }

    /**
     * @param int|Model|TModel $model
     * @param array $data
     * @return Model|TModel
     */
    public function update($model, array $data)
    {
        if (!is_object($model)) {
            $model = $this->findById($model);
        }

        $model->fill($data);
        $model->save();
        return $model;
    }

    /**
     * @param int $id
     * @return Model|TModel
     */
    public function findById($id)
    {
        $class = static::modelClass();
        return $class::findOrFail($id);
    }
}
