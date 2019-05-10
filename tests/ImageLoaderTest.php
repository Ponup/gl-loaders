<?php
namespace Ponup\GlLoaders;

class ImageLoaderTest extends \PHPUnit\Framework\TestCase {

    /**
     * @var ImageLoader 
     */
    private $subject;

    public function setUp() {
        $this->subject = new ImageLoader;
    }

    /**
     * @expectedException \Ponup\GlLoaders\LoaderException
     * @expectedExceptionMessage File not found: wrong-file-path.foo
     */
    public function testExceptionIsThrownOnWrongPath() {
        $this->subject->load('wrong-file-path.foo', $width, $height);
    }
}

