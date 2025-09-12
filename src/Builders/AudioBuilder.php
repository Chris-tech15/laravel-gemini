<?php

namespace HosseinHezami\LaravelGemini\Builders;

use HosseinHezami\LaravelGemini\Enums\Capability;
use HosseinHezami\LaravelGemini\Responses\AudioResponse;
use HosseinHezami\LaravelGemini\Exceptions\ValidationException;

class AudioBuilder extends BaseBuilder
{
    protected function getCapability(): string
    {
        return Capability::AUDIO->value;
    }
	
	/**
     * Set voice name for single-speaker TTS.
     *
     * @param string $voiceName e.g., 'Kore', 'Puck'
     * @return self
     */
    public function voiceName(string $voiceName): self
    {
        $this->params['voiceName'] = $voiceName;
        $this->params['multiSpeaker'] = false;
        return $this;
    }

    /**
     * Set speaker voices for multi-speaker TTS.
     *
     * @param array $speakerVoices e.g., [['speaker' => 'Joe', 'voiceName' => 'Kore'], ['speaker' => 'Jane', 'voiceName' => 'Puck']]
     * @return self
     */
    public function speakerVoices(array $speakerVoices): self
    {
        $this->params['speakerVoices'] = $speakerVoices;
        $this->params['multiSpeaker'] = true;
        return $this;
    }

    public function generate(): AudioResponse
    {
		// Validate voiceName for single-speaker
        if (!isset($this->params['multiSpeaker']) || !$this->params['multiSpeaker']) {
            if (!isset($this->params['voiceName']) || empty($this->params['voiceName'])) {
                throw new ValidationException('Voice name is required for single-speaker TTS.');
            }
        }
        // Validate speakerVoices for multi-speaker
        if (isset($this->params['multiSpeaker']) && $this->params['multiSpeaker']) {
            if (!isset($this->params['speakerVoices']) || empty($this->params['speakerVoices'])) {
                throw new ValidationException('Speaker voices are required for multi-speaker TTS.');
            }
        }
        return $this->provider->generateAudio($this->params);
    }
}