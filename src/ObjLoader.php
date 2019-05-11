<?php
namespace Ponup\GlLoaders;

use \glm\vec2;
use \glm\vec3;

/**
 * This class loads Wavefront (.obj) files and returns their vertices data.
 *
 * @see https://people.cs.clemson.edu/~dhouse/courses/405/docs/brief-obj-file-format.html
 */
class ObjLoader
{

    /**
     * @param string $path
     * @return WavefrontObj
     */
    public function load($path)
    {
        if (!file_exists($path)) {
            throw new LoaderException('File not found: ' . $path);
        }

        $obj = new WavefrontObj;
        //        $obj = new Mesh;

        $file = new \SplFileObject($path);
        $file->setFlags(\SplFileObject::DROP_NEW_LINE);

        while (!$file->eof()) {
            $line = $file->fgets();
            if (empty($line)) {
                continue;
            }
            if ($line[0] == '#') {
                continue;
            }
            $tokens = explode(' ', $line);
            $type = $tokens[0];

            switch ($type) {
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
                    if (preg_match('@f -?\d+/-?\d+/-?\d+ -?\d+/-?\d+/-?\d+ -?\d+/-?\d+/-?\d+@', $line)) {
                        list(
                            $vertin->x, $uvin->x, $normin->x,
                            $vertin->y, $uvin->y, $normin->y,
                            $vertin->z, $uvin->z, $normin->z
                        ) = sscanf($line, 'f %d/%d/%d %d/%d/%d %d/%d/%d');
                        $obj->verticesIndices[] = $vertin->x;
                        $obj->verticesIndices[] = $vertin->y;
                        $obj->verticesIndices[] = $vertin->z;
                    }
                    if (preg_match('@f -?\d+//-?\d+ -?\d+//-?\d+ -?\d+//-?\d+@', $line)) {
                        list(
                            $vertin->x, $normin->x,
                            $vertin->y, $normin->y,
                            $vertin->z, $normin->z
                        ) = sscanf($line, 'f %d//%d %d//%d %d//%d');
                        $obj->verticesIndices[] = $vertin->x;
                        $obj->verticesIndices[] = $vertin->y;
                        $obj->verticesIndices[] = $vertin->z;
                    } elseif (preg_match('@f\s+\d+\s+\d+\s+\d+@', $line)) {
                        list($vertin->x, $vertin->y, $vertin->z) = sscanf($line, 'f  %d  %d  %d');
                        $obj->verticesIndices[] = $vertin->x;
                        $obj->verticesIndices[] = $vertin->y;
                        $obj->verticesIndices[] = $vertin->z;
                    }
                    break;
            }
        }
        $obj->normals = [];
        $obj->normals = array_fill(0, count($obj->vertices), new vec3(0.0, 0.0, 0.0));
        $nb_seen = array_fill(0, count($obj->vertices), 0);
        for ($i = 0; $i < count($obj->verticesIndices); $i += 3) {
            $ia = $obj->verticesIndices[$i] - 1;
            $ib = $obj->verticesIndices[$i + 1] - 1;
            $ic = $obj->verticesIndices[$i + 2] - 1;
            $normal = \glm\normalize(\glm\cross(
                (($obj->vertices[$ib]))->substract(($obj->vertices[$ia])),
                (($obj->vertices[$ic]))->substract(($obj->vertices[$ia]))
            ));

            $v = array($ia, $ib, $ic);
            for ($j = 0; $j < 3; $j++) {
                $cur_v = $v[$j];
                $nb_seen[$cur_v]++;
                if ($nb_seen[$cur_v] == 1) {
                    $obj->normals[$cur_v] = $normal;
                } else {
                    // average
                    $obj->normals[$cur_v]->x = $obj->normals[$cur_v]->x * (1.0 - 1.0 / $nb_seen[$cur_v]) + $normal->x * 1.0 / $nb_seen[$cur_v];
                    $obj->normals[$cur_v]->y = $obj->normals[$cur_v]->y * (1.0 - 1.0 / $nb_seen[$cur_v]) + $normal->y * 1.0 / $nb_seen[$cur_v];
                    $obj->normals[$cur_v]->z = $obj->normals[$cur_v]->z * (1.0 - 1.0 / $nb_seen[$cur_v]) + $normal->z * 1.0 / $nb_seen[$cur_v];
                    $obj->normals[$cur_v] = \glm\normalize($obj->normals[$cur_v]);
                }
            }
        }

        return $obj;
    }
}
