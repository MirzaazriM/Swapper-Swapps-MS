<?php

require __DIR__ . '/../../vendor/autoload.php';

class DeleteTest extends \PHPUnit\Framework\TestCase
{

    protected static $http;

    /**
     * Function which will be called at creation of the class and contains properties which will be shared among all test functions
     */
    public static function setUpBeforeClass() {
        // create new guzzle object for each test
        self::$http = new GuzzleHttp\Client(['base_uri' => 'http://swapper-swap:8889']);

        // print statement to mark current test
        printf(" **********   TESTING DELETE   ********** \n");
    }


    /**
     * Data provider for function which test delete swapp route
     */
    public function deleteSwappDataProvider(){

        // scenarios data
        // 2 means that response to request should be 200 or 304
        // 3 means that response to request needs to be 304
        // 4 means that response to request should be 404
        return [
            "1. case: " => [39, 2],
            "1a. case: " => [39, 3],
            "2. case: " => ["40", 2],
            "3. case: " => ["17r", 4],
            "4. case: " => [-38, 4],
            "5. case: " => [null, 4],
            "6. case: " => ["Text", 4]
        ];
    }


    /**
     * Function for testing deleting swapp route
     *
     * @dataProvider deleteSwappDataProvider
     */
    public function test_delete_swapp($id, $passes){

        try {

            // make request and save it to response
            $response = self::$http->request('DELETE', '/swapp/swapp?id=' . $id);
            // set status code to its own variable
            $code = $response->getStatusCode();

            // check if status code is 200 or 304
            if($passes === 2){
                $this->assertContains($code, [200, 304], "Test for id *** " . $id . " *** passed but it didnt returned 200 or 304 code.");
            }else if($passes === 3){
                $this->assertEquals(304, $code, "Test for id *** " . $id . " *** should return 304 code but it didnt. It returned " . $code . ".");
            }

            // check if contoller called service with non positive numeric number
            $this->assertTrue(is_numeric($id) && $id > 0, "Test for id *** " . $id . " ***: The id wasnt numeric or it had negative value, 
            but it passed through the controller and returned status code of " . $code . ". It should return 404 Bad Request.");

        }catch(\GuzzleHttp\Exception\ClientException $e){
            // check if this test really should thrown an exception
            $this->assertEquals(4, $passes, "Test for id *** " . $id . " *** should passed but it returned 404.");
        }

    }


    /**
     * Destroy Guzzle object after all test had finished
     */
    public static function tearDownAfterClass(){
        self::$http = null;
    }
}