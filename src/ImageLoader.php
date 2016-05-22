<?php
namespace Ponup\GlLoaders;

class ImageLoader {

    /**
     * @param string $path
     * @param integer &$width
     * @param integer &$height
     */
    public function load($path, &$width, &$height) {
        if(!file_exists($path)) {
            throw new LoaderException('File not found: ' . $path);
        }

        $pixels = [];
        if(substr($path, -3) == 'jpg')
            $img = imagecreatefromjpeg($path);
        else
            $img = imagecreatefrompng($path);
        $width = imagesx($img); 
        $height = imagesy($img); 
        for($h = 0; $h < $height; $h++) {
            for($w = 0; $w < $width; $w++) {
                $pixels[] = imagecolorat($img, $w, $h);
            } 
        }
        return $pixels;
    }
}

