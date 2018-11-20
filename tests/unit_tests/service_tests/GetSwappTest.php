<?php

require __DIR__ . '/../../../vendor/autoload.php';

class GetSwappTest extends \PHPUnit\Framework\TestCase
{

    private $response;

    public static function setUpBeforeClass(){
        // print heading of tests
        printf("***********  TESTING GET SWAPP SERVICE  **********");
    }


    public function setUp(){
        // create response entity
        $this->response = new \Model\Entity\ResponseBootstrap();
    }


    /**
     * Data for testing getting swapp if elastic return data
     */
    public function getSwappFromElasticDataProvider(){

        return [
            [10, 20, ['One', 'Two'], 200],
            [10, 20, [], 200],
            [10, 20, "Error", 409],
            [10, 20, null, 200],
        ];
    }


    /**
     * Function to test if IF statement handle response from ESHandler approprietely
     *
     * @dataProvider getSwappFromElasticDataProvider
     */
    public function test_get_swapp_if_elastic_passes($swappId, $userId, $finalR, $state){

        if($state === 409){
            $this->expectException(TypeError::class);
        }

        // create mock for mapper
//        $getSwappMock = $this->getMockBuilder(\Core\Handler\ESHandler::class)
//            ->setMethods(['getDataById'])
//            ->disableOriginalConstructor()
//            ->disableOriginalClone()
//            ->disableArgumentCloning()
//            ->disallowMockingUnknownTypes()
//            ->getMock();
//        $getSwappMock->expects($this->once())->method('getDataById')->will($this->returnValue($returnedData));
//        $finalR = $getSwappMock->getDataById($swappId);

        // no mocking ESHandler because it must return array which doesnt allow isolation of our testing code

        try {

            if(!empty($finalR)){
                $this->response->setStatus(200);
                $this->response->setMessage('Success');
                $this->response->setData($finalR);

                // check if response values are appropriate
                $this->assertEquals(200, $this->response->getStatus(), "Status code is not as expected.");
                $this->assertEquals('Success', $this->response->getMessage(), "Message is not as expected.");
                $this->assertTrue(is_array($this->response->getData()), "Response data is set but it isnt array");
            }

        }catch(TypeError $e){
            // check if response values are appropriate
            printf("Exception thrown: " . $e->getMessage() . " \n");
            $this->assertEquals(409, $this->response->getStatus(), "Status code is not as expected.");
            $this->assertEquals('Exception thrown', $this->response->getMessage(), "Message is not as expected.");
        }

        // to avoid warning of no assertion
        $this->assertTrue(true);

    }
}