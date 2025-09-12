<?php

namespace HosseinHezami\LaravelGemini\Providers;

use HosseinHezami\LaravelGemini\Contracts\ProviderInterface;
use HosseinHezami\LaravelGemini\Responses;
use HosseinHezami\LaravelGemini\Exceptions\ApiException;
use HosseinHezami\LaravelGemini\Exceptions\StreamException;
use HosseinHezami\LaravelGemini\Exceptions\RateLimitException;
use HosseinHezami\LaravelGemini\Exceptions\ValidationException;
use Illuminate\Support\Facades\Log;

class GeminiProvider extends BaseProvider implements ProviderInterface
{
    public function generateText(array $params): Responses\TextResponse
    {
        return $this->executeRequest($params, 'Text');
    }

    public function generateImage(array $params): Responses\ImageResponse
    {
        return $this->executeRequest($params, 'Image');
    }

    public function generateVideo(array $params): Responses\VideoResponse
    {
        return $this->executeRequest($params, 'Video');
    }

    public function generateAudio(array $params): Responses\AudioResponse
    {
        return $this->executeRequest($params, 'Audio');
    }

    public function embeddings(array $params): array
    {
        $response = $this->http->post("models/{$params['model']}:embedContent", $params);
        return $response->json();
    }

    public function uploadFile(array $params): string
    {
        if (!isset($params['fileType']) || !isset($params['filePath'])) {
            throw new ValidationException('File type and path are required.');
        }
        return $this->upload($params['fileType'], $params['filePath']);
    }

    public function listFiles(array $params = []): Responses\FileResponse
    {
        try {
			$response = Http::baseUrl("https://generativelanguage.googleapis.com")
			->withHeaders([
				'x-goog-api-key' => config('gemini.api_key'),
			])
			->timeout(config('gemini.timeout'))
			->retry(config('gemini.retry_policy.max_retries'), config('gemini.retry_policy.retry_delay'), function ($exception, $request) {
				if ($exception instanceof Exceptions\RateLimitException) {
					sleep($exception->retryAfter ?? 1);
					return true;
				}
				return $exception->response->status() >= 500;
			})
			->get("v1beta/files");

            return $this->handleResponse($response, 'File');
        } catch (\Exception $e) {
            throw new ApiException("Get files list error: {$e->getMessage()}");
        }
    }

    public function getFile(string $fileName): Responses\FileResponse
    {
        if (empty($fileName)) {
            throw new ValidationException('File name is required.');
        }

        try {
			$response = Http::baseUrl("https://generativelanguage.googleapis.com")
			->withHeaders([
				'x-goog-api-key' => config('gemini.api_key'),
			])
			->timeout(config('gemini.timeout'))
			->retry(config('gemini.retry_policy.max_retries'), config('gemini.retry_policy.retry_delay'), function ($exception, $request) {
				if ($exception instanceof Exceptions\RateLimitException) {
					sleep($exception->retryAfter ?? 1);
					return true;
				}
				return $exception->response->status() >= 500;
			})
			->get("v1beta/files/{$fileName}");
			
            return $this->handleResponse($response, 'File');
        } catch (\Exception $e) {
            throw new ApiException("Get file error: {$e->getMessage()}");
        }
    }

    public function deleteFile(string $fileName): bool
    {
        if (empty($fileName)) {
            throw new ValidationException('File name is required.');
        }

        try {
			$response = Http::baseUrl("https://generativelanguage.googleapis.com")
			->withHeaders([
				'x-goog-api-key' => config('gemini.api_key'),
			])
			->timeout(config('gemini.timeout'))
			->retry(config('gemini.retry_policy.max_retries'), config('gemini.retry_policy.retry_delay'), function ($exception, $request) {
				if ($exception instanceof Exceptions\RateLimitException) {
					sleep($exception->retryAfter ?? 1);
					return true;
				}
				return $exception->response->status() >= 500;
			})
			->delete("v1beta/files/{$fileName}");
			
            if ($response->status() === 200) {
                return true;
            }

            throw new ApiException('Failed to delete file.');
        } catch (\Exception $e) {
            throw new ApiException("Delete file error: {$e->getMessage()}");
        }
    }

    public function models(): array
    {
        $response = $this->http->get('models');
        return $response->json()['models'];
    }

