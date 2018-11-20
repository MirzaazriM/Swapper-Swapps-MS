<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 7/17/18
 * Time: 12:42 PM
 */

namespace Application\Controller;

use Model\Entity\ResponseBootstrap;
use Model\Service\SwappService;
use Symfony\Component\HttpFoundation\Request;
use Model\Entity\TagsCollection;
use Model\Entity\Tags;
use Helper\InputChecker;

class SwappController
{
    

    private $swapService;
    private $response;
    private $inputChecker;
    

    public function __construct(SwappService $swappService)
    {
        $this->swapService = $swappService;
        $this->response = new ResponseBootstrap();
        $this->inputChecker = new InputChecker();
    }

    
    /**
     * Get all ids of stored active swapps
     * 
     * @return ResponseBootstrap
     */
    public function getAllIds():ResponseBootstrap
    {
        $response = new ResponseBootstrap();
        
        $response = $this->swapService->getAllSwapIds();
        
        return $response;
    }

    /**
     * Get Swapp Info
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function getSwapp(Request $request):ResponseBootstrap
    {
        // get data
        $swappId = $request->get('swapp_id');
        $userId = $request->get('token_user_id');
        

        if(!empty($swappId) && !empty($userId)){
            return $this->swapService->getSwapp($swappId, $userId);
        }else{
            $this->response->setStatus(404);
            $this->response->setMessage('Bad request');
        }
        
        return $this->response;
    }
    
        
    /**
     * Get user swapps
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function getSwapps(Request $request):ResponseBootstrap {  
        // get data
        $userId = $request->get('user_id');
        $from = $request->get('from');
        $limit = $request->get('limit');
        $last = $request->get('last');
        $type = $request->get('type');
    
                 
        return $this->swapService->getSwapps($last, $from, $limit, $userId,$type);
    }
    
    
    /**
     * Get all swapps
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function getAll(Request $request):ResponseBootstrap 
    {  
        // get data
        $userId = $request->get('user_id');
        $from = $request->get('from');
        $limit = $request->get('limit');
        $last = $request->get('last');
        $state = $request->get('state');
        $tags = $request->get('tags');
        $tokenUserId = $request->get('token_user_id');
        // location
        $location = $request->get('location');
        $location = explode(",",$location);
        $liked = $request->get('liked');
        // range
        $range = $request->get('range');
        $type = $request->get('type');
        
        $tagsCollection = new TagsCollection();
        // explode tags
        foreach(explode(",",$tags) as $name){
            if(!empty($name)){
                $tag = new Tags();
                $tag->setName($name);
                $tagsCollection->addEntity($tag);
            }
        }
        
        $searchTags = [];
        if($tags){
            foreach(explode(",",$tags) as $tag){
                array_push($searchTags, $tag);
            }
        }

        
        
        if(empty($state)){
            $state = "ACTIVE";
        }
        if($state === "ACTIVE" || $state === "INACTIVE" || $state === "LIKED"){
            $state = $state;
        }else{
            $state = "ACTIVE";
        }
        
                                                
        // check if it can pass further
        if (!empty($searchTags) && !empty($limit)){
            return $this->swapService->searchSwappsByTags($searchTags, $limit);
        }
            
        if(!empty($tokenUserId)){
            return $this->swapService->getSwapps($last, $from, $limit, $userId, $state, $tagsCollection,$location,$range,$tokenUserId,$type);
        } else{
            $this->response->setStatus(404);
            $this->response->setMessage('Bad request');
        }
        
        // return response in case of failure
        return $this->response;
    }


    /**
     * Get Simple Swapps
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function getSimple(Request $request):ResponseBootstrap
    {
        // get data
        $userId = $request->get('user_id');

        if(!empty($userId)){
            return $this->swapService->getSimpleSwapps($userId);
        }else{
            $this->response->setStatus(404);
            $this->response->setMessage('Bad request');
        }

        // return response in case of failure
        return $this->response;
    }


    /**
     * Create Swapp
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function postSwapp(Request $request):ResponseBootstrap {
       
        // get data
        $data = json_decode($request->getContent(), true);
        // form paramters from data
        $images = $data['images'];
        $tags = $data['tags'];
        $swappingFor = $data['swapping_for'];
        $fromUser = $data['from_user'];
        $description = $data['description'];
        $name = $data['name'];

        // check if request is okay
        if(
                $this->inputChecker->checkInput($images, 'ARRAY',[["path"=>"","height"=>"","width"=>""]]) &&  // check image
                $this->inputChecker->checkInput($tags, 'ARRAY',[""]) && // check tags
                $this->inputChecker->checkInput($swappingFor, 'ARRAY',[""]) && // check swapping for tags
                $this->inputChecker->checkInput($fromUser, 'INT') && // check from user id
                $this->inputChecker->checkInput($description, 'STRING')  && // check description
                $this->inputChecker->checkInput($name, 'STRING') // chekc name
            ){
            return $this->swapService->createSwapp($images, $fromUser, $tags, $description, $swappingFor, $name);
        }
        // if unable to pass
        else{
            $this->response->setStatus(404);
            $this->response->setMessage('Bad request');
        }
       
        return $this->response;
    }
    

    /**
     * Delete swapp
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function deleteSwapp(Request $request):ResponseBootstrap {
        // get data
        $id = $request->get('id');

        // check values
        if(isset($id)){
            // call service function for response
            return $this->swapService->deleteSwapp($id);
        }else {
            $this->response->setStatus(404);
            $this->response->setMessage('Bad request');
        }

        // return response in case of failure
        return $this->response;
    }


    /**
     * Edit swap by id
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function putSwapp(Request $request):ResponseBootstrap {
        // get data
        $data = json_decode($request->getContent(), true);
        $id = $request->get('id');
        $images = $data['images'];
        $tags = $data['tags'];
        $swappingFor = $data['swapping_for'];
        $fromUser = $data['from_user'];
        $description = $data['description'];
        $name = $data['name'];

        // check values
        if(isset($id) && isset($images) && isset($fromUser) && isset($tags) && isset($description) && isset($swappingFor) && isset($name)){
            // call service function for response
            return $this->swapService->editSwapp($id, $images, $fromUser, $tags, $description, $swappingFor, $name);
        }else {
            $this->response->setStatus(404);
            $this->response->setMessage('Bad request');
        }

        // return response in case of failure
        return $this->response;
    }


    /**
     * Send swap request
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function postRequest(Request $request):ResponseBootstrap {
        // get data
        $data = json_decode($request->getContent(), true);

        $fromUserId = $data['from_user_id'];
        $fromSwappid = $data['from_swapp_id'];
        $toUserId = $data['to_user_id'];
        $toSwappId = $data['to_swapp_id'];

        // check values
        if(!empty($fromUserId) &&  !empty($fromSwappid) && !empty($toUserId) && !empty($toSwappId)){
            // call service function for response
            return $this->swapService->sendSwappRequest($fromUserId, $fromSwappid, $toUserId, $toSwappId);
        }else {
            $this->response->setStatus(404);
            $this->response->setMessage('Bad request');
        }

        // return response in case of failure
        return $this->response;
    }


    /**
     * Edit swap request
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function putRequest(Request $request):ResponseBootstrap {
        // get data
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];
        $state = $data['state'];

        // check values
        if(isset($id) && isset($state)){
            // call service function for response
            return $this->swapService->editSwappRequest($id, $state);
        }else {
            $this->response->setStatus(404);
            $this->response->setMessage('Bad request');
        }

        // return response in case of failure
        return $this->response;
    }


    /**
     * Accept swap
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function postAcceptance(Request $request):ResponseBootstrap {
        // get data
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];

        // check values
        if(isset($id)){
            // call service function for response
            return $this->swapService->sendAcceptance($id);
        }else {
            $this->response->setStatus(404);
            $this->response->setMessage('Bad request');
        }

        // return response in case of failure
        return $this->response;
    }


    /**
     * Change accepted swap state
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function putAcceptance(Request $request):ResponseBootstrap {
        // get data
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];
        $state = $data['state'];

        // check values
        if(isset($id) && isset($state)){
            // call service function for response
            return $this->swapService->changeAcceptance($id, $state);
        }else {
            $this->response->setStatus(404);
            $this->response->setMessage('Bad request');
        }

        // return response in case of failure
        return $this->response;
    }


    /**
     * Get swapps by search
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function getSearch(Request $request):ResponseBootstrap {  // getSearchSwapps
        // get data
        $like = $request->get('like');
        $category = $request->get('category');
        $condition = $request->get('condition');
        $location = $request->get('location');

        // create response object
        $response = new ResponseBootstrap();

        // check values
        if(isset($like) or isset($category) or isset($condition) or isset($location)){
            // call service function for response
            return $this->swapService->searchSwapps($like, $condition, $category, $location);
        }else {
            $response->setStatus(404);
            $response->setMessage('Bad request');
        }

        // return response in case of failure
        return $response;
    }


    /**
     * Get total number of swapps
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function getTotal(Request $request):ResponseBootstrap {  // getTotalSwapps
        // create new response object
        $response = new ResponseBootstrap();

        // check if response is empty
        if (!empty($response)){
            return $this->swapService->getTotalSwapps();
        } else {
            $response->setStatus(406);
            $response->setMessage('Invalid parameters');
        }
    }


    /**
     * Like swap
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function postLike(Request $request):ResponseBootstrap {
        // get data
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];
        $userId = $data['user_id'];

        // check values
        if(!empty($id) && !empty($userId)){
            // call service function for response
            return $this->swapService->likeSwap($id, $userId);
        }else {
            $this->response->setStatus(404);
            $this->response->setMessage('Bad request');
        }

        // return response in case of failure
        return $this->response;
    }


    /**
     * Delete swap
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function deleteDelete(Request $request):ResponseBootstrap
    {
        // get id from request
        $id = $request->get('id');

        // check values
        if (!empty($id)){
            return $this->swapService->deleteSwapp($id);
        } else {
            $this->response->setStatus(404);
            $this->response->setMessage('Bad request');
        }

    }

}