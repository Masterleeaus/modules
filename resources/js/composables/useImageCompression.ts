/**
 * Compress an image File to a JPEG Blob under `maxKB` kilobytes.
 *
 * Steps:
 * 1. Downsample dimensions to at most `MAX_DIM` px on the longest side.
 * 2. Re-encode as JPEG, stepping quality down by 0.1 until under the target
 *    size or quality reaches 0.3 (minimum acceptable).
 */
export function useImageCompression() {
    function compressImage(file: File, maxKB = 800): Promise<Blob> {
        return new Promise((resolve) => {
            const img = new Image();
            const url = URL.createObjectURL(file);

            img.onload = () => {
                URL.revokeObjectURL(url);

                const canvas = document.createElement('canvas');
                const MAX_DIM = 1920;
                let { width, height } = img;

                if (width > MAX_DIM || height > MAX_DIM) {
                    if (width > height) {
                        height = Math.round((height * MAX_DIM) / width);
                        width = MAX_DIM;
                    } else {
                        width = Math.round((width * MAX_DIM) / height);
                        height = MAX_DIM;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                canvas.getContext('2d')!.drawImage(img, 0, 0, width, height);

                const tryQuality = (q: number) => {
                    canvas.toBlob(
                        (blob) => {
                            if (!blob) { resolve(new Blob()); return; }
                            if (blob.size <= maxKB * 1024 || q <= 0.3) { resolve(blob); return; }
                            tryQuality(Math.round((q - 0.1) * 10) / 10);
                        },
                        'image/jpeg',
                        q,
                    );
                };

                tryQuality(0.85);
            };

            img.src = url;
        });
    }

    return { compressImage };
}
