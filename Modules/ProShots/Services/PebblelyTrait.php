<?php

namespace Modules\ProShots\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait PebblelyTrait
{
    public function getThemes(): array
    {
        $response = $this->get(self::THEMES_URL);

        return is_array($response) && !isset($response['error']) ? $response : [];
    }

    /**
     * Remove background from image.
     * Returns raw base64-encoded PNG data on success, or an error array on failure.
     * Returning raw data (not a URL) allows the result to be passed directly into createBg().
     */
    public function removeBg(mixed $image): array|string
    {
        $encoded = $this->imageControl($image);

        $body = ['image' => $encoded];

        $response = $this->post(self::REMOVE_BG_URL, $body);

        if (isset($response['error'])) {
            return [
                'error'   => $response['error'],
                'message' => $response['message'] ?? __('Background removal failed.'),
            ];
        }

        // Return the raw base64 image data so createBg() can use it directly
        return $response['data'] ?? '';
    }

    /**
     * Generate a professional background.
     * $image may be:
     *   - an UploadedFile
     *   - a raw base64 string (output of removeBg)
     *   - a readable local file path
     *
     * Returns the public URL of the stored result on success, or an error array.
     */
    public function createBg(mixed $image, string $theme, int $height = 2048, int $width = 2048): array|string
    {
        $encoded = $this->imageControl($image);

        $body = [
            'images' => [$encoded],
            'theme'  => $theme,
            'height' => $height,
            'width'  => $width,
        ];

        $response = $this->post(self::CREATE_BACKGROUND_URL, $body);

        if (isset($response['error'])) {
            return [
                'error'   => $response['error'],
                'message' => $response['message'] ?? __('Background generation failed.'),
            ];
        }

        $imageBytes = base64_decode($response['data']);
        $imageName  = Str::random(12) . '.png';

        Storage::disk('public')->put('proshots/' . $imageName, $imageBytes);

        return Storage::disk('public')->url('proshots/' . $imageName);
    }

    /**
     * Encode an image for the Pebblely API.
     * Accepts UploadedFile, raw base64 string, or local file path.
     */
    private function imageControl(mixed $image): string
    {
        if ($image instanceof \Illuminate\Http\UploadedFile) {
            return base64_encode(file_get_contents($image->getRealPath()));
        }

        if (is_array($image) && isset($image['tmp_name'])) {
            return base64_encode(file_get_contents($image['tmp_name']));
        }

        if (is_string($image)) {
            // Already base64? (output of removeBg)
            if ($this->isBase64($image)) {
                return $image;
            }

            // Local file path
            if (is_readable($image)) {
                return base64_encode(file_get_contents($image));
            }
        }

        return '';
    }

    /**
     * Heuristic: a base64 string is non-empty, uses only base64 chars, and its
     * decoded length is consistent with image data (> 100 bytes).
     */
    private function isBase64(string $value): bool
    {
        if (strlen($value) < 100) {
            return false;
        }

        return (bool) preg_match('/^[A-Za-z0-9+\/]+=*$/', $value);
    }
}
