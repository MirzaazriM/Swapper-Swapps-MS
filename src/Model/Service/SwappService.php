<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 7/17/18
 * Time: 12:42 PM
 */

namespace Model\Service;

use Helper\PaginationHelper;
use Helper\TagFilterHelper;
use Model\Entity\Counter;
use Model\Entity\Images;
use Model\Entity\ImagesCollection;
use Model\Entity\ResponseBootstrap;
use Model\Entity\Shared;
use Model\Entity\Swap;
use Model\Entity\SwapRequest;
use Model\Entity\Tags;
use Model\Mapper\SwappMapper;
use Model\Service\Facade\AddEditFacade;
use Model\Service\Facade\GetFacade;
use Model\Entity\TagsCollection;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Core\Handler\ESHandler;

class SwappService
{

    private $swapMapper;
    private $response;
    private $configuration;

    public function __construct(SwappMapper $swappMapper)
    {
        $this->swapMapper = $swappMapper;
        $this->configuration = $this->swapMapper->getConfiguration();
        $this->response = new ResponseBootstrap();
    }
    
    
    /**
     * Extract all active swap ids
     * 
     * @return ResponseBootstrap
     */
    public function getAllSwapIds():ResponseBootstrap
    {
        $data = $this->swapMapper->getAllIds();

        // set response
        $this->response->setStatus(200);
        $this->response->setMessage('Success');
        $this->response->setData($data);

        return $this->response;
        
    }


    /**
     * Get Swapp Info
     * 
     * @param int $swappId
     * @param int $userId
     * @return ResponseBootstrap
     */
    public function getSwapp(int $swappId, int $userId):ResponseBootstrap
    {        
        // es handler
        $esHandler = new ESHandler($this->swapMapper->getConfiguration());
                
        // get data from cache
        $finalR = $esHandler->getDataById($swappId);
                                           
        // If ES has cached users
        if (!empty($finalR)){
            $this->response->setStatus(200);
            $this->response->setMessage('Success');
            $this->response->setData($finalR);
            
            // get swapped liked state
            //$finalR['liked'] = $this->swappLikedState($userId ,$swappId);
            
        // get data and cache it
        }else {
            // set entities
            $swap = new Swap();
            $shared = new Shared();
            
            // set swap id
            $swap->setId(trim($swappId));
            $swapResponse = [];
            
            // get response
            $swapp = $this->swapMapper->getSwapp($shared,$swap);
            
            // set user id
            $user_id = $swapp->user_id;
            
            // set swap id
            $id = $swap->getId();
            
            // get images
            $imagesTemp = $this->getSwappImages((string)$swapp->getId());
            
            //serialize object
            $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
            $imagesTemp = json_decode($serializer->serialize($imagesTemp->toArray(), 'json'), true);
            
            // get tags
            $tagsTemp = $this->getSwappTags($id, 'TAG');
            
            // get swaps
            $swappingForTemp = $this->getSwappTags((string)$swapp->getId(), 'SWAPP');
            
            // validate state
            if($swapp->getState() === "ACTIVE"){
                $state = "NOT SWAPPED";
            } else {
                $state = "SWAPPED";
            }
            
            // return valid swap date  
            // $date = date("Y-m-d\TH:i:sO", strtotime($swapp->getDate()));    
            
            // form response
            array_push($swapResponse,
                [
                    "id" => (string)$swapp->getId(),
                    "images" => $imagesTemp,
                    "tags" => $tagsTemp,
                    "swapping_for" => $swappingForTemp,
                    "title" => $swapp->getName(), // name
                    "description" => $swapp->getDescription(), // description
                    "user" => $user_id, // user_id
                    "liked" => $this->swappLikedState($userId ,$swapp->getId()),// TODO
                    "date" =>$swapp->getDate(), // date
                    // "date" => $registered,
                    "state" => $state
                ]
            );
                                    
            // save data to es
            $esHandler->manipulateData($swappId, $swapResponse[0]);   
                        
            // set response
            $this->response->setStatus(200);
            $this->response->setMessage('Success');
            $this->response->setData($swapResponse);
        }
        
        // return response
        return $this->response;
    }
    
