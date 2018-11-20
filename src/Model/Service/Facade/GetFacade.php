<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 8/1/18
 * Time: 4:30 PM
 */

namespace Model\Service\Facade;


use Model\Entity\Swap;

class GetFacade
{

    private $id;
    private $from;
    private $limit;
    private $connection;
    private $like;
    private $location;
    private $category;
    private $condition;
    private $configuration;


    public function __construct(int $id = null, int $from = null, int $limit = null, string $like = null, string $location = null, string $category = null, string $condition = null, $connection)
    {
        $this->id = $id;
        $this->from = $from;
        $this->limit = $limit;
        $this->like = $like;
        $this->location = $location;
        $this->category = $category;
        $this->condition = $condition;
        $this->connection = $connection;
        $this->configuration = $connection->getConfiguration();

    }


    public function handleData() {
        // get data
        $data = $this->setEntity();

        // convert data
        $data = $this->convertor($data);

        // return data
        return $data;
    }


    public function setEntity() {
        // create entity and set its values
        $entity = new Swap();
        $entity->setId($this->id);
        $entity->setFrom($this->from);
        $entity->setLimit($this->limit);
        $entity->setName($this->like);
        $entity->setLocation($this->location);
        $entity->setCategory($this->category);
        $entity->setCondition($this->condition);

        // check which function to call
        if(!is_null($this->id)){
            // get swapps by user id
            $data = $this->connection->getMySwapps($entity);
        }else if(!is_null($this->from) && !is_null($this->limit)) {
            // get all swapps between from and from + limit
            $data = $this->connection->getSwapps($entity);
        }else {
            // search swapps by given parametar/s
            $data = $this->connection->searchSwapps($entity);
        }

        return $data;
    }


    public function convertor($data) {
        // conver data
        $formatedData = [];
        for($i = 0; $i < count($data); $i++){
            // set array values for each collection item
            $formatedData[$i]['id'] = $data[$i]->getId();
            $formatedData[$i]['name'] = $data[$i]->getName();
            $formatedData[$i]['description'] = $data[$i]->getDescription();
            $formatedData[$i]['likes'] = $data[$i]->getLikes();
            $formatedData[$i]['user_id'] = $data[$i]->getFromUser();
            $formatedData[$i]['user_name'] = $data[$i]->getUserName();
            $formatedData[$i]['user_surname'] = $data[$i]->getUserSurname();
            $formatedData[$i]['user_image'] = $data[$i]->getUserImage();
            $formatedData[$i]['user_location'] = $data[$i]->getLocation();

            // loop through images
            $images = $data[$i]->getImages();
            $imagesTemp = [];
            for($j = 0; $j < count($images); $j++){
                $imagesTemp[$j]['id'] = $images[$j]->getId();
                $imagesTemp[$j]['source'] = $this->configuration['system_url'] . '/' . $images[$j]->getSource();
            }

            $formatedData[$i]['images'] = $imagesTemp;

            // loop through tags
            $tags = $data[$i]->getTags();
            $tagsTemp = [];
            for($j = 0; $j < count($tags); $j++) {
                $tagsTemp[$j]['id'] = $tags[$j]->getId();
                $tagsTemp[$j]['name'] = $tags[$j]->getName();
                $tagsTemp[$j]['type'] = $tags[$j]->getType();
                $tagsTemp[$j]['tags_id'] = $tags[$j]->getTagId();
            }

            $formatedData[$i]['tags'] = $tagsTemp;
        }

        return $formatedData;
    }
}