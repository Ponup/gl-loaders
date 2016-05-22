<?php
namespace Ponup\GlLoaders;

class ImageLoaderTest extends \PHPUnit_Framework_TestCase {

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

