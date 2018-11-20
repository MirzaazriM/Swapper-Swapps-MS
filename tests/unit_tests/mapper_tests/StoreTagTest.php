<?php

require __DIR__ . '/../../../vendor/autoload.php';

class StoreTagTest extends \PHPUnit\Framework\TestCase
{

    private static $connection;

    public static function setUpBeforeClass()
    {
        // print heading of tests
        printf("***********  TESTING STORE_TAG  ********** \n");
    }

    public function setUp(){

        try {

            self::$connection = new PDO('mysql:dbname=Swapper;host=localhost', 'root', 'root');

        }catch(PDOException $exception){
            die($exception->getMessage());
        }

    }


    public function dataProvider(){
        // create tag objects
        $tag = new \Model\Entity\Tags();
        $tag->setName("Test" . rand(1,30) . "Test" . rand(1,30));

        return [
            [$tag, 200],
            [$tag, 304], // if tag row is set as unique (it should be)
            [new \Model\Entity\Tags(), 304] // empty object
        ];
    }


    /**
     * Test inserting tag
     *
     * @dataProvider dataProvider
     */
    public function test_store_tag($tags, $passes){

        try{
            // begin transaction
            self::$connection->beginTransaction();

            // check if transaction started
            $this->assertEquals(1, self::$connection->inTransaction(), "Transaction not started.");

            $sql = "INSERT IGNORE INTO tags(name) VALUES(?)";
            $statement = self::$connection->prepare($sql);
            $statement->execute(
                [
                    $tags->getName()
                ]
            );

            // If insert was successfull
            if ($statement->rowCount() > 0)
            {
                // check if rowCount returns as expected
                $this->assertEquals(200, $passes, "This tag should not be inserted");

                $tags->setId(self::$connection->lastInsertId());

                // check if id set properly
                $this->assertEquals(self::$connection->lastInsertId(), $tags->getId(), "Tag ID isnt set properly.");

            }else {

                // check if rowCount returns as expected
                $this->assertEquals(304, $passes, "This tag should be inserted.");

                // duplicate tag insert
                $sql = "SELECT id FROM tags WHERE name LIKE ?";
                $statement = self::$connection->prepare($sql);
                $statement->execute(
                    [
                        '%'.trim($tags->getName()).'%'
                    ]
                );

                $tagsTemp = $statement->fetchObject(\Model\Entity\Tags::class);

                // check if returned object is of type class
                $this->assertInstanceOf(\Model\Entity\Tags::class, $tagsTemp, "Returned object isnt instance of Tags class.");

                $tags->setId($tagsTemp->getId());

                // check if id set properly
                $this->assertEquals($tagsTemp->getId(), $tags->getId(), "Tag ID isnt set properly - TagsTemp.");
            }

            //commit transaction
            self::$connection->commit();

            // check if transaction ended
            $this->assertEquals(0, self::$connection->inTransaction(), "Transaction not ended.");

        }catch (PDOException $e){
            // rollback everything in case of any failure
            self::$connection->rollBack();

            // check if transaction ended
            $this->assertEquals(0, self::$connection->inTransaction(), "Transaction not ended.");

            echo "\n die() isnt appropriate response on exception.";
        }

    }
}