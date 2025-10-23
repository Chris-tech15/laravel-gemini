<?php

namespace HosseinHezami\LaravelGemini\Builders;

use HosseinHezami\LaravelGemini\Providers\GeminiProvider;
use HosseinHezami\LaravelGemini\Responses\FileResponse;
use HosseinHezami\LaravelGemini\Exceptions\ValidationException;

class FileBuilder
{
    protected array $params = [];
    protected GeminiProvider $provider;
    
    public function __construct(GeminiProvider $provider)
    {
        $this->provider = $provider;
    }

    public function upload(string $fileType, string $filePath): string
    {
        if (empty($fileType) || empty($filePath)) {
            throw new ValidationException('File type and path are required for upload.');
        }
        $this->params['fileType'] = $fileType;
        $this->params['filePath'] = $filePath;
        return $this->provider->uploadFile($this->params);
    }

    public function get(string $fileName): FileResponse
    {
        if (empty($fileName)) {
            throw new ValidationException('File name is required.');
        }
        return $this->provider->getFile($fileName);
    }

    public function delete(string $fileName): bool
    {
        if (empty($fileName)) {
            throw new ValidationException('File name is required.');
        }
        return $this->provider->deleteFile($fileName);
    }

    public function list(array $params = []): FileResponse
    {
        $this->params = array_merge($this->params, $params);
        return $this->provider->listFiles($this->params);
    }
}