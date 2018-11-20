<?php

require __DIR__ . '/../../vendor/autoload.php';

class PostTest extends \PHPUnit\Framework\TestCase
{

    protected static $http;

    /**
     * Function which will be called at creation of the class and contains properties which will be shared among all test functions
     */
    public static function setUpBeforeClass() {
        // create new guzzle object for each test
        self::$http = new GuzzleHttp\Client(['base_uri' => 'http://swapper-swap:8889']);

        // print statement to mark current test
        printf(" **********   TESTING POST   ********** \n");
    }


    /**
     * Providing data for testing like swapp
     */
    public function likeSwappDataProvider(){

        // scenarios data
        // 2 means that response to request should be 200 or 304
        // 3 means that response to request should be 304
        // 4 means that response to request should be 404
        return [
            '1. case' => [1, 5, 2],
            '1a. case' => [1, 5, 3],
            '2a. case' => ["2", 12, 2],
            '2b. case' => [3, "6", 2],
            '3. case' => [0, 10, 4],
            '3a. case' => [11, 0, 4],
            '4. case' => [-20, 25, 4],
            '4a. case' => [20, -25, 4],
            '5. case' => ["One", 8, 4],
            '5a. case' => [8, "Two", 4],
            '6. case' => [null, 12, 4],
            '6a. case' => [15, null, 4],
            '7. case' => [new \Model\Entity\Swap(), 80, 4],
            '7a. case' => [19, new \Model\Entity\Swap(), 4],
            '8. case' => [array(2, 5), 20, 4],
            '8a. case' => [1, array(3), 4]
        ];
    }


    /**
     * Testing like swapp
     *
     * @dataProvider likeSwappDataProvider
     */
    public function test_like_swapp($id, $userId, $passes){

        try {

            // make request
            $response = self::$http->post('/swapp/like',
                [
                    \GuzzleHttp\RequestOptions::JSON => [
                        'id' => $id,
                        'user_id' => $userId
                    ]
                ]);

            // set status code to its own variable
            $code = $response->getStatusCode();

            // check if status code is 200 or 304
            if($passes === 2){
                $this->assertContains($code, [200, 304], "Test for 'LIKE SWAP' passed but it didnt returned 200 or 304 code.");
            }else if($passes === 3){
                $this->assertEquals(304, $code, "Test for  'LIKE SWAP' should return 304 code but it didnt. It returned " . $code . ".");
            }

            // check if contoller called service with bad data
            $this->assertTrue(is_numeric($id), "ID wasnt numeric but request passed through controller. It didnt return 404 Bad request.");
            $this->assertTrue($id > 0, "ID wasnt positive number but request passed through controller. It didnt return 404 Bad request.");
            $this->assertTrue(is_numeric($userId), "USERID wasnt numeric but request passed through controller. It didnt return 404 Bad request.");
            $this->assertTrue($userId > 0, "USERID wasnt numeric but request passed through controller. It didnt return 404 Bad request.");

        }catch(\GuzzleHttp\Exception\ClientException $e){
            // check if this test really should thrown an exception
            $this->assertEquals(4, $passes, "Test for 'LIKE SWAP' should passed but it returned 404.");
        }

    }


    /**
     * Data provider for function which test swapp acceptance route
     */
    public function swappAcceptanceDataProvider(){

        // scenarios data
        // 2 means that response to request should be 200 or 304
        // 3 means that response to request needs to be 304
        // 4 means that response to request should be 404
        return [
            "1, case: " => [5, 2],
            "1a. case: " => [5, 3],
            "2. case: " => ["12", 2],
            "3. case: " => ["num4", 4],
            "5. case: " => [-38, 4],
            "6. case: " => [null, 4],
            "7. case: " => ["Text", 4]
        ];
    }


