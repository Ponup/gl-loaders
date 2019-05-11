<?php
namespace Ponup\GlLoaders;

class Mesh extends \Ponup\GlLoaders\WavefrontObj
{
    public $vbo_vertices, $vbo_normals, $ibo_elements;

    public function upload()
    {
        $verticesObjects = $this->getVertices();
        $vertices = [];
        foreach ($verticesObjects as $vertexObject) {
            $vertices[] = $vertexObject->x;
            $vertices[] = $vertexObject->y;
            $vertices[] = $vertexObject->z;
        }

        if (count($vertices) > 0) {
            glGenBuffers(1, $this->vbo_vertices2);
            $this->vbo_vertices = $this->vbo_vertices2[0];
            glBindBuffer(GL_ARRAY_BUFFER, $this->vbo_vertices);
            glBufferData(
                GL_ARRAY_BUFFER,
                count($vertices) * 4,
                $vertices,
                GL_STATIC_DRAW
            );
        }

        $normalObjects = $this->getVertexNormals();
        $normals = [];
        foreach ($normalObjects as $normalObject) {
            $normals[] = $normalObject->x;
            $normals[] = $normalObject->y;
            $normals[] = $normalObject->z;
        }

        if (count($normals) > 0) {
            glGenBuffers(1, $this->vbo_normals2);
            $this->vbo_normals = $this->vbo_normals2[0];
            glBindBuffer(GL_ARRAY_BUFFER, $this->vbo_normals);
            glBufferData(
                GL_ARRAY_BUFFER,
                count($normals) * 4,
                $normals,
                GL_STATIC_DRAW
            );
        }

        if (count($this->verticesIndices) > 0) {
            $indices = $this->getVertexFaces();
            $this->elements = array_map(function ($index) {
                return $index - 1;
            }, $indices);

            glGenBuffers(1, $this->ibo_elements2);
            $this->ibo_elements = $this->ibo_elements2[0];
            glBindBuffer(GL_ELEMENT_ARRAY_BUFFER, $this->ibo_elements);
            glBufferData(
                GL_ELEMENT_ARRAY_BUFFER,
                count($this->elements) * 4,
                $this->elements,
                GL_STATIC_DRAW
            );
        }
    }

    public function draw()
    {
        $attribute_v_coord = 0;
        if ($this->vbo_vertices != 0) {
            glEnableVertexAttribArray($attribute_v_coord);
            glBindBuffer(GL_ARRAY_BUFFER, $this->vbo_vertices);
            glVertexAttribPointer(
                $attribute_v_coord,  // attribute
                4,                  // number of elements per vertex, here (x,y,z,w)
                GL_FLOAT,           // the type of each element
                GL_FALSE,           // take our values as-is
                0,                  // no extra data between each position
                0                   // offset of first element
            );
        }

        $attribute_v_normal = 1;
        if ($this->vbo_normals != 0) {
            glEnableVertexAttribArray($attribute_v_normal);
            glBindBuffer(GL_ARRAY_BUFFER, $this->vbo_normals);
            glVertexAttribPointer(
                $attribute_v_normal, // attribute
                3,                  // number of elements per vertex, here (x,y,z)
                GL_FLOAT,           // the type of each element
                GL_FALSE,           // take our values as-is
                0,                  // no extra data between each position
                0                   // offset of first element
            );
        }

        /* Push each element in buffer_vertices to the vertex shader */
        if ($this->ibo_elements != 0) {
            glBindBuffer(GL_ELEMENT_ARRAY_BUFFER, $this->ibo_elements);
            $a = count($this->verticesIndices);
            glDrawElements(GL_TRIANGLES, $a, GL_UNSIGNED_SHORT, 0);
        } else {
            $b = count($this->vertices);
            glDrawArrays(GL_TRIANGLES, 0, $b);
        }

        if ($this->vbo_normals != 0) {
            //glDisableVertexAttribArray($attribute_v_normal);
        }
        if ($this->vbo_vertices != 0) {
            //glDisableVertexAttribArray($attribute_v_coord);
        }
    }
}