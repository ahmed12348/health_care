<?php

namespace App\Services;

use App\Models\Media;
use App\Repositories\MediaRepository;
use App\Services\BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaService extends BaseService
{
    /**
     * MediaService constructor.
     *
     * @param MediaRepository $repository
     */
    public function __construct(
        protected MediaRepository $repository
    ) {}

    /**
     * Upload and associate media with a model.
     *
     * @param UploadedFile $file
     * @param string $modelType
     * @param int $modelId
     * @param string|null $disk
     * @return Media
     */
    public function uploadAndAttach(UploadedFile $file, string $modelType, int $modelId, ?string $disk = 'public'): Media
    {
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        
        // Store file
        $filePath = $file->storeAs('media', $filename, $disk);
        
        // Determine file type
        $fileType = $this->getFileType($file);
        
        // Create media record
        return $this->repository->create([
            'model_type' => $modelType,
            'model_id' => $modelId,
            'file_path' => $filePath,
            'file_type' => $fileType,
        ]);
    }

    /**
     * Get media for a specific model.
     *
     * @param string $modelType
     * @param int $modelId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMediaForModel(string $modelType, int $modelId)
    {
        return $this->repository->getByModel($modelType, $modelId);
    }

    /**
     * Delete media file and record.
     *
     * @param int $mediaId
     * @param string $disk
     * @return bool
     */
    public function deleteMedia(int $mediaId, string $disk = 'public'): bool
    {
        $media = $this->repository->getById($mediaId);
        
        if (!$media) {
            return false;
        }

        // Delete file from storage
        if (Storage::disk($disk)->exists($media->file_path)) {
            Storage::disk($disk)->delete($media->file_path);
        }

        // Delete media record
        return $this->repository->delete($mediaId);
    }

    /**
     * Determine file type from uploaded file.
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function getFileType(UploadedFile $file): string
    {
        $mimeType = $file->getMimeType();
        
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }
        
        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }
        
        if (str_starts_with($mimeType, 'application/pdf')) {
            return 'document';
        }
        
        return 'other';
    }

    /**
     * Handle the service logic (required by BaseService).
     *
     * @param mixed ...$args
     * @return mixed
     */
    public function handle(...$args)
    {
        return $this->repository->getAll();
    }
}

