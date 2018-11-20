<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 8/1/18
 * Time: 3:42 PM
 */

namespace Model\Service\Facade;


use Model\Entity\Images;
use Model\Entity\ImagesCollection;
use Model\Entity\Shared;
use Model\Entity\Swap;
use Model\Entity\Tags;
use Model\Entity\TagsCollection;

class AddEditFacade
{

    private $id;
    private $images;
    private $fromUser;
    private $tags;
    private $description;
    private $swappingFor;
    private $name;
    private $connection;
    private $imagesCollection;
    private $tagsCollection;
    private $swappingForCollection;


    public function __construct(int $id = null, array $images, int $fromUser, array $tags, string $description, array $swappingFor, string $name, $connection)
    {
       $this->id = $id;
       $this->images = $images;
       $this->fromUser = $fromUser;
       $this->tags = $tags;
       $this->description = $description;
       $this->swappingFor = $swappingFor;
       $this->name = $name;
       $this->connection = $connection;
    }

    public function handleCreatingAndEditingSwapp() {
        // make neccessary adjustments
        $this->convertArraysToCollections();

        // set entity and fetch data from mapper
        $data = $this->setEntity();

        // return response
        return $data;
    }


    public function convertArraysToCollections() {
        // convert images
        $this->imagesCollection = new ImagesCollection();
        for($i = 0; $i < count($this->images); $i++){
            // create entity
            $image = new Images();
            $image->setSource($this->images[$i]);

            // add to collection
            $this->imagesCollection->addEntity($image);
        }

        // convert tags
        $this->tagsCollection = new TagsCollection();
        for($i = 0; $i < count($this->tags); $i++){
            // create entity
            $tag = new Tags();
            //$tag->setName($this->tags[$i]['name']);
            $tag->setType('TAG');
            $tag->setName($this->tags[$i]);

            // add to collection
            $this->tagsCollection->addEntity($tag);
        }

        // convert swapping for
        $this->swappingForCollection = new TagsCollection();
        for($i = 0; $i < count($this->swappingFor); $i++){
            // create entity
            $tag = new Tags();
            //$tag->setName($this->swappingFor[$i]['name']);
            $tag->setType('SWAPP');
            $tag->setName($this->swappingFor[$i]);

            // add to collection
            $this->swappingForCollection->addEntity($tag);
        }

    }


    public function setEntity() {
        // create entity and set its values
        $entity = new Swap();
        $entity->setId($this->id);
        $entity->setImages($this->imagesCollection);
        $entity->setFromUser($this->fromUser);
        $entity->setTags($this->tagsCollection);
        $entity->setDescription($this->description);
        $entity->setName($this->name);
        $entity->setSwappingFor($this->swappingForCollection);

        // create shared entity for response
        $shared = new Shared();

        // call mapper function
        $data = $this->callMapperForData($entity, $shared);

        return $data;
    }


    public function callMapperForData($entity, $shared) {
        // fetch response
        if(!is_null($this->id)) {
            // call edit function
            $data = $this->connection->editSwapp($entity, $shared);
        }else {
            // call add function
            $data = $this->connection->createSwapp($entity, $shared);
        }

        return $data;
    }
}