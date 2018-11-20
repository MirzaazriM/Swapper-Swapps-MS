<?php

require __DIR__ . '/../../../vendor/autoload.php';

class DeleteSwappTest extends \PHPUnit\Framework\TestCase
{

    public static function setUpBeforeClass(){
        // print heading of tests
        printf("***********  TESTING DELETE SWAPP SERVICE  **********");
    }


    public function deleteSwappDataProvider(){

        return [
            [5, 200],
            [15, 304],
            ["4", 200],
            ["num", 409],
            [-5, 409]
        ];
    }


    /**
     * Function to test deleting swapp
     *
     * @dataProvider deleteSwappDataProvider
     */
    public function test_delete_swapp($id, $state){

        if($state === 409){
            $this->expectException(\Psr\Log\InvalidArgumentException::class);
        }

        // set entities
        $response = new \Model\Entity\ResponseBootstrap();
        $entity = new \Model\Entity\Swap();
        $shared = new \Model\Entity\Shared();

        // create mock for mapper
        $deleteMock = $this->getMockBuilder(\Model\Mapper\SwappMapper::class)
            ->setMethods(array())
            ->disableOriginalConstructor()
            ->getMock();
        $deleteMock->expects($this->once())->method('deleteSwapp')->with()->willReturn($state);

        // call mock method and fake returned result
        $shared->setState($deleteMock->deleteSwapp($entity, $shared));

        // set response
        if($shared->getState() == 200){
            $response->setStatus(200);
            $response->setMessage('Success');
        }else {
            $response->setStatus(304);
            $response->setMessage('Not modified');
        }

        // check if response is setted approprietely
        $this->assertEquals($state, $response->getStatus(), "Expected status was " . $state . " but it returned " . $response->getStatus()
        . " - Exception not handled.");
    }
}