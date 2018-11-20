<?php
/**
 * Created by PhpStorm.
 * User: arslanhajdarevic
 * Date: 10/09/2018
 * Time: 14:40
 */

namespace Helper;


class PaginationHelper
{

    /**
     * Handle Pagination
     *
     * @param int $from
     * @param int $limit
     * @param array $dataResponse
     * @return array
     */
    public function paginationHandler(int $last = null ,int $from = null, int $limit = null, array $dataResponse):array
    {

        $finalDataResponse = [];
        $count = 0;
        foreach($dataResponse as $dataResponse){

            // handle from
            if(empty($last)){
                if($from > $dataResponse['id']){
                    // handle limit
                    if($count < $limit){
                        array_push($finalDataResponse, $dataResponse);
                        $count ++;
                    }
                }
            }
            // handle last
            if(empty($from)){
                if($last < $dataResponse['id']){
                    // handle limit
                    if($count < $limit){
                        array_push($finalDataResponse, $dataResponse);
                        $count ++;
                    }
                }
            }

            if(empty($last) && empty($from)){

                if($count < $limit){
                    if(!is_array($finalDataResponse)){
                        array_push($finalDataResponse, $dataResponse);
                        $count ++;
                    }

                }
            }

        }

        return $finalDataResponse;
    }

}