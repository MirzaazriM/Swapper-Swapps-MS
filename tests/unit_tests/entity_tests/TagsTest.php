<?php

require __DIR__ . '/../../../vendor/autoload.php';

class TagsTest extends \PHPUnit\Framework\TestCase
{

    private $tags;

    public function setUp(){
        $this->tags = new \Model\Entity\Tags();
    }


    /**
     * Provides data for testing Tags entity
     */
    public function setTagDataProvider(){

        return [
            [5, 'name', 'SWAPP', 2, 15]
        ];
    }


    /**
     * Function to test Tags entity
     *
     * @dataProvider setTagDataProvider
     */
    public function test_tags($id, $name, $type, $tagId, $parent){

        $this->tags->setId($id);
        $this->tags->setName($name);
        $this->tags->setType($type);
        $this->tags->setTagId($tagId);
        $this->tags->setParent($parent);

        // check if entity values are correctly set
        $this->assertEquals($id, $this->tags->getId(), "Provided and setted ID value are not the same.");
        $this->assertTrue(is_numeric($this->tags->getId()) && $this->tags->getId() > 0,
            "Setted ID isnt positive number.");

        $this->assertEquals($name, $this->tags->getName(), "Provided and setted NAME value are not the same.");
        $this->assertTrue(is_string($this->tags->getName()) && !is_numeric($this->tags->getName()),
            "Setted NAME isnt string.");

        $this->assertContains($this->tags->getType(), ['SWAPP', 'TAG'], "Setted type isnt valid.");

        $this->assertEquals($tagId, $this->tags->getTagId(), "Provided and setted TAGID value are not the same.");
        $this->assertTrue(is_numeric($this->tags->getTagId()) && $this->tags->getTagId() > 0,
            "Setted TAGID isnt positive number.");

        $this->assertEquals($parent, $this->tags->getParent(), "Provided and setted PARENT value are not the same.");
        $this->assertTrue(is_numeric($this->tags->getParent()) && $this->tags->getParent() > 0,
            "Setted Parent isnt positive number.");
    }
}