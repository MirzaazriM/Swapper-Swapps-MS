<?php

require __DIR__ . '/../../../vendor/autoload.php';

class GetFacadeTest extends \PHPUnit\Framework\TestCase
{

    public static function setUpBeforeClass(){
        // print heading of tests
        printf("***********  TESTING GET FACADE (setEntity function)  **********");
    }


    public function setEntityDataProvider(){

        return [
            '1. case: ' => [1, null, null, [5,6,7], "IF"],
            '2. case: ' => [null, 5, 10, [5,6,7], "ELSE IF"],
            '3. case: ' => [null, null, 10, [5,6,7], "ELSE"],
            '4. case: ' => [1, 5, 10, [5,6,7], "ELSE IF"],
            '5. case: ' => [null, null, null, [5,6,7], "ELSE"],

        ];
    }


    /**
     * Function to test setEntity in GetFacade class
     *
     * @dataProvider setEntityDataProvider
     */
    public function test_set_entity($id, $from, $limit, $returnedData, $passes){

        // set entity
        $entity = new \Model\Entity\Swap();

        // mock mapper class
        $mapper = $this->getMockBuilder(\Model\Mapper\SwappMapper::class)
            ->setMethods(array())
            ->disableOriginalConstructor()
            ->getMock();
        $mapper->expects($this->once())->willReturn($passes);

        if(!is_null($id)){

            // check if right method is calles
            $this->assertEquals("IF", $passes, "Wanted method was " . $passes . " not IF");

            // get swapps by user id
            $data = $mapper->getMySwapps($entity); // function not exist

        }else if(!is_null($from) && !is_null($limit)) {

            // check if right method is calles
            $this->assertEquals("ELSE IF", $passes, "Wanted method was " . $passes . " not ELSE IF");

            // get all swapps between from and from + limit
            $data = $mapper->getSwapps($entity);

        }else {

            // check if right method is calles
            $this->assertEquals("ELSE", $passes, "Wanted method was " . $passes . " not ELSE");

            // search swapps by given parametar/s
            $data = $mapper->searchSwapps($entity);
        }

        return $data;

    }

}