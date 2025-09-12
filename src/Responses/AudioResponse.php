<?php

namespace HosseinHezami\LaravelGemini\Responses;
use HosseinHezami\LaravelGemini\Exceptions\ApiException;

class AudioResponse extends BaseResponse
{
    public function content(): string
    {
		if (isset($this->data['candidates'][0]['finishReason']) && $this->data['candidates'][0]['finishReason'] != 'STOP') {
            $finishReason = $this->data['candidates'][0]['finishReason'] ?? 'UNKNOWN';
            throw new ApiException("Failed to retrieve audio content. Finish reason: {$finishReason}");
        }
        return base64_decode($this->data['candidates'][0]['content']['parts'][0]['inlineData']['data']);
    }
	
	/**
     * Extract audio metadata from mimeType
     */
    public function getAudioMeta(): array
    {
        $inlineData = $this->data['candidates'][0]['content']['parts'][0]['inlineData'] ?? [];
        $mime = $inlineData['mimeType'] ?? null;

        // Defaults
        $sampleRate = 16000;
        $channels = 1;
        $bitsPerSample = 16;

        if ($mime && str_starts_with($mime, 'audio/L16')) {
            $parts = explode(';', $mime);
            foreach ($parts as $part) {
                $part = trim($part);
                if (str_starts_with($part, 'rate=')) {
                    $sampleRate = (int)substr($part, 5);
                } elseif (str_starts_with($part, 'channels=')) {
                    $channels = (int)substr($part, 9);
                }
            }
        }

        return [
            'mimeType'       => $mime,
            'sampleRate'     => $sampleRate,
            'channels'       => $channels,
            'bitsPerSample'  => $bitsPerSample,
        ];
    }

    public function save(string $path): void
    {
        $raw = $this->content();
        $meta = $this->getAudioMeta();

        if ($meta['mimeType'] && str_starts_with($meta['mimeType'], 'audio/L16')) {
            // Wrap raw PCM to WAV
            $wav = $this->pcmToWav(
                $raw,
                $meta['sampleRate'],
                $meta['channels'],
                $meta['bitsPerSample']
            );
            file_put_contents($path, $wav);
        } else {
            // fallback: raw dump
            file_put_contents($path, $raw);
        }
    }

    private function pcmToWav(string $pcmData, int $sampleRate, int $channels, int $bitsPerSample): string
    {
        $byteRate = $sampleRate * $channels * $bitsPerSample / 8;
        $blockAlign = $channels * $bitsPerSample / 8;
        $dataSize = strlen($pcmData);

        // WAV header
        $header = pack('N4', 0x52494646, 36 + $dataSize, 0x57415645, 0x666d7420);
        $header .= pack('V', 16); // Subchunk1Size
        $header .= pack('v', 1);  // PCM format
        $header .= pack('v', $channels);
        $header .= pack('V', $sampleRate);
        $header .= pack('V', $byteRate);
        $header .= pack('v', $blockAlign);
        $header .= pack('v', $bitsPerSample);
        $header .= pack('N2', 0x64617461, $dataSize);

        return $header . $pcmData;
    }
}