    public function streaming(array $params, callable $callback): void
    {
        $method = $params['method'] ?? 'generateContent';
        if ($method !== 'generateContent') {
            throw new ValidationException('Streaming only supported for generateContent method.');
        }
        try {
            $response = $this->http->withOptions([
                'stream' => true,
            ])->post("models/{$params['model']}:streamGenerateContent", $this->buildRequestBody($params));
            
            $body = $response->getBody();
            $buffer = '';

            while (!$body->eof()) {
                $chunk = $body->read(config('gemini.stream.chunk_size', '1024'));
                if (!empty($chunk)) {
                    $buffer .= $chunk;
                    $lines = explode("\n", $buffer);
                    $buffer = array_pop($lines); // Keep last incomplete line

                    foreach ($lines as $line) {
                        if (strpos($line, 'data: ') === 0) {
                            $jsonStr = substr($line, 5); // Remove 'data: ' prefix
                            $data = json_decode(trim($jsonStr), true);
                            
                            if (json_last_error() === JSON_ERROR_NONE) {
                                $part = $data['candidates'][0]['content']['parts'][0] ?? [];
                                $callback($part);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            throw new StreamException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
    
    protected function executeRequest(array $params, string $responseType)
    {
        $method = $params['method'] ?? 'generateContent';
        $body = $this->buildRequestBody($params, $method === 'predictLongRunning', $responseType === 'Audio');
        $endpoint = "models/{$params['model']}:" . $method;

        $response = $this->http->post($endpoint, $body);

        if ($method === 'predictLongRunning') {
            $operation = $response->json()['name'];
            do {
                sleep(5);
                $status = $this->http->get($operation)->json();
            } while (!$status['done']);
            return $this->handleResponse($this->http->get($status['response']['generatedSamples'][0][$responseType === 'Video' ? 'video' : 'uri']), $responseType);
        }

		// Check for error response
        if (isset($response->json()['candidates'][0]['finishReason']) && $response->json()['candidates'][0]['finishReason'] != 'STOP') {
            Log::error('Gemini API error response', ['response' => $response->json()]);
            throw new ApiException("API request failed with finishReason: {$response->json()['candidates'][0]['finishReason']}");
        }
		
        return $this->handleResponse($response, $responseType);
    }

    protected function buildRequestBody(array $params, bool $forLongRunning = false, bool $forAudio = false): array
    {
        $method = $params['method'] ?? 'generateContent';
        $isPredict = $method === 'predict' || $method === 'predictLongRunning';
        
        if ($isPredict) {
            // Structure for predict/predictLongRunning
            $instance = ['prompt' => $params['prompt'] ?? ''];
            if (isset($params['filePath']) && isset($params['fileType'])) {
                $filePart = $params['fileType'] === 'image' ? [
                    'inlineData' => [
                        'mimeType' => $this->getMimeType($params['fileType'], $params['filePath']),
                        'data' => base64_encode(file_get_contents($params['filePath']))
                    ]
                ] : [
                    'fileData' => [
                        'mimeType' => $this->getMimeType($params['fileType'], $params['filePath']),
                        'fileUri' => $this->upload($params['fileType'], $params['filePath'])
                    ]
                ];
                $instance = array_merge($instance, $filePart);
            }
            $body = [
                'instances' => [$instance],
                'parameters' => [
                    'temperature' => $params['temperature'] ?? 0.7,
                    'maxOutputTokens' => $params['maxTokens'] ?? 1024,
                ],
            ];
            if (isset($params['safetySettings'])) {
                $body['parameters']['safetySettings'] = $params['safetySettings'];
            }
        } else {
            // Structure for generateContent
            if (!isset($params['prompt']) || empty($params['prompt'])) {
                throw new ValidationException('Prompt is required for audio generation (TTS).');
            }

            $body = [
                'contents' => $params['contents'] ?? [['parts' => [['text' => $params['prompt'] ?? '']]]],
                'generationConfig' => [
                    'temperature' => $params['temperature'] ?? 0.7,
                    'maxOutputTokens' => $params['maxTokens'] ?? 1024,
                ],
                'safetySettings' => $params['safetySettings'] ?? config('gemini.safety_settings'),
            ];

            if (isset($params['filePath']) && isset($params['fileType'])) {
                $filePart = $params['fileType'] === 'image' ? [
                    'inlineData' => [
                        'mimeType' => $this->getMimeType($params['fileType'], $params['filePath']),
                        'data' => base64_encode(file_get_contents($params['filePath']))
                    ]
                ] : [
                    'fileData' => [
                        'mimeType' => $this->getMimeType($params['fileType'], $params['filePath']),
                        'fileUri' => $this->upload($params['fileType'], $params['filePath'])
                    ]
                ];
                $body['contents'][0]['parts'][] = $filePart;
            }

            if ($forAudio) {
                $body['generationConfig']['responseModalities'] = ['AUDIO'];
                $speechConfig = config('gemini.default_speech_config', []);
                if (isset($params['multiSpeaker']) && $params['multiSpeaker']) {
                    $speechConfig['multiSpeakerVoiceConfig'] = [
                        'speakerVoiceConfigs' => $params['speakerVoices'] ?? []
                    ];
                } else {
                    $speechConfig['voiceConfig'] = [
                        'prebuiltVoiceConfig' => [
                            'voiceName' => $params['voiceName'] ?? $speechConfig['voiceName'] ?? config('gemini.providers.gemini.default_speech_config.voiceName')
                        ]
                    ];
                }
                $body['generationConfig']['speechConfig'] = $speechConfig;
            }
        }

        if (isset($params['system'])) {
            $body[$isPredict ? 'parameters' : 'systemInstruction'] = ['parts' => [['text' => $params['system']]]];
        }

        if (isset($params['history'])) {
            $body['contents'] = [['role' => 'user', 'parts' => [['text' => $params['prompt'] ?? '']]]];
            $body[$isPredict ? 'instances' : 'contents'] = array_merge($params['history'], $body[$isPredict ? 'instances' : 'contents']);
        }

        if (isset($params['functions'])) {
            $body[$isPredict ? 'parameters' : 'tools'] = ['functionDeclarations' => $params['functions']];
        }

        if (isset($params['structuredSchema'])) {
            $body[$isPredict ? 'parameters' : 'generationConfig']['responseMimeType'] = 'application/json';
            $body[$isPredict ? 'parameters' : 'generationConfig']['responseSchema'] = $params['structuredSchema'];
        }

        if ($forLongRunning) {
            // For predictLongRunning, no additional changes needed as per docs
        }

        return $body;
    }
}