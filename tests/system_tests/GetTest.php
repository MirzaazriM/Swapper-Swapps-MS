<?php

require __DIR__ . '/../../vendor/autoload.php';

class GetTest extends \PHPUnit\Framework\TestCase
{

    protected static $http;

    /**
     * Function which will be called at creation of the class and contains properties which will be shared among all test functions
     */
    public static function setUpBeforeClass() {
        // create new guzzle object for each test
        self::$http = new GuzzleHttp\Client(['base_uri' => 'http://swapper-swap:8889']);

        // print statement to mark current test
        printf(" **********   TESTING GET   ********** \n");
    }


    /**
     * Function to test getAllIds route
     */
    public function test_get_all_ids(){

        // make request and save it to response
        $response = self::$http->request('GET', '/swapp/allids');
        // set status code to its own variable
        $code = $response->getStatusCode();

        // check if status code is 200 or 204
        $this->assertContains($code, [200, 204], "Request passed but status code isnt 200 or 204");

        // set response to variable
        $obj = json_decode($response->getBody()->getContents());
        // check if returned object has 'data' attribute
        $this->assertObjectHasAttribute("data", $obj, "Returned response object doesnt have 'data' attribute. Returned code is " . $code);
        // returned data must be an array
        $this->assertSame(true, is_array($obj->data), "Returned object data isnt an array. Returned code is " . $code);
        // returned data cant be empty
        $this->assertNotEmpty($obj->data, "Returned data array is empty. Returned code is " . $code);
    }



    /**
     * Function to provide data for testing get swapp
     */
    public function getSwappDataProvider(){

        // scenarios data
        // 200 means that response to request should be 200 or 204
        // 204 means that response to request needs to be 204
        // 404 means that response to request should be 404
        return [
            "1. case: " => [46, 88, 200],  // this swapp exists
            "2. case: " => [45, 88, 204],  // this swapp does not exist
            "3. case: " => [-46, 5, 404],
            "4. case: " => [46, -15, 404],
            "5. case: " => ["61", 25, 200],
            "6. case: " => ["num", 30, 404],
            "7. case: " => [50, "num", 404],
            "8. case: " => [null, 30, 404],
            "9. case: " => [50, null, 404],
            "10. case: " => [null, null, 404],
        ];
    }


    /**
     * Function for testing getting single swapp
     *
     * @dataProvider getSwappDataProvider
     */
    public function test_get_swapp($id, $tokenUserId, $passes){

        try {

            // make request and save it to response
            $response = self::$http->request('GET', '/swapp/swapp?swapp_id=' . $id . '&token_user_id=' . $tokenUserId);
            // set status code to its own variable
            $code = $response->getStatusCode();

            // check if status code is 200 or 204
            $this->assertContains($code, [200, 204], "Request passed but status code isnt 200 or 204");

            // check if request passed with invalid swapp_id parametar
            $this->assertTrue(is_numeric($id) && $id > 0, "Request passed through controller with swapp_id of " . $id ." and returned " . $code);
            // check if request passed with invalid user_id parametar
            $this->assertTrue(is_numeric($tokenUserId) && $tokenUserId > 0, "Request passed through controller with token_user_id of " . $tokenUserId ." and returned " . $code);

            // check if status code is appropriate
            if($passes === 200){
               // $this->assertContains($code, [200, 204], "Expected returned code was 200 or 204, but returned " . $code);
            }else if($passes === 204) {
                $this->assertTrue(204 === $code, "Expected returned code was 204, but returned " . $code);
            }else if($passes === 404){
                printf("This test should return 404, but instead it passed through controller and returned " . $code . " \n");
            }

            // set tests according to returned status code
            if($code === 200){
                // set response to variable
                $obj = json_decode($response->getBody()->getContents());
                // check if returned object has 'data' attribute
                $this->assertObjectHasAttribute("data", $obj, "Returned response object doesnt have 'data' attribute. Returned code is " . $code);
                // returned data must be an array
                $this->assertSame(true, is_array($obj->data), "Returned object data isnt an array. Returned code is " . $code);
                // returned data cant be empty
                $this->assertNotEmpty($obj->data, "Returned data array is empty. Returned code is " . $code);
                // array contains objects
                $this->assertSame("object", gettype($obj->data[0]), "Returned array doesnt contain objects. Returned code is " . $code);

            }else if ($code === 204){
                // check if message is appropriate
                $this->assertEquals('No Content', $response->getReasonPhrase());
            }

        }catch (\GuzzleHttp\Exception\ClientException $e){
            // check if this test really should thrown an exception
            $this->assertEquals(404, $passes, "Test should passed but it returned 404.");
        }

    }


