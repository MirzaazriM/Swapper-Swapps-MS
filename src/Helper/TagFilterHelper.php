<?php
/**
 * Created by PhpStorm.
 * User: arslanhajdarevic
 * Date: 25/09/2018
 * Time: 16:03
 */

namespace Helper;


use Model\Entity\TagsCollection;

class TagFilterHelper
{

    /**
     * Filter By Tags
     *
     * @param array $swapResponse
     * @param TagsCollection $tagCollection
     * @return array
     */
    public function tagFilter(array $swapResponse, TagsCollection $tagCollection):array
    {
        $swapResponseTemp = $swapResponse;
        if($tagCollection->count() > 0){
            $swapResponse = [];
            // loop feed profiles
            foreach($swapResponseTemp as $swap){
                // loop though tags
                foreach($swap['tags'] as $tag){
                    $tag = $tag['name'];

                    // lopp though suplied tags
                    foreach($tagCollection->toArray() as $filtrationTag){
                        $filtrationTag = $filtrationTag->getName();
                        similar_text($filtrationTag, $tag, $perc);

                        if($perc > 60){
                            if(!in_array($swap, $swapResponse, true)){
                                array_push($swapResponse, $swap);
                            }
                        }
                    }
                }
            }
        }

        return $swapResponse;
    }

}