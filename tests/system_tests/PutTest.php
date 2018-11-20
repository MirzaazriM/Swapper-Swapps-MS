<?php

require __DIR__ . '/../../vendor/autoload.php';

class PutTest extends \PHPUnit\Framework\TestCase
{

    protected static $http;

    /**
     * Function which will be called at creation of the class and contains properties which will be shared among all test functions
     */
    public static function setUpBeforeClass() {
        // create new guzzle object for each test
        self::$http = new GuzzleHttp\Client(['base_uri' => 'http://swapper-swap:8889']);

        // print statement to mark current test
        printf(" **********   TESTING PUT   ********** \n");
    }


    /**
     * Data provider for function which test swapp request route
     */
    public function changeSwappRequestDataProvider(){

        // scenarios data
        // 2 means that response to request should be 200 or 304
        // 3 means that response to request needs to be 304
        // 4 means that response to request should be 404
        return [
            "1. case: " => [5, "ACCEPTED", 2],
            "1b. case: " => [5, "ACCEPTED", 3],
            "2. case: " => ["8", "DECLINED", 2],
            "3. case: " => ["num", "ACCEPTED", 4],
            "4. case: " => [null, "DECLINED", 4],
            "5. case: " => [array(5,1122), "ACCEPTED", 4],
            "6. case: " => [12, "declined", 2],
            "7. case: " => [12, "Declined", 2],
            "8. case: " => [12, 5, 4],
            "9. case: " => [12, "UNKNOWN", 4],
        ];
    }


    /**
     * Function for testing editing swapp request route
     *
     * @dataProvider changeSwappRequestDataProvider
     */
    public function test_editing_swapp_request($id, $state, $passes){

        try {

            // make request
            $response = self::$http->put('/swapp/request',
                [
                    \GuzzleHttp\RequestOptions::JSON => [
                        'id' => $id,
                        'state' => $state
                    ]
                ]);

            // set status code to its own variable
            $code = $response->getStatusCode();

            // check if status code is 200 or 304
            if($passes === 2){
                // if the right code is returned
                $this->assertContains($code, [200, 304], "Test passed but it didnt returned 200 or 304 code.");
            }else if($passes === 3){
                $this->assertEquals(304, $code, "Test should return 304 code but it didnt. It returned " . $code . ".");
            }

            // check if contoller called service with invalid data
            $this->assertTrue(is_numeric($id) && $id > 0, "ID provided isnt number or it isnt positive but request passed through controller. Returned code is " . $code);
            $possibleStates = ["ACCEPTED", "DECLINED"];
            $this->assertContains($state, $possibleStates, "State provided to the service: " . $state . " is invalid but request passed through controller. It must be ACCEPTED or DECLINED. Returned code is " . $code);

        }catch(\GuzzleHttp\Exception\ClientException $e){
            // check if this test really should thrown an exception
            $this->assertEquals(4, $passes, "Test should passed but it returned 404.");
        }

    }


    /**
     * Data provider for function which test swapp acceptance route
     */
    public function changeSwappAcceptanceDataProvider(){

        // scenarios data
        // 2 means that response to request should be 200 or 304
        // 3 means that response to request needs to be 304
        // 4 means that response to request should be 404
        return [
            "1. case: " => [34, "ACCEPTED", 2],
            "1b. case: " => [34, "ACCEPTED", 3],
            "2. case: " => ["8", "DECLINED", 2],
            "3. case: " => ["num", "ACCEPTED", 4],
            "4. case: " => [null, "DECLINED", 4],
            "5. case: " => [array(5,1122), "ACCEPTED", 4],
            "6. case: " => [12, "declined", 2],
            "7. case: " => [66, "Declined", 2],
            "8. case: " => [12, 5, 4],
            "9. case: " => [12, "UNKNOWN", 4],
        ];
    }


    /**
     * Function for testing swapp acceptance route
     *
     * @dataProvider changeSwappAcceptanceDataProvider
     */
    public function test_editing_swapp_acceptance($id, $state, $passes){

        try {

            // make request
            $response = self::$http->put('/swapp/acceptance',
                [
                    \GuzzleHttp\RequestOptions::JSON => [
                        'id' => $id,  // this is a message_id
                        'state' => $state
                    ]
                ]);

            // set status code to its own variable
            $code = $response->getStatusCode();

            // check if status code is 200 or 304
            if($passes === 2){
                $this->assertContains($code, [200, 304], "Test passed but it didnt returned 200 or 304 code.");
            }else if($passes === 3){
                $this->assertEquals(304, $code, "Test for should return 304 code but it didnt. It returned " . $code . ".");
            }

            // check if contoller called service with invalid data
            $this->assertTrue(is_numeric($id) && $id > 0, "ID provided isnt number or it isnt positive but request passed through controller. Returned code is " . $code);
            $possibleStates = ["ACCEPTED", "DECLINED"];
            $this->assertContains($state, $possibleStates, "State provided to the service: " . $state . " is invalid but request passed through controller. It must be ACCEPTED or DECLINED. Returned code is " . $code);


        }catch(\GuzzleHttp\Exception\ClientException $e){
            // check if this test really should thrown an exception
            $this->assertEquals(4, $passes, "Test for id *** " . $id . " *** should passed but it returned 404.");
        }

    }