    /**
     * Get date with timezone
     *
     * @param $date
     * @param $offset
     * @return string
     */
    public function getDateRegistered($date, $offset)
    {
        $offsetTemp = new \DateTimeZone((string)$offset);
        $timeZone = new \DateTime("now",$offsetTemp);
        $registered = $timeZone->format('Y-m-d\TH:i:sO');
        
        return $registered;
    }
    
    
    /**
     * Get Swapp Liked State
     * 
     * @param int $userId
     * @param int $swappId
     * @return string
     */
    public function swappLikedState(int $userId, int $swappId):string
    {
        // set values
        $swapp = new Swap();
        $shared = new Shared();
        
        // setup entity values
        $swapp->setUserId($userId);
        $swapp->setId($swappId);
        
        // set state
        $this->swapMapper->getLikedSwappState($swapp, $shared);
        
        return $shared->getResponse()['state'];
    }
    
    
    /**
     *  Get all swapps limited
     *
     * @param int $from
     * @param int $limit
     * @return ResponseBootstrap
     */
    public function getSwapps(int $last = null, int $from = null, int $limit = null, int $userId = null, string $state = null, TagsCollection $tagCollection = null, array $location = null, int $range = null, int $tokenUserId, string $type):ResponseBootstrap 
    {        
        if(is_null($limit)){
            $limit = 10;
        }
        // es handler
        $esHandler = new ESHandler($this->swapMapper->getConfiguration());  
        
        $swap = new Swap();
        $swap->setFrom($from);
        $swap->setLimit($limit);
        $swap->setLast($last);
        $swap->setState(trim($state));
        
        $shared = new Shared();
        
        // get main swapps
        if($type === 'ALL' && $swap->getState() !== "LIKED"){
            if(is_null($userId)){
                // get all swapps
                $this->swapMapper->getSwapps($shared,$swap);
                // TODO return from last limit
            }else{
                // get all swapps by user id
                $swap->setUserId($userId);
                $this->swapMapper->getSwappsByUserId($shared,$swap);
            }
        }
        //  If liked
        else if($swap->getState() === "LIKED" &&  $type === 'MY'){
            // get liked swapps
            $swap->setUserId($tokenUserId);
            $this->swapMapper->getSwappsByUserIdLiked($shared,$swap,$tokenUserId);
        }
        // get my swapps
        else{
            // get simple swapps
            $userId = $tokenUserId;
            $swap->setUserId($userId);
            $this->swapMapper->getSwappsByUserId($shared,$swap);            
        }
            
        $realIds = [];
        if(!empty($shared->getResponse())){
            $rawSwapps = $shared->getResponse();
            foreach($rawSwapps as $swappSingle){
                array_push($realIds,$swappSingle['id']);
            }
        }
                
        // die(print_r($realIds));
        
//         // extract ids        
//         $realIds = [];
//         foreach ($swapIds as $jedan){
//             $jedan = $jedan['id'];
//             array_push($realIds, $jedan);
//         }
                        
        // get data from cache
        $data = $esHandler->getDataByIds($realIds);
                       
        // Get Data From Ids
        $swapsFinal = [];
        foreach($data as $swap){
            $swap = $swap['_source'];
            array_push($swapsFinal,$swap);
        }
                
        // Map Data To Ids
        foreach($realIds as $key=>$id){
            // echo $id."\n";
            
            $canSkip = false;
            foreach($swapsFinal as $singleSwap){
                if($id == $singleSwap['id']){
                    $singleSwap['liked'] = $this->swappLikedState($tokenUserId, $singleSwap['id']);
                    $realIds[$key] = $singleSwap;
                    $canSkip = true;
                    break;
                }
            }
            
            if($canSkip){
                continue;
            }
                        
            if(empty($swapsFinal) || !is_array($id)){ 
                    // die(print_r($this->getSwapp($id, $tokenUserId)));
                    $data = $this->getSwapp($id, $tokenUserId);
                    $data = $data->getData()['data'][0];
                    
                    

                    if(!(empty($data))){
                        $realIds[$key] = $data;
                    }
                    
            }
        }
        
        // Taf Filter System
        // $tagFilterHelper = new TagFilterHelper();
        // $swapResponse = $tagFilterHelper->tagFilter($swapResponse, $tagCollection);
        // Handle Pagination
        // $paginationHelper = new PaginationHelper();
        // $finalSwapResponse = $paginationHelper->paginationHandler($last, $from, $limit, $swapResponse);
        
        
        if((empty($realIds))){
            $this->response->setStatus(204);
            $this->response->setMessage('No Content');         
        } else {
            $this->response->setData($realIds);
            $this->response->setMessage('Success');
            $this->response->setStatus(200);
        }
         
       
        return $this->response;
    }

    
    /**
     * Search for swapps by tags
     * 
     * @param array $tags
     * @return ResponseBootstrap
     */
    public function searchSwappsByTags(array $tags, int $limit):ResponseBootstrap
    {
        // es handler
        $esHandler = new ESHandler($this->swapMapper->getConfiguration());
        
        // return elastic search
        $result = $esHandler->searchSwaps($tags, $limit);
        
        $response = [];
        foreach ($result as $item){
            array_push($response, $item['_source']);
        }

        if((empty($response))){
            $this->response->setStatus(204);
            $this->response->setMessage('No Content');
        } else {
            $this->response->setData($response);
            $this->response->setMessage('Success');
            $this->response->setStatus(200);
        }
        
        // return response
        return $this->response;
    }

