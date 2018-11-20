<?php

require __DIR__ . '/../../../vendor/autoload.php';

class GetSimpleSwappsTest extends \PHPUnit\Framework\TestCase
{

    private $response;

    public static function setUpBeforeClass(){
        // print heading of tests
        printf("***********  TESTING GET_SIMPLE_SWAPPS SERVICE  **********");
    }


    public function setUp(){
        $this->response = new \Model\Entity\ResponseBootstrap();
    }

    /**
     * Data provider
     */
    public function getSimpleDataProvider(){

        return [
            // first
            [[
                [
                    'id' => 5,
                    'name' => 'My swapp',
                    'description' => 'Desc...'
                ],
                [
                    'id' => 125,
                    'name' => 'Mobile',
                    'description' => 'Desc...'
                ],
            ]],
            // second
            [[]],
            // third
            [[null]],
            // fourth
            [["Error"]]
        ];
    }


    /**
     * Function for testing getSimpleSwapps
     *
     * @dataProvider getSimpleDataProvider
     */
    public function test_get_simple_swapps($returnedData){

        // set entities
        $entity = new \Model\Entity\Swap();
        $shared = new \Model\Entity\Shared();

        // create mock for mapper
        $getMock = $this->getMockBuilder(\Model\Mapper\SwappMapper::class)
            ->setMethods(array())
            ->disableOriginalConstructor()
            ->getMock();
        $getMock->expects($this->once())->method('getSwappsSimple')->with()->willReturn($returnedData);

        // get swapps
        $swapps = $getMock->getSwappsSimple($shared, $entity);
        $swapResponse = [];

        // loop through fetched data
        foreach($swapps as $swapp){

            // form response
            array_push($swapResponse,
                [
                    "id" => $swapp['id'],
                    "title" => $swapp['name'],
                    "description" => $swapp['description']
                ]
            );
        }

        // set response
        $this->response->setStatus(200);
        $this->response->setMessage('Success');
        $this->response->setData($swapResponse);

        // check if response is setted approprietly
        $this->assertTrue($this->response->getStatus() == 200 && empty($this->response->getData()), "Data is empty but returned code is 200. ");

        if($this->response->getStatus() === 200){
            $this->assertEquals("array", gettype($this->response->getData()['data']),
                "Response does not contains data array.");
            $this->assertNotEmpty($this->response->getData()['data'], "Array is empty but returned code is 200.");
        }

    }
}