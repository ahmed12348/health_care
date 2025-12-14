<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    /**
     * The model instance.
     *
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all records.
     *
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(array $columns = ['*'])
    {
        return $this->model->select($columns)->get();
    }

    /**
     * Get a record by ID.
     *
     * @param int $id
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getById(int $id, array $columns = ['*'])
    {
        return $this->model->select($columns)->find($id);
    }

    /**
     * Create a new record.
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update a record by ID.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data)
    {
        $record = $this->getById($id);
        
        if (!$record) {
            return false;
        }

        return $record->update($data);
    }

    /**
     * Delete a record by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id)
    {
        $record = $this->getById($id);
        
        if (!$record) {
            return false;
        }

        return $record->delete();
    }

    /**
     * Find records by a specific field.
     *
     * @param string $field
     * @param mixed $value
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findBy(string $field, $value, array $columns = ['*'])
    {
        return $this->model->select($columns)->where($field, $value)->get();
    }

    /**
     * Find a single record by a specific field.
     *
     * @param string $field
     * @param mixed $value
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findOneBy(string $field, $value, array $columns = ['*'])
    {
        return $this->model->select($columns)->where($field, $value)->first();
    }
}

