<?php

namespace HosseinHezami\LaravelGemini\Contracts;

use HosseinHezami\LaravelGemini\Responses;

interface ProviderInterface
{
    public function generateText(array $payload): Responses\TextResponse;

    public function generateImage(array $payload): Responses\ImageResponse;

    public function generateVideo(array $payload): Responses\VideoResponse;

    public function generateAudio(array $payload): Responses\AudioResponse;

    public function embeddings(array $params): array;
    
    public function uploadFile(array $params): string;
    public function listFiles(array $params): Responses\FileResponse;
    public function getFile(string $fileName): Responses\FileResponse;
    public function deleteFile(string $fileName): bool;

    public function models(): array;

    public function streaming(array $params, callable $callback): void;
}
