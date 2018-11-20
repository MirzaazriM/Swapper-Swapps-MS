<?php

require __DIR__ . '/../../../vendor/autoload.php';

class CreateSwappTest extends \PHPUnit\Framework\TestCase
{

    public static function setUpBeforeClass(){
        // print heading of tests
        printf("***********  TESTING CREATE SWAPP SERVICE  **********");
    }


    public function storingSwappInEntityDataProvider(){

        return [
            ["Name", "Description...", 5],
            ["Name", "10", 5],
            [15, "Description...", 5],
        ];
    }


    /**
     * Function tests storing swapp info into swapp entity
     *
     * @dataProvider storingSwappInEntityDataProvider
     */
    public function test_storing_swapp_in_entity($name, $description, $fromUser){
        // create swapp entity
        $swapp = new \Model\Entity\Swap();
        $swapp->setName($name);
        $swapp->setDescription($description);
        $swapp->setUserId($fromUser);

        // check if setted entity values are equal to provided ones
        $this->assertEquals($name, $swapp->getName(), "Provided and entity name value are not equal.");
        $this->assertEquals($description, $swapp->getDescription(), "Provided and entity description value are not equal.");
        $this->assertEquals($fromUser, $swapp->getUserId(), "Provided and entity userId value are not equal.");

        // check if entity accepts correct type values
        $this->assertTrue(is_string($name) && !is_numeric($name), "Swapp name isnt string.");
        $this->assertTrue(is_string($description) && !is_numeric($description), "Swapp description isnt string.");
        $this->assertTrue(is_numeric($fromUser) && $fromUser > 0, "Swapp userId isnt string.");
    }


    /**
     * Data provider for testing tag loops
     */
    public function storingTagsDataProvider(){

        return [
            [["One", "Two", "Three"], 5],
            [["One", 2, "Three"], 15],

        ];
    }


    /**
     * Function to test first loop (Set tags)
     *
     * @dataProvider storingTagsDataProvider
     */
    public function test_storing_tags($tagsData, $swappId){

        // number of iterations tracker
        $tracker = 0;

        // loop through all tags
        foreach($tagsData as $tag) {
            // set entity values and chek if setted values are correct
            $tags = new \Model\Entity\Tags();
            $tags->setName($tag);
            $this->assertEquals($tag, $tags->getName(), "Setted and provided TAG name value in TESTING FIRST LOOP are not equal.");
            $tags->setType("TAG");
            $this->assertEquals("TAG", $tags->getType(), "Setted and provided TYPE value in TESTING FIRST LOOP are not equal.");
            $tags->setParent($swappId);
            $this->assertEquals($swappId, $tags->getParent(), "Setted and provided PARENT value in TESTING FIRST LOOP are not equal.");

            // mock storing tag and tags
            $storingMock = $this->getMockBuilder(\Model\Mapper\SwappMapper::class)
                ->disableOriginalConstructor()
                ->setMethods(array())
                ->getMock();

            // set expectations  --  in each iteration this method must be called
            $storingMock->expects($this->once())->method('storeTag')->with();
            $storingMock->expects($this->once())->method('storeTags')->with();
            // call mock methods
            $storingMock->storeTag($tags);
            $storingMock->storeTags($tags);

            // increment tracker
            $tracker++;
        }

        // check if number of iterations is equal to tags array length
        $this->assertEquals($tracker, count($tagsData));
    }



    /**
     * Function to test second loop (Swapping for tags)
     *
     * @dataProvider storingTagsDataProvider
     */
    public function test_storing_swapp_tags($tagsData, $swappId){

        // number of iterations tracker
        $tracker = 0;

        // loop through all tags
        foreach($tagsData as $tag) {
            // set entity values and chek if setted values are correct
            $tags = new \Model\Entity\Tags();
            $tags->setName($tag);
            $this->assertEquals($tag, $tags->getName(), "Setted and provided TAG name value in TESTING SECOND LOOP are not equal.");
            $tags->setType("SWAPP");
            $this->assertEquals("SWAPP", $tags->getType(), "Setted and provided TYPE value in TESTING SECOND LOOP are not equal.");
            $tags->setParent($swappId);
            $this->assertEquals($swappId, $tags->getParent(), "Setted and provided PARENT value in TESTING SECOND LOOP are not equal.");

            // mock storing tag and tags
            $storingMock = $this->getMockBuilder(\Model\Mapper\SwappMapper::class)
                ->disableOriginalConstructor()
                ->setMethods(array())
                ->getMock();

            // set expectations  --  in each iteration this method must be called
            $storingMock->expects($this->once())->method('storeTag')->with();
            $storingMock->expects($this->once())->method('storeTags')->with();
            // call mock methods
            $storingMock->storeTag($tags);
            $storingMock->storeTags($tags);

            // increment tracker
            $tracker++;
        }

        // check if number of iterations is equal to tags array length
        $this->assertEquals($tracker, count($tagsData));
    }


    /**
     * Data provider for testing tag loops
     */
    public function storingImagesDataProvider(){

        return [
            [[['path' => 'my/file.png', 'height' => 200, 'width' => 300]]],
            [[['path' => 'my/file.png', 'height' => 200, 'width' => 300]], [['path' => 'test/new.png', 'height' => 500, 'width' => 250]]]

        ];
    }


    /**
     * Function to test third loop (Set images)
     *
     * @dataProvider storingImagesDataProvider
     */
    public function test_storing_images($imagesData){

        // number of iterations tracker
        $tracker = 0;

        // loop through all tags
        foreach($imagesData as $img) {
            // set entity values and chek if setted values are correct
            $image = new \Model\Entity\Images();
            $image->setImage($img['path']);
            $this->assertEquals($img['path'], $image->getImage(), "Setted and provided IMAGE path value in TESTING THIRD LOOP are not equal.");
            $image->setHeight($img['height']);
            $this->assertEquals($img['height'], $image->getHeight(), "Setted and provided IMAGE height value in TESTING THIRD LOOP are not equal.");
            $image->setWidth($img['width']);
            $this->assertEquals($img['width'], $image->getWidth(), "Setted and provided IMAGE width value in TESTING THIRD LOOP are not equal.");

            // fake parent id
            $parentId = rand(1,50);
            $image->setParent($parentId);
            $this->assertEquals($parentId, $image->getParent(), "Setted and provided PARENT value in TESTING THIRD LOOP are not equal.");

            // mock storing images
            $storingMock = $this->getMockBuilder(\Model\Mapper\SwappMapper::class)
                ->disableOriginalConstructor()
                ->setMethods(array())
                ->getMock();

            // set expectations  --  in each iteration this method must be called
            $storingMock->expects($this->once())->method('storeImages')->with();
            // call mock methods
            $storingMock->storeImages($image);

            // increment tracker
            $tracker++;
        }

        // check if number of iterations is equal to images array length
        $this->assertEquals($tracker, count($imagesData));
    }

}