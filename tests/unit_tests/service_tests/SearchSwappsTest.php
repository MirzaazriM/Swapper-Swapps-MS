<?php

require __DIR__ . '/../../../vendor/autoload.php';

class SearchSwappsTest extends \PHPUnit\Framework\TestCase
{

    private $response;

    public static function setUpBeforeClass(){
        // print heading of tests
        printf("***********  TESTING SEARCHING SWAPPS SERVICE  **********");
    }


    public function setUp(){
        $this->response = new \Model\Entity\ResponseBootstrap();
    }


    public function searchSwappsDataProvider(){

        return [
            ["like", "cond", "cat", "sarajevo", ["One", "Two"], 200],
            ["like", "cond", "cat", "sarajevo", [5,8,9], 200],
            ["like", "cond", "cat", "sarajevo", 10, 409],
            ["like", "cond", "cat", "sarajevo", "Error", 409]
        ];
    }


    /**
     * Function to test searching swapps
     *
     * @dataProvider searchSwappsDataProvider
     */
    public function test_search_swapps($like, $condition, $category, $location, $returnedData, $passes){

        if($passes === 409){
            $this->expectException(TypeError::class);
        }

        try {

            // create mock for facade object
            $facadeMock = $this->getMockBuilder(\Model\Service\Facade\GetFacade::class)
                ->disableOriginalConstructor()
                ->setMethods(array())
                ->getMock();
            $facadeMock->expects($this->once())->method('handleData')->willReturn($returnedData);
            $data = $facadeMock->handleData();

            // check data and set response
            if(!empty($data)){
                // check if response is appropriate depending on data provided
                $this->assertTrue(is_array($data), "Data value wasnt array with elements but it passed as success.");

                $this->response->setStatus(200);
                $this->response->setMessage('Success');
                $this->response->setData(
                    $data
                );
            }else {
                $this->response->setStatus(204);
                $this->response->setMessage('No content');
            }

        }catch (TypeError $e){
            // check if this scenario should failed
            if($passes !== 409){
                $this->fail("This scenario shouldnt fail.");
            }
            // check if response is ok
            $this->assertEquals(409, $this->response->getStatus(), "In case of exception status code is set to " . $this->response->getStatus());
        }


    }
}