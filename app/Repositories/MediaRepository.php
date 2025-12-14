<?php

namespace App\Repositories;

use App\Models\Media;
use App\Repositories\BaseRepository;

class MediaRepository extends BaseRepository
{
    /**
     * MediaRepository constructor.
     *
     * @param Media $model
     */
    public function __construct(Media $model)
    {
        parent::__construct($model);
    }

    /**
     * Get media by model type and ID.
     *
     * @param string $modelType
     * @param int $modelId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByModel(string $modelType, int $modelId)
    {
        return $this->model->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->get();
    }

    /**
     * Get media by file type.
     *
     * @param string $fileType
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByFileType(string $fileType)
    {
        return $this->model->where('file_type', $fileType)->get();
    }
}

