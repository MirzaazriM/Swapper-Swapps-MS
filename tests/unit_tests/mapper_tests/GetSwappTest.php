<?php

require __DIR__ . '/../../../vendor/autoload.php';

class GetSwappTest extends \PHPUnit\Framework\TestCase
{

    private static $connection;

    public static function setUpBeforeClass()
    {
        // print heading of tests
        printf("***********  TESTING GET_SWAPP  ********** \n");
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

//
//        return [
//            [$tag, 200],
//            [$tag, 204],
//        ];
    }


    /**
     * Test inserting tag
     *
     * @dataProvider dataProvider
     */
    public function test_get_swapp($shared, $swapp, $passes){

        try{
            // check if id set
            $this->assertTrue(is_numeric($swapp->getId()) && $swapp->getId() > 0, "ID isnt set but database is called.");

            $sql = "SELECT * FROM swapps WHERE id = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute(
                [
                    $swapp->getId()
                ]
            );
            // fetch object
            $swapp = $statement->fetchObject(Swap::class);

            // check if returned object is of type class
            $this->assertInstanceOf(\Model\Entity\Tags::class, $swapp, "Returned object isnt instance of Swapp class.");

        }catch (PDOException $e){
            echo "\n Exception not handled.";
        }

        return $swapp;
    }
}