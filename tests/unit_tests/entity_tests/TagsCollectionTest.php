<?php

require __DIR__ . '/../../../vendor/autoload.php';

class TagsCollectionTest extends \PHPUnit\Framework\TestCase
{

    private $collection;

    public function setUp(){
        $this->collection = new \Model\Entity\TagsCollection();
    }


    public function collectionDataProvider(){

        return [
            [new \Model\Entity\Tags(), 200],
            [new \Model\Entity\Swap(), 409],
            [new \Model\Entity\Images(), 409]
        ];

    }


    /**
     * Function to test tagsCollection
     *
     * @dataProvider collectionDataProvider
     */
    public function test_tags_collection($entity, $passes){

        // it will be thrown exception for 409
        if($passes === 409){
            $this->expectException(\Psr\Log\InvalidArgumentException::class);
        }else {
            $this->collection->addEntity($entity);
            // to disable risky test
            $this->assertTrue(true);
        }

    }
}