    /**
     * Get Simple Swapps
     *
     * @param int $userId
     * @return ResponseBootstrap
     */
    public function getSimpleSwapps(int $userId):ResponseBootstrap
    {
        // setup entites
        $swap = new Swap();
        $shared = new Shared();
        // set entity values
        $swap->setUserId($userId);
        $swap->setState('ACTIVE');

        // mapper functionality
        $this->swapMapper->getSwappsSimple($shared, $swap);

        // get swapps
        $swapps = $shared->getResponse();
        $swapResponse = [];
        foreach($swapps as $swapp){

            // form response
            array_push($swapResponse,
                [
                    "id" => $swapp['id'],
                    "title" => $swapp['name'], // name
                    "description" => $swapp['description'] // description
                ]
            );

        }
        
        $this->response->setStatus(200);
        $this->response->setMessage('Success');
        $this->response->setData($swapResponse);

        return $this->response;
    }

    

    /**
     * Get Swapp Images
     *
     * TODO REMOVE TO HELPER CALSS
     *
     * @param int $swappId
     * @return array
     */
    public function getSwappImages(int $swappId):ImagesCollection
    {
        $swap = new Swap();
        $swap->setId($swappId);
        $shared = new Shared();

        $imagesCollection = $this->swapMapper->getSwappImages($swap,$shared);
        
        return $imagesCollection;
    }
    
    
    /**
     * Remove Parent From Images
     * 
     * @param ImagesCollection $imagesCollection
     * @return array
     */
    public function removeParentFromImages(ImagesCollection $imagesCollection):array
    {
        return [];
    }


    /**
     * Get Swapp Tags
     *
     * @param int $swappId
     * @return array
     */
    public function getSwappTags(int $swappId, string $type):array
    {

        $tags = new Tags();
        $tags->setParent($swappId);
        $tags->setType($type);
        $shared = new Shared();
        $this->swapMapper->getSwappTags($tags,$shared);

        $tagsTemp = [];
        foreach($shared->getResponse() as $tags){
                array_push(
                    $tagsTemp,
                    [
                        "id" => $tags['id'],
                        "name" => $tags['name'],
                        "type" => $tags['type']
                    ]
                );
        }

        return $tagsTemp;
    }

    
    
    /**
     * Get user Info
     * 
     * @param Int $id
     * @return array
     */
    public function getUserInfo(Int $id):array
    {
        // create guzzle client and call MS for data
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $this->configuration['users_url'] . '/users/profile?id=' . $id, []);
        
        
        //die( $this->configuration['users_url'] . '/users/profile?id=' . $id);
        
