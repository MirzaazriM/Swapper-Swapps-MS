<?php

require __DIR__ . '/../../../vendor/autoload.php';

class ImagesTest extends \PHPUnit\Framework\TestCase
{

    private $images;

    public function setUp(){
        $this->images = new \Model\Entity\Images();
    }


    /**
     * Provides data for testing Images entity
     */
    public function setImageDataProvider(){

        return [
            [5, 'myfile/x.png', 450, 250, 15],
            ["num", 'myfile/x.png', 450, 250, 15],
            [5, '20', 450, 250, 15],
            [5, 'myfile/x.png', "num", 250, 15],
            [5, 'myfile/x.png', 450, -20, 15],
            [5, 'myfile/x.png', 450, 250, null]
        ];
    }


    /**
     * Function to test Images entity
     *
     * @dataProvider setImageDataProvider
     */
    public function test_image_entity($id, $path, $height, $width, $parent){

        $this->images->setId($id);
        $this->images->setImage($path);
        $this->images->setHeight($height);
        $this->images->setWidth($width);
        $this->images->setParent($parent);

        // check if entity values are correctly set
        $this->assertEquals($id, $this->images->getId(), "Provided and setted ID value are not the same.");
        $this->assertTrue(is_numeric($this->images->getId()) && $this->images->getId() > 0,
            "Setted ID isnt positive number.");

        $this->assertEquals($path, $this->images->getImage(), "Provided and setted PATH value are not the same.");
        $this->assertTrue(is_string($this->images->getImage()) && !is_numeric($this->images->getImage()),
            "Setted PATH isnt string.");

        $this->assertEquals($height, $this->images->getHeight(), "Provided and setted HEIGHT value are not the same.");
        $this->assertTrue(is_numeric($this->images->getHeight()) && $this->images->getHeight() > 0,
            "Setted HEIGHT isnt positive number.");

        $this->assertEquals($width, $this->images->getWidth(), "Provided and setted WIDTH value are not the same.");
        $this->assertTrue(is_numeric($this->images->getWidth()) && $this->images->getWidth() > 0,
            "Setted WIDTH isnt positive number.");

        $this->assertEquals($parent, $this->images->getParent(), "Provided and setted PARENT value are not the same.");
        $this->assertTrue(is_numeric($this->images->getParent()) && $this->images->getParent() > 0,
            "Setted Parent isnt positive number.");
    }

}