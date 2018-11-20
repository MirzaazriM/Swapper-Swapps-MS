<?php

require __DIR__ . '/../../../vendor/autoload.php';

class GetAllSwappIdsTest extends \PHPUnit\Framework\TestCase
{

    private $response;

    public static function setUpBeforeClass(){
        // print heading of tests
        printf("***********  TESTING GET_ALL_SWAPPS_IDS SERVICE  **********");
    }


    public function setUp()
    {
        $this->response = new \Model\Entity\ResponseBootstrap();
    }


    public function getAllSwappIdsDataProvider(){

        return [
            '1. case:' => [[5,6,7]],
            '2. case: ' => [[]]
        ];
    }


    /**
     * Function to test get all swapp ids service
     *
     * @dataProvider getAllSwappIdsDataProvider
     */
    public function test_get_all_swapp_ids($mapperResponse){

        // mock mapper response
        $mapper = $this->createMock(\Model\Mapper\SwappMapper::class);
        $mapper->method('getAllIds')->willReturn($mapperResponse);
        $data = $mapper->getAllIds();

        // set response
        $this->response->setStatus(200);
        $this->response->setMessage('Success');
        $this->response->setData($data);

        // check array state
        if(!empty($data)){
            // status code is 200
            $this->assertEquals(200, $this->response->getStatus(), "Returned value is not an empty array, but status code is set to " . $this->response->getStatus());
            // message
            $this->assertEquals("Success", $this->response->getMessage(), "Status code is 200 but message is set to " . $this->response->getMessage());
        }else {
            // status code is 204
            $this->assertEquals(204, $this->response->getStatus(), "Returned value is an empty array, but status code is set to " . $this->response->getStatus());
            // message
            $this->assertEquals("No Content", $this->response->getMessage(), "Status code is 204 but message is set to " . $this->response->getMessage());
        }

    }
}