    /**
     * Function for testing swapp acceptance route
     *
     * @dataProvider swappAcceptanceDataProvider
     */
    public function test_swapp_acceptance($id, $passes){

        try {

            // make request
            $response = self::$http->post('/swapp/acceptance',
                [
                    \GuzzleHttp\RequestOptions::JSON => [
                        'id' => $id
                    ]
                ]);

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
     * Data provider for function which test swapp request route
     */
    public function swappRequestDataProvider(){

        // scenarios data
        // 2 means that response to request should be 200 or 304
        // 3 means that response to request needs to be 304
        // 4 means that response to request should be 404
        return [
            "1. case: " => [5, 10, 15, 20, 2],
            "1a. case: " => [5, 10, 15, 20, 3],
            "2. case: " => ["12", 6, 8, 19, 2],
            "2a. case: " => [3, "10", 16, 80, 2],
            "2b. case: " => [15, 28, "3", 14, 2],
            "2c. case: " => [4, 5, 100, "49", 2],
            "3. case: " => ["num", 1, 2, 3, 4],
            "3a. case: " => [7, "num", 4 , 3, 4],
            "3b. case: " => [13, 1, "num", 3, 4],
            "3c. case: " => [25, 26, 27, "num", 4],
            "11. case: " => [null, 1, 2, 3, 4],
            "12. case: " => [7, null, 4 , 3, 4],
            "13. case: " => [13, 1, null, 3, 4],
            "14. case: " => [25, 26, 27, null, 4],
            "15. case: " => [-16, 1, 2, 3, 4],
            "16. case: " => [7, -40, 4 , 3, 4],
            "17. case: " => [13, 1, -63, 3, 4],
            "18. case: " => [25, 26, 27, -50, 4],
        ];
    }


    /**
     * Function for testing swapp request route
     *
     * @dataProvider swappRequestDataProvider
     */
    public function test_swapp_request($fromUserId, $fromSwappId, $toUserId, $toSwappId, $passes){

        try {

            // make request
            $response = self::$http->post('/swapp/request',
                [
                    \GuzzleHttp\RequestOptions::JSON => [
                        'from_user_id' => $fromUserId,
                        'from_swapp_id' => $fromSwappId,
                        'to_user_id' => $toUserId,
                        'to_swapp_id' => $toSwappId
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

            // check if contoller called service with non positive numeric number
            $this->assertTrue(is_numeric($fromUserId) && $fromUserId > 0, "FROM_USER_ID wasnt numeric but request passed controller and didnt returned 404 Bad Request.");
            $this->assertTrue(is_numeric($fromSwappId) && $fromSwappId > 0, "FROM_SWAPP_ID wasnt numeric but request passed controller and didnt returned 404 Bad Request.");
            $this->assertTrue(is_numeric($toUserId) && $toUserId > 0, "TO_USER_ID wasnt numeric but request passed controller and didnt returned 404 Bad Request.");
            $this->assertTrue(is_numeric($toSwappId) && $toSwappId > 0, "TO_SWAPP_ID wasnt numeric but request passed controller and didnt returned 404 Bad Request.");

        }catch(\GuzzleHttp\Exception\ClientException $e){
            // check if this test really should thrown an exception
            $this->assertEquals(4, $passes, "Test should passed but it returned 404.");
        }

    }



    /**
     * Data provider for function which test swapp create route
     */
    public function swappCreateDataProvider(){

        // scenarios data
        // 2 means that response to request should be 200 or 304
        // 3 means that response to request needs to be 304
        // 4 means that response to request should be 404
        return [
            "1. case: " => ["", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 4],
            "1a. case: " => [15, [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], [], 15, "Opis...", 4],
            "1b. case: " => ["15", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], [], 15, "Opis...", 4],
            "1c. case: " => [null, [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 4],
            "2. case: " => ["My swapp", [[]], ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 4],
            "2a. case: " => ["My swapp", ['path' => 'TEST 20', 'height' => '', 'width' => ''], ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 4],
            "2b. case: " => ["My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => ''], ['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 2],
            "2c. case: " => ["My swapp", null, ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 4],
            "3. case: " => ["My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 2], // TO PASS
            "3a. case: " => ["My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Used"], ["Mobile", "Shoes"], 15, "Opis...", 3],      // TO PASS BUT TO RETURN 304
            "3b. case: " => ["My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], null, ["Mobile", "Shoes"], 15, "Opis...", 4],
            "3c. case: " => ["My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], [5], ["Mobile", "Shoes"], 15, "Opis...", 4],
            "4. case: " => ["My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], [], 15, "Opis...", 4],
            "4a. case: " => ["My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], [20], 15, "Opis...", 4],
            "4b. case: " => ["My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], null, 15, "Opis...", 4],
            "5. case: " => ["My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], ["Mouse"], -15, "Opis...", 4],
            "5a. case: " => ["My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], ["Mouse"], "num", 5, 4],
            "5b. case: " => ["My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], ["Mouse"], null, "Opis...", 4],
            "5c. case: " => ["My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], ["Mouse"], 006, "Opis...", 2],
            "6. case: " => ["My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], ["Mouse"], 15, "", 4],
            "6a. case: " => ["My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], ["Mouse"], 15, 25, 4],
            "6b. case: " => ["My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], ["Mouse"], 15, "456", 4],
            "6c. case: " => ["My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], ["Mouse"], 15, -20, 4],
            "6d. case: " => ["My swapp", [['path' => 'TEST 20', 'height' => '', 'width' => '']], ["Tag"], ["Mouse"], 15, null, 4],
        ];
    }


    /**
     * Function for testing swapp create route
     *
     * @dataProvider swappCreateDataProvider
     */
    public function test_swapp_create($name, $images, $tags, $swapping_for, $from_user, $description, $passes){

        try {

            // make request
            $response = self::$http->post('/swapp/swapp',
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