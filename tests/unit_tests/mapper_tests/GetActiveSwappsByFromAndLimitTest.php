<?php

require __DIR__ . '/../../../vendor/autoload.php';

class GetActiveSwappsByFromAndLimitTest extends \PHPUnit\Framework\TestCase
{

    private static $connection;

    public static function setUpBeforeClass(){

        try {

            self::$connection = new PDO('mysql:dbname=Swapper;host=localhost', 'root', 'root');

            // print heading of tests
            printf("***********  TESTING GET_ACTIVE_SWAPPS_BY_FROM_AND_LIMIT  **********");

        }catch(PDOException $exception){
            die($exception->getMessage());
        }

    }


    public function dataProvider(){

        return [
            [5, -20, 200], // data
            [-15, 10, 409], // invalid
            [89898998, 0, 204],  // no data
            [0, 50, 204], // no data
            ["14", 200] // data or no data
        ];
    }


    /**
     * Test function
     *
     * @dataProvider dataProvider
     */
    public function test_get_active_swapps_by_from_and_limit($from, $limit){

        try {
            // set database instructions
            $sql = "SELECT 
                        id 
                    FROM swapps 
                    WHERE state='ACTIVE' AND :from > swapps.id
                    ORDER BY swapps.date DESC
                    LIMIT :limit";

            // check limit
            $this->assertTrue(is_numeric($limit) && $limit > 0, "Limit is not positive number but database will be called with it.");
            $this->assertTrue(is_numeric($from) && $from > 0, "From is not positive number but database will be called with it.");

            $statement = self::$connection->prepare($sql);
            $statement->bindValue("from", $from, PDO::PARAM_INT);
            $statement->bindValue("limit", $limit, PDO::PARAM_INT);
            $statement->execute();

            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            // check if returned value from database is an array
            $this->assertTrue(is_array($result));

        } catch (PDOException $e){
            echo $e->getMessage() . '<br/>';
            echo "Return value isnt handled in exception.";
        }

        return $result;
    }

}