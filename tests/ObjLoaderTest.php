<?php
namespace Ponup\GlLoaders;

class ObjLoaderTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var ObjLoader
     */
    private $subject;

    public function setUp() {
        $this->subject = new ObjLoader;
    }

    /**
     * @expectedException \Ponup\GlLoaders\LoaderException
     * @expectedExceptionMessage File not found: wrong-file-path.foo
     */
    public function testExceptionIsThrownOnWrongPath() {
        $this->subject->load('wrong-file-path.foo');
    }

    public function testVerticesAreLoaded() {
        $obj = $this->subject->load(__DIR__ . '/cube.obj');
        $this->assertInstanceOf('\Ponup\GlLoaders\WavefrontObj', $obj);

        $this->assertCount(8, $obj->getVertices());
        $this->assertCount(4, $obj->getTextureCoordinates());
        $this->assertCount(8, $obj->getVertexNormals());
        $this->assertCount(36, $obj->getVertexFaces());
    }
}