    /**
     * Function to provide data for testing get swapp
     */
    public function getAllSwappsDataProvider(){

        // scenarios data
        // 200 means that response to request should be 200 or 204
        // 204 means that response to request needs to be 204
        // 404 means that response to request should be 404
        return [
            // from  -  limit  -  last  -  type  -  token_user_id  - user_id  -  state  -  tags  -  location  -  range  -  -  problem  -  passes
            "1. case: " => ['/swapp/all?token_user_id=345&limit=5&type=MY', 'No', 200],
            "2. case: " => ['/swapp/all?token_user_id=345&limit=-5&type=MY', 'Bad limit', 404],
            "3. case: " => ['/swapp/all?token_user_id=345&limit=num&type=MY', 'Bad limit', 404],
            "4. case: " => ['/swapp/all?token_user_id=-20&limit=5&type=ALL', 'Bad token id', 404],
            "5. case: " => ['/swapp/all?token_user_id=num&limit=5&type=MY', 'Bad token id', 404],
            "6. case: " => ['/swapp/all?token_user_id=345&limit=5&type=test', 'Bad type', 404],
            "7. case: " => ['/swapp/all?token_user_id=345&limit=5&type=all', 'No', 200],
            "8. case: " => ['/swapp/all?token_user_id=345&limit=5&type=MY&from=-6', 'Bad from', 404],
            "9. case: " => ['/swapp/all?token_user_id=345&limit=5&type=all&from=num', 'Bad from', 404],
            "10. case: " => ['/swapp/all?token_user_id=345&limit=5&type=MY&las=-6', 'Bad last', 404],
            "11. case: " => ['/swapp/all?token_user_id=345&limit=5&type=all&last=num', 'Bad last', 404],
            "12. case: " => ['/swapp/all?token_user_id=345&limit=5&type=MY&tags=one-two-three', 'Bad tags', 404],
            "13. case: " => ['/swapp/all?token_user_id=345&limit=5&type=MY&tags=one two three', 'Bad tags', 404],
            "14. case: " => ['/swapp/all?token_user_id=345&limit=5&type=MY&location=one-two-three', 'Bad location', 404],
            "15. case: " => ['/swapp/all?token_user_id=345&limit=5&type=MY&location=one two three', 'Bad location', 404],
            "16. case: " => ['/swapp/all?token_user_id=345&limit=5&type=MY&range=-5', 'Bad range', 404],
            "17. case: " => ['/swapp/all?token_user_id=345&limit=5&type=MY&range=num', 'Bad range', 404],
        ];
    }


    /**
     * Function for testing getting all swapps
     *
     * @dataProvider getAllSwappsDataProvider
     */
    public function test_get_all_swapps($url, $issue, $passes){

        try {

            // make request and save it to response
            $response = self::$http->request('GET', $url);
            // set status code to its own variable
            $code = $response->getStatusCode();

            // check if status code is 200 or 204
            $this->assertContains($code, [200, 204], "Request passed but status code isnt 200 or 204");

            // check if request should passed and make some assertions
            if($issue === 'No'){
                // set response to variable
                $obj = json_decode($response->getBody()->getContents());
                // check if returned object has 'data' attribute
                $this->assertObjectHasAttribute("data", $obj, "Returned response object doesnt have 'data' attribute. Returned code is " . $code);
                // returned data must be an array
                $this->assertSame(true, is_array($obj->data), "Returned object data isnt an array. Returned code is " . $code);
                // returned data cant be empty
                $this->assertNotEmpty($obj->data, "Returned data array is empty. Returned code is " . $code);
                // array contains objects
                $this->assertSame("object", gettype($obj->data[0]), "Returned array doesnt contain objects. Returned code is " . $code);
            }else if ($issue === 'Bad limit'){
                // set test to fail if exception isnt thrown
                $this->fail("Request " . $url . " with bad LIMIT value passed through controller and returned " . $code . " " . $response->getReasonPhrase());
            }else if ($issue === 'Bad token id'){
                // set test to fail if exception isnt thrown
                $this->fail("Request " . $url . " with bad TOKEN ID value passed through controller and returned " . $code . " " . $response->getReasonPhrase());
            }else if ($issue === 'Bad type'){
                // set test to fail if exception isnt thrown
                $this->fail("Request " . $url . " with bad TYPE value passed through controller and returned " . $code . " " . $response->getReasonPhrase());
            }else if ($issue === 'Bad from'){
                // set test to fail if exception isnt thrown
                $this->fail("Request " . $url . " with bad FROM value passed through controller and returned " . $code . " " . $response->getReasonPhrase());
            }else if ($issue === 'Bad last'){
                // set test to fail if exception isnt thrown
                $this->fail("Request " . $url . " with bad LAST value passed through controller and returned " . $code . " " . $response->getReasonPhrase());
            }else if ($issue === 'Bad tags'){
                // set test to fail if exception isnt thrown
                $this->fail("Request " . $url . " with bad TAGS value passed through controller and returned " . $code . " " . $response->getReasonPhrase());
            }else if ($issue === 'Bad location'){
                // set test to fail if exception isnt thrown
                $this->fail("Request " . $url . " with bad LOCATION value passed through controller and returned " . $code . " " . $response->getReasonPhrase());
            }else if ($issue === 'Bad range'){
                // set test to fail if exception isnt thrown
                $this->fail("Request " . $url . " with bad RANGE value passed through controller and returned " . $code . " " . $response->getReasonPhrase());
            }

        }catch (\GuzzleHttp\Exception\ClientException $e){
            // check if this test really should thrown an exception
            $this->assertEquals(404, $passes, "Test should passed but it returned 404.");
        }

    }



    /**
     * Function to provide data for testing get swapp
     */
    public function getSwappSimpleDataProvider(){

        // scenarios data
        // 200 means that response to request should be 200 or 304
        // 204 means that response to request needs to be 204
        // 404 means that response to request should be 404
        return [
            "1. case: " => [2, 200], // user exist
            "2. case: " => [2000, 204], // user not exist
            "3. case: " => [-10, 404],
            "4. case: " => ["Text", 404],
            "5. case: " => [null, 404],
        ];
    }


    /**
     * Function for testing getting simple swapp
     *
     * @dataProvider getSwappSimpleDataProvider
     */
    public function test_get_simple_swapp($id, $passes){

        try {

            // make request and save it to response
            $response = self::$http->request('GET', '/swapp/simple?user_id=' . $id);
            // set status code to its own variable
            $code = $response->getStatusCode();

            // check if status code is 200 or 204
            $this->assertContains($code, [200, 204], "Request passed but status code isnt 200 or 204");

            // check if request passed with invalid swapp_id parametar
            $this->assertTrue(is_numeric($id) && $id > 0, "Request (id not numeric) passed through controller with user_id of " . $id ." and returned " . $code);

            // check if status code is appropriate
            if($passes === 200){
                // $this->assertContains($code, [200, 204], "Expected returned code was 200 or 204, but returned " . $code);
            }else if($passes === 204) {
                $this->assertTrue(204 === $code, "Expected returned code was 204, but returned " . $code);
            }else if($passes === 404){
                printf("This test should return 404, but instead it passed through controller and returned " . $code . " \n");
            }

            // set tests according to returned status code
            if($code === 200){
                // set response to variable
                $obj = json_decode($response->getBody()->getContents());
                // check if returned object has 'data' attribute
                $this->assertObjectHasAttribute("data", $obj, "Returned response object doesnt have 'data' attribute. Returned code is " . $code);
                // returned data must be an array
                $this->assertSame(true, is_array($obj->data), "Returned object data isnt an array. Returned code is " . $code);
                // returned data cant be empty
                $this->assertNotEmpty($obj->data, "Returned data array is empty. Returned code is " . $code);
                // array contains objects
                $this->assertSame("object", gettype($obj->data[0]), "Returned array doesnt contain objects. Returned code is " . $code);

            }else if ($code === 204){
                // check if message is appropriate
                $this->assertEquals('No Content', $response->getReasonPhrase());
            }

        }catch (\GuzzleHttp\Exception\ClientException $e){
            // check if this test really should thrown an exception
            $this->assertEquals(404, $passes, "Test should passed but it returned 404.");
        }

    }


    /**
     * Function to test getting total number of swapps
     */
    public function test_getting_total_swapps(){

        // make request and save it to response
        $response = self::$http->request('GET', '/swapp/total');
        // set status code to its own variable
        $code = $response->getStatusCode();

        // check if status code is 200 or 204
        $this->assertContains($code, [200, 204], "Request passed but status code isnt 200 or 204");

        // set tests according to returned status code
        if($code === 200){
            // set response to variable
            $obj = json_decode($response->getBody()->getContents());
            // check if returned object has 'data' attribute
            $this->assertObjectHasAttribute("data", $obj, "Returned response object doesnt have 'data' attribute. Returned code is " . $code);
            // returned data must be an object
            $this->assertSame(true, is_object($obj->data), "Returned object data isnt an object. Returned code is " . $code);
            // check if returned 'data' object has 'total_swapps' property
            $this->assertObjectHasAttribute("total_swapps", $obj->data, "Returned data object doesnt have 'total_swapps' property. Returned code is " . $code);
        }else if ($code === 204){
            // check if message is appropriate
            $this->assertEquals('No Content', $response->getReasonPhrase());
        }

    }


    /**
     * Function to provide data for testing search swapp
     */
    public function getSearchDataProvider(){

        // scenarios data
        // 200 means that response to request should be 200 or 204
        // 204 means that response to request needs to be 204
        // 404 means that response to request should be 404
        return [
            "1. case: " => ["", "", "", "", 404],
            "2. case: " => ["text", "", "", "", 200],
            "3. case: " => ["", "text", "", "", 200],
            "4. case: " => ["", "", "text", "", 200],
            "5. case: " => ["", "", "", "text", 200],
            "6. case: " => [5, "", "", "", 404],
            "7. case: " => ["", -2, "", "", 404],
            "8. case: " => ["", "", 100, "", 404],
            "9. case: " => ["", "", "", "50", 404]
        ];
    }


    /**
     * Function for testing getting single swapp
     *
     * @dataProvider getSearchDataProvider
     */
    public function test_search_swapps($like, $category, $condition, $location, $passes){

        try {

            // make request and save it to response
            $response = self::$http->request('GET', '/swapp/search?like=' . $like . '&condition=' . $condition .
                '&category=' . $condition . '&location=' . $location);
            // set status code to its own variable
            $code = $response->getStatusCode();

            // check if status code is 200 or 204
            $this->assertContains($code, [200, 204], "Request passed but status code isnt 200 or 204");


            // check like
            if(isset($like)){
                // check if request passed with invalid like parametar
                $this->assertTrue(is_string($like) && !is_numeric($like), "Request passed through controller with LIKE of " . $like ." and returned " . $code);
            }

            // check category
            if(isset($category)){
                // check if request passed with invalid category parametar
                $this->assertTrue(is_string($category) && !is_numeric($category), "Request passed through controller with CATEGORY of " . $category ." and returned " . $code);
            }

            // check condition
            if(isset($condition)){
                // check if request passed with invalid condition parametar
                $this->assertTrue(is_string($condition) && !is_numeric($condition), "Request passed through controller with CONDITION of " . $condition ." and returned " . $code);
            }

            // check location
            if(isset($location)){
                // check if request passed with invalid location parametar
                $this->assertTrue(is_string($location) && !is_numeric($location), "Request passed through controller with LOCATION of " . $location ." and returned " . $code);
            }

            // check if status code is appropriate
            if($passes === 200){
                // $this->assertContains($code, [200, 204], "Expected returned code was 200 or 204, but returned " . $code);
            }else if($passes === 204) {
                $this->assertTrue(204 === $code, "Expected returned code was 204, but returned " . $code);
            }else if($passes === 404){
                printf("This test should return 404, but instead it passed through controller and returned " . $code . " \n");
            }

            // set tests according to returned status code
            if($code === 200){
                // set response to variable
                $obj = json_decode($response->getBody()->getContents());
                // check if returned object has 'data' attribute
                $this->assertObjectHasAttribute("data", $obj, "Returned response object doesnt have 'data' attribute. Returned code is " . $code);
                // returned data must be an array
                $this->assertSame(true, is_array($obj->data), "Returned object data isnt an array. Returned code is " . $code);
                // returned data cant be empty
                $this->assertNotEmpty($obj->data, "Returned data array is empty. Returned code is " . $code);
                // array contains objects
                $this->assertSame("object", gettype($obj->data[0]), "Returned array doesnt contain objects. Returned code is " . $code);

            }else if ($code === 204){
                // check if message is appropriate
                $this->assertEquals('No Content', $response->getReasonPhrase());
            }

        }catch (\GuzzleHttp\Exception\ClientException $e){
            // check if this test really should thrown an exception
            $this->assertEquals(404, $passes, "Test should passed but it returned 404.");
        }

    }

}