    /**
     * Data provider for function which test swapp edit route
     */
    public function swappEditDataProvider(){

        // scenarios data
        // 2 means that response to request should be 200 or 304
        // 3 means that response to request needs to be 304
        // 4 means that response to request should be 404
        return [
            "1. case: " => [40, "", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 4],
            "1a. case: " => [40, 15, [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], [], 15, "Opis...", 4],
            "1b. case: " => [40, "15", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], [], 15, "Opis...", 4],
            "1c. case: " => [40, null, [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 4],
            "2. case: " => [40, "My swapp", [[]], ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 4],
            "2a. case: " => [40, "My swapp", ['path' => 'TEST 20', 'height' => '', 'width' => ''], ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 4],
            "2b. case: " => [40, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => ''], ['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 2],
            "2c. case: " => [40, "My swapp", null, ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 4],
            "3. case: " => [40, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 2], // TO PASS
            "3a. case: " => [40, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 3],      // TO PASS BUT TO RETURN 304
            "3b. case: " => [40, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], null, ["Mobile", "Shoes"], 15, "Opis...", 4],
            "3c. case: " => [40, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], [5], ["Mobile", "Shoes"], 15, "Opis...", 4],
            "4. case: " => [40, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], [], 15, "Opis...", 4],
            "4a. case: " => [40, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], [20], 15, "Opis...", 4],
            "4b. case: " => [40, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], null, 15, "Opis...", 4],
            "5. case: " => [40, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], ["Mouse"], -15, "Opis...", 4],
            "5a. case: " => [40, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], ["Mouse"], "num", 5, 4],
            "5b. case: " => [40, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], ["Mouse"], null, "Opis...", 4],
            "5c. case: " => [40, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], ["Mouse"], 006, "Opis...", 2],
            "6. case: " => [40, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], ["Mouse"], 15, "", 4],
            "6a. case: " => [40, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], ["Mouse"], 15, 25, 4],
            "6b. case: " => [40, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], ["Mouse"], 15, "456", 4],
            "6c. case: " => [40, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], ["Mouse"], 15, -20, 4],
            "6d. case: " => [40, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], ["Mouse"], 15, null, 4],
            "7. case: " => [40, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 2],
            "7a. case: " => ["41", "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 2],
            "7b. case: " => [null, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 4],
            "7c. case: " => ["text", "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 4],
            "7d. case: " => [-9, "My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 4],
        ];
    }


    /**
     * Function for testing swapp edit route
     *
     * @dataProvider swappEditDataProvider
     */
    public function test_swapp_edit($id, $name, $images, $tags, $swapping_for, $from_user, $description, $passes){

        try {

            // make request
            $response = self::$http->put('/swapp/swapp?id=' . $id,
                [
                    \GuzzleHttp\RequestOptions::JSON => [
                        'name' => $name,
                        'images' => $images,
                        'tags' => $tags,
                        'swapping_for' => $swapping_for,
                        'from_user' => $from_user,
                        'description' => $description
                    ]
                ]);

            // set status code to its own variable
            $code = $response->getStatusCode();

            // check if status code is 200 or 304
            if($passes === 2){
                $this->assertContains($code, [200, 304], "Test passed but it didnt returned 200 or 304 code.");
            }else if($passes === 3){
                $this->assertEquals(304, $code, "Test should return 304 code but it didnt. It returned " . $code . ".");
            }

            // check if contoller called service with invalid data
            $this->assertTrue(is_numeric($id) && $id > 0, "ID value isnt numer or it isnt positive, but request passed controller and returned . " . $code);
            $this->assertTrue(is_string($name) && strlen($name) > 0, "NAME value isnt string, but request passed through controller and returned " . $code);
            $this->assertTrue(is_array($images) && !empty($images), "IMAGES value isnt array or it is empty but request passed controller and returned " . $code);

            // check images array values
            $this->assertTrue(isset($images[0]['path']) && isset($images[0]['width']) && isset($images[0]['height']), "IMAGES object values are invalid but request passes the controller and returned " . $code);

            $this->assertTrue(is_array($tags) && !empty($tags), "TAGS value isnt array or it is empty but request passed controller and returned " . $code);
            $this->assertTrue(is_array($swapping_for) && !empty($swapping_for), "SWAPPING_FOR value isnt array or it is empty but request passed controller and returned " . $code);
            $this->assertTrue(is_numeric($from_user) && $from_user > 0, "FROM_USER value isnt numeric or positive but request passed through controller and returned " . $code);
            $this->assertTrue(is_string($description) && !is_numeric($description) && strlen($description) > 0, "DESCRIPTION value isnt string, but request passed through controller and returned " . $code);

        }catch(\GuzzleHttp\Exception\ClientException $e){
            printf("Notice: Request caused 404 Bad request.");

            // check if this test really should thrown an exception
            $this->assertEquals(4, $passes, "Test should passed but it returned 404.");
        }

    }

}