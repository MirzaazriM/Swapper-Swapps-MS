<?php

require __DIR__ . '/../../../vendor/autoload.php';

class SearchSwappsTest extends \PHPUnit\Framework\TestCase
{

    public static function setUpBeforeClass(){
        // print heading of tests
        printf("***********  TESTING SEARCHING SWAPPS ON ES  **********");
    }

    public function searchSwappsDataProvider(){

        return [
            [["One", "Two"], 5, json_encode(['hits' => ['hits' => 'DATA']]), 200],
            [["One", "Two"], 5, json_encode(['hits' => 'DATA']), 204],
            [["One", "Two"], 5, "Error", 409]
        ];
    }


    /**
     * Function to test searching swapps on ES
     *
     * @dataProvider searchSwappsDataProvider
     */
    public function test_search_swapps($tags, $limit, $returnedData, $passes){

        if($passes === 409){
            $this->expectException(Exception::class);
        }

        try {
            $result = json_decode($returnedData, true);

            if(isset($result['hits']['hits'])){
                // in IF statement
                $this->assertEquals(200, $passes);
                return $result['hits']['hits'];
            }

            $this->assertEquals(204, $passes);
            return [];

        }catch (Exception $e){
            // check exception
            $this->assertEquals(409, $passes, "Exception thrown - no data to return.");
        }

    }
}