        // set data to variable
        $data = json_decode($res->getBody()->getContents(), true)['data'];
        
                   
        //die($data);
        return [
            "id" => (int)$data['id'],
            "socialId" => $data['socialId'],
            "name" => $data['name'],
            "surname" => $data['surname'],
            "email" => $data['email'],
            "location" => $data['location'],
            "type" => $data['type'],
            "image" => $data['image'],
            "badge" => $data['badge'],
            "rating" => 1,
            "registered" => $data['registered']
        ];
    }


    /**
     * Create swapp
     *
     * @param array $images
     * @param $fromUser
     * @param array $tags
     * @param string $description
     * @param array $swappingFor
     * @return ResponseBootstrap
     */
    public function createSwapp(array $images, int $fromUser, array $tags, string $description, array $swappingFor, string $name):ResponseBootstrap {
        
        // Create Response Object
        $response = new ResponseBootstrap();

        // Store User
        $swap = new Swap();
        $swap->setName($name);
        $swap->setDescription($description);
        $swap->setUserId($fromUser);
        $this->swapMapper->storeBasicInfo($swap);

        // Store Tags
        foreach($tags as $tag) {
            $tags = new Tags();
            $tags->setName($tag);
            $tags->setType("TAG");
            $tags->setParent($swap->getId());
            // store tags
            $this->swapMapper->storeTag($tags);
            // store swapp tags
            $this->swapMapper->storeTags($tags);
        }
        
        // Store Swapp For Tags
        foreach($swappingFor as $tag) {
            $tags = new Tags();
            $tags->setName($tag);
            $tags->setType("SWAPP");
            $tags->setParent($swap->getId());
            // store tags
            $this->swapMapper->storeTag($tags);
            // store swapp tags
            $this->swapMapper->storeTags($tags);
        }

        // Store Images
        foreach($images as $image) {
            $images = new Images();
            $images->setHeight($image['height']);
            $images->setWidth($image['width']);
            $images->setImage($image['path']);
            $images->setParent($swap->getId());
            $this->swapMapper->storeImages($images);
        }

        $response->setStatus(200);
        $response->setMessage('Swapp successfully stored');
        $response->setData(['swap_id' => $swap->getId()]);

        // return response
        return $response;
    }


    /**
     * Delete swapp
     *
     * @param int $id
     * @return ResponseBootstrap
     */
    public function deleteSwapp(int $id):ResponseBootstrap {
        // create response object
        $response = new ResponseBootstrap();

        // create entity and set its values
        $entity = new Swap();
        $entity->setId($id);

        // create shared entity for response
        $shared = new Shared();

        // call mapper
        $this->swapMapper->deleteSwapp($entity, $shared);

        // check mapper response and set real response
        if($shared->getState() == 200){
            $response->setStatus(200);
            $response->setMessage('Success');
        }else {
            $response->setStatus(304);
            $response->setMessage('Not modified');
        }

        // return response
        return $response;
    }


    /**
     * Edit swapp
     *
     * @param int $id
     * @param array $images
     * @param $fromUser
     * @param array $tags
     * @param string $description
     * @param array $swappingFor
     * @return ResponseBootstrap
     */
    public function editSwapp(int $id, array $images, $fromUser, array $tags, string $description, array $swappingFor, string $name):ResponseBootstrap {
        // create response object
        $response = new ResponseBootstrap();
        
        // es handler
        $esHandler = new ESHandler($this->swapMapper->getConfiguration());

        // store User
        $swap = new Swap();
        $swap->setName($name);
        $swap->setDescription($description);
        $swap->setUserId($fromUser);
        $swap->setId($id);
        $this->swapMapper->editBasicInfo($swap);

        // clear tags
        $this->clearTags($id);

        // clear images
        $this->clearImages($id);

        // Store Tags
        foreach($tags as $tag) {
            $tags = new Tags();
            $tags->setName($tag);
            $tags->setType("TAG");
            $tags->setParent($swap->getId());
            // store tags
            $this->swapMapper->storeTag($tags);
            // store swapp tags
            $this->swapMapper->storeTags($tags);
        }
        
        // Store Swapp For Tags
        foreach($swappingFor as $tag) {
            $tags = new Tags();
            $tags->setName($tag);
            $tags->setType("SWAPP");
            $tags->setParent($swap->getId());
            // store tags
            $this->swapMapper->storeTag($tags);
            // store swapp tags
            $this->swapMapper->storeTags($tags);
        }

        // store images
        foreach($images as $image) {
            $images = new Images();
            $images->setHeight($image['height']);
            $images->setWidth($image['width']);
            $images->setImage($image['path']);
            $images->setParent($swap->getId());
            $this->swapMapper->storeImages($images);
        }
        
        // delete old cached swap data
        $esHandler->deleteDataById($id);
        
        $response->setStatus(200);
        $response->setMessage('Swapp successfully stored');

        // return response
        return $response;
    }


    /**
     * Clear Tags
     *
     * @param int $id
     */
    public function clearTags(int $id)
    {
        // clear tags
        $tags = new Tags();
        $tags->setParent($id);
        $this->swapMapper->deleteTags($tags);
    }


    /**
     * Clear Images
     *
     * @param int $id
     */
    public function clearImages(int $id)
    {
        // clear images
        $images = new Images();
        $images->setParent($id);
        $this->swapMapper->deleteImages($images);
    }


    /**
     * Send Swapp Request
     *
     * @param int $fromUserId
     * @param int $fromSwappid
     * @param int $toUserId
     * @param int $toSwappId
     * @return ResponseBootstrap
     */
    public function sendSwappRequest(int $fromUserId, int $fromSwappid, int $toUserId, int $toSwappId):ResponseBootstrap {
        // create response object
        $response = new ResponseBootstrap();

        // create entity and set its values
        $swappRequest = new SwapRequest();
        $shared = new Shared();
        $swappRequest->setFromUser($fromUserId);
        $swappRequest->setSwappThis($fromSwappid);
        $swappRequest->setToUser($toUserId);
        $swappRequest->setSwappFor($toSwappId);

        // call mapper
        $mapperResponse = $this->swapMapper->sendSwappRequest($swappRequest, $shared);

        // check mapper reponse and set real response
        if($mapperResponse->getState() == 200){
            $response->setStatus(200);
            $response->setMessage('Success');
        }else {
            $response->setStatus(304);
            $response->setMessage('Not modified');
        }

        // return response
        return $response;
    }


    /**
     * Accept swapp
     *
     * @param int $id
     * @return ResponseBootstrap
     */
    public function sendAcceptance(int $id):ResponseBootstrap {
        // create response object
        $response = new ResponseBootstrap();

        // create entity and set its values
        $entity = new SwapRequest();
        $entity->setId($id);

        // create shared entity for response
        $shared = new Shared();

        // call mapper
        $mapperResponse = $this->swapMapper->sendSwappAcceptance($entity, $shared);

        // and send message to the user
        // --- message handler ---

        // check mapper reponse and set real response
        if($mapperResponse->getState() == 200){
            $response->setStatus(200);
            $response->setMessage('Success');
        }else {
            $response->setStatus(304);
            $response->setMessage('Not modified');
        }

        // return response
        return $response;
    }


    /**
     * Change swapp acceptance state
     *
     * @param int $id
     * @param string $state
     * @return ResponseBootstrap
     */
    public function changeAcceptance(int $id, string $state):ResponseBootstrap {
        // create response object
        $response = new ResponseBootstrap();

        // create entity and set its values
        $entity = new SwapRequest();
        $entity->setId($id);
        $entity->setState($state);

        // create shared entity for response
        $shared = new Shared();

        // call mapper   ????
        $mapperResponse = $this->swapMapper->changeSwappRequestState($entity, $shared);


        // check mapper reponse and set real response
        if($mapperResponse->getState() == 200){
            $response->setStatus(200);
            $response->setMessage('Success');
        }else {
            $response->setStatus(304);
            $response->setMessage('Not modified');
        }

        // return response
        return $response;
    }


    /**
     * Edit swap request
     *
     * @param int $id
     * @param string $state
     * @return ResponseBootstrap
     */
    public function editSwappRequest(int $id, string $state):ResponseBootstrap {
        // create response object
        $response = new ResponseBootstrap();

        // create entity and set its values
        $entity = new SwapRequest();
        $entity->setId($id);
        $entity->setState($state);

        // create shared entity for response
        $shared = new Shared();

        // call mapper
        $mapperResponse = $this->swapMapper->editSwappRequest($entity, $shared);

        // and send notification
        //  --- notification handler ---

        // check mapper reponse and set real response
        if($mapperResponse->getState() == 200){
            $response->setStatus(200);
            $response->setMessage('Success');
        }else {
            $response->setStatus(304);
            $response->setMessage('Not modified');
        }

        // return response
        return $response;
    }


    /**
     * Get total number of swapps
     *
     * @return ResponseBootstrap
     */
    public function getTotalSwapps():ResponseBootstrap {
        // create response object
        $response = new ResponseBootstrap();

        // create counter
        $counter = new Counter();

        // call mapper
        $this->swapMapper->getTotalSwapps($counter);

        // set response
        $response->setStatus(200);
        $response->setMessage('Success');
        $response->setData([
            'total_swapps' => $counter->getCount()
        ]);

        // return response
        return $response;
    }


    /**
     * Get swapps by search
     *
     * @param string $like
     * @param string $condition
     * @param string $category
     * @param string $location
     * @return ResponseBootstrap
     */
    public function searchSwapps(string $like = null, string $condition = null, string $category = null, string $location = null):ResponseBootstrap {
        // create response object
        $response = new ResponseBootstrap();

        // create facade object and call its functions
        $facade = new GetFacade(null, null, null, $like, $location, $category, $condition, $this->swapMapper);
        $data = $facade->handleData();

        // check mapper reponse and set real response
        if(!empty($data)){
            $response->setStatus(200);
            $response->setMessage('Success');
            $response->setData(
                $data
            );
        }else {
            $response->setStatus(204);
            $response->setMessage('No content');
        }
        // return response
        return $response;
    }


    /**
     * Like swapp
     *
     * @param int $id
     * @param int $userId
     * @return ResponseBootstrap
     */
    public function likeSwap(int $id, int $userId):ResponseBootstrap {
        // create shared entity for response
        $shared = new Shared();
        // create entity and set its values
        $swapp = new Swap();
        $swapp->setId($id);
        $swapp->setUserId($userId);
        
        // call mapper 
        $mapperResponse = $this->swapMapper->likeSwapp($swapp, $shared);

        // check mapper reponse and set real response
        if($mapperResponse->getState() == 200){
            $this->response->setStatus(200);
            $this->response->setMessage('Success');
        }else {
            $this->response->setStatus(304);
            $this->response->setMessage('Not modified');
        }

        // return response
        return $this->response;
    }

}