<?php

require __DIR__ . '/../../../vendor/autoload.php';

class GetActiveSwappsByLimitTest extends \PHPUnit\Framework\TestCase
{

    private static $connection;

    public static function setUpBeforeClass(){

        try {

            self::$connection = new PDO('mysql:dbname=Swapper;host=localhost', 'root', 'root');

            // print heading of tests
            printf("***********  TESTING GET_ACTIVE_SWAPPS_BY_LIMIT  **********");

        }catch(PDOException $exception){
            die($exception->getMessage());
        }

    }


    public function dataProvider(){

        return [
            [5, 200], // data
            [-15, 409], // invalid
            [89898998, 204],  // no data
            [0, 204], // no data
            ["14", 200] // data or no data
        ];
    }


    /**
     * Test function
     *
     * @dataProvider dataProvider
     */
    public function test_get_active_swapps_by_limit($limit){

        try {
            // set database instructions
            $sql = "SELECT 
                        id 
                    FROM swapps 
                    WHERE state='ACTIVE' 
                    ORDER BY swapps.date DESC
                    LIMIT :limit";

            // check limit
            $this->assertTrue(is_numeric($limit) && $limit > 0, "Limit is not positive number but database will be called with it.");

            $statement = self::$connection->prepare($sql);
            $statement->bindValue("limit", $limit, PDO::PARAM_INT);
            $statement->execute();
            $statement->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e){

            echo $e->getMessage() . '<br/>';
            echo "Return value isnt handled in exception.";
        }
    }

}