<?php

require __DIR__ . '/../../../vendor/autoload.php';

class SearchSwappsByTagsTest extends \PHPUnit\Framework\TestCase
{

    private $response;

    public static function setUpBeforeClass(){
        // print heading of tests
        printf("***********  TESTING SEARCH_SWAPPS_BY_TAGS SERVICE  **********");
    }


    public function setUp(){
        $this->response = new \Model\Entity\ResponseBootstrap();
    }


    public function searchSwappsDataProvider(){

        return [
            [["One", "Two", "Three"], 5, [['_source' => "Test"]], 200], // with returned results
            [["One", "Two", "Three"], 5, [], 204], // with empty results
            [["One", "Two", "Three"], 5, "Error", 409]
        ];
    }


    /**
     * Function to test searching swapps
     *
     * @dataProvider searchSwappsDataProvider
     */
    public function test_search_swapps($tags, $limit, $returnedData, $passes){

        if($passes === 409){
            $this->expectException(TypeError::class);
        }

        try {

            // create mock for facade object
            $esMock = $this->getMockBuilder(\Core\Handler\ESHandler::class)
                ->disableOriginalConstructor()
                ->setMethods(['searchSwapps'])
                ->getMock();
            $esMock->expects($this->once())->method('searchSwapps')->willReturn($returnedData);

            // return es data
            $result = $esMock->searchSwapps($tags, $limit);

            $response = [];
            foreach ($result as $item){
                array_push($response, $item['_source']);
            }

            if((empty($response))){
                $this->response->setStatus(204);
                $this->response->setMessage('No Content');
            } else {
                $this->response->setData($response);
                $this->response->setMessage('Success');
                $this->response->setStatus(200);
            }

            // check if status code is set appropriately
            if($passes === 200){
                $this->assertEquals($this->response->getStatus(), $passes);
            }else {
                $this->assertEquals($this->response->getStatus(), $passes);
            }


        }catch (Exception $e){
            // check if this scenario should failed
            if($passes !== 409){
                $this->fail("This scenario shouldnt fail.");
            }

            // check if response is ok
            $this->assertEquals(409, $this->response->getStatus(), "In case of exception status code is set to "
                . $this->response->getStatus() . " Exception not handled.");
        }

    }

}