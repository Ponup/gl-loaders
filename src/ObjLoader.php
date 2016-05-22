<?php
namespace Ponup\GlLoaders;

use \glm\vec3;

class Vec2 { public $x, $y; }

/**
 * This class loads Wavefront (.obj) files and returns their vertices data.
 *
 * @see https://people.cs.clemson.edu/~dhouse/courses/405/docs/brief-obj-file-format.html
 */
class ObjLoader {

    /**
     * @param string $path
     * @return WavefrontObj
     */
    public function load($path) {
        if(!file_exists($path)) {
            throw new LoaderException('File not found: ' . $path);
        }

        $obj = new WavefrontObj;

        $file = new \SplFileObject($path);
        $file->setFlags(\SplFileObject::DROP_NEW_LINE);

        while(!$file->eof()) {
            $line = $file->fgets();
            if(empty($line)) {
                continue;
            }
            if($line[0] == '#') {
                continue;
            }
            $tokens = explode(' ', $line);
            $type = $tokens[0];

            switch($type) {
                case 'v':
                    $vertex = new vec3;
                    list($vertex->x, $vertex->y, $vertex->z) = sscanf($line, 'v %f %f %f');
                    $obj->vertices[] = $vertex;
                break;
                case 'vt':
                    $uv = new Vec2;
                    list($uv->x, $uv->y) = sscanf($line, 'vt %f %f');
                    $obj->textureCoordinates[] = $uv;
                break;
                case 'vn':
                    $vertex = new vec3;
                    list($vertex->x, $vertex->y, $vertex->z) = sscanf($line, 'vn %f %f %f');
                    $obj->normals[] = $vertex;
                break;
                case 'f':
                    $vertin = new vec3;
                    $uvin = new vec3;
                    $normin = new vec3;
                    if(preg_match('@f -?\d+/-?\d+/-?\d+ -?\d+/-?\d+/-?\d+ -?\d+/-?\d+/-?\d+@', $line)) {
                        list(
                            $vertin->x, $uvin->x, $normin->x,
                            $vertin->y, $uvin->y, $normin->y,
                            $vertin->z, $uvin->z, $normin->z
                        ) = sscanf($line, 'f %d/%d/%d %d/%d/%d %d/%d/%d');
                        $obj->verticesIndices[] = $vertin;
                    }
                    elseif(preg_match('@f\s+\d+\s+\d+\s+\d+@', $line)) {
                        list($vertin->x, $vertin->y, $vertin->z) = sscanf($line, 'f  %d  %d  %d');
                        $obj->verticesIndices[] = $vertin;
                    }
                    break;
            }
        }

        return $obj;
    }
}

