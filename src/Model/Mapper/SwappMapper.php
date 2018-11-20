<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 7/17/18
 * Time: 12:43 PM
 */

namespace Model\Mapper;

use Model\Entity\Counter;
use Model\Entity\Images;
use Model\Entity\ImagesCollection;
use Model\Entity\Shared;
use Model\Entity\Swap;
use Model\Entity\SwapCollection;
use Model\Entity\SwapRequest;
use Model\Entity\Tags;
use Model\Entity\TagsCollection;
use PDO;
use PDOException;
use Component\DataMapper;

class SwappMapper extends DataMapper
{

    public function getConfiguration(){
        return $this->configuration;
    }
    
    
    /**
     * Get all swap ids
     * 
     * @return mixed
     */
    public function getAllIds()
    {
        try {
            // set database instructions
            $sql = "SELECT
                        id
                    FROM swapps
                    WHERE state='ACTIVE'
                    ORDER BY swapps.date DESC
                    LIMIT 100";
            $statement = $this->connection->prepare($sql);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            
            $response = [];
            foreach ($result as $id){
                array_push($response, $id['id']);
            }
            
            
        } catch (PDOException $e){
            die($e->getMessage());
        }
                
        return $response;
    }
    
    /**
     * Get last swaps by limit
     * 
     * @param $limit
     */
    public function getActiveSwappsByLimit(int $limit)
    {
        try {
            // set database instructions
            $sql = "SELECT 
                        id 
                    FROM swapps 
                    WHERE state='ACTIVE' 
                    ORDER BY swapps.date DESC
                    LIMIT :limit";
            $statement = $this->connection->prepare($sql);
            $statement->bindValue("limit", $limit, PDO::PARAM_INT);
            $statement->execute();
            $statement->fetch(PDO::FETCH_ASSOC);
            
            
        } catch (PDOException $e){
            die($e->getMessage());
        }

        die(print_r($result));
        
        return $result;
    }
    
    
    /**
     * Get last swaps by last and limit
     *
     * @param int $limit
     * @param int $last
     */
    public function getActiveSwappsByLastAndLimit(int $last, int $limit)
    {
        try {
            
            // set database instructions
            $sql = "SELECT 
                        id 
                    FROM swapps 
                    WHERE state='ACTIVE' AND :last < swapps.id
                    ORDER BY swapps.date DESC
                    LIMIT :limit";
            $statement = $this->connection->prepare($sql);
            $statement->bindValue("last", $last, PDO::PARAM_INT);
            $statement->bindValue("limit", $limit, PDO::PARAM_INT);
            $statement->execute();
            
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e){
            die($e->getMessage());
        }
        
        return $result;
    }
    
    
    /**
     * Get last swaps by from and limit
     *
     * @param $limit
     * @param $from
     */
    public function getActiveSwappsByFromAndLimit(int $from, int $limit)
    {
        try {
            // set database instructions
            $sql = "SELECT 
                        id 
                    FROM swapps 
                    WHERE state='ACTIVE' AND :from > swapps.id
                    ORDER BY swapps.date DESC
                    LIMIT :limit";
            $statement = $this->connection->prepare($sql);
            $statement->bindValue("from", $from, PDO::PARAM_INT);
            $statement->bindValue("limit", $limit, PDO::PARAM_INT);
            $statement->execute();
            
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e){
            die($e->getMessage());
        }
        
        return $result;
    }


    /**
     * Store Basic Swapp Info
     *
     * @param Swap $swap
     */
    public function storeBasicInfo(Swap $swap)
    {
        try{
            // begin transaction
            $this->connection->beginTransaction();

            $sql = "INSERT INTO swapps(name, description, user_id) VALUES(?, ?, ?)";
            $statement = $this->connection->prepare($sql);
            $statement->execute(
                [
                    $swap->getName(),
                    $swap->getDescription(),
                    $swap->getUserId()
                ]
            );

            $swap->setId($this->connection->lastInsertId());

            // commit transaction
            $this->connection->commit();
        }catch (PDOException $e){
            // rollback everything in case of any failure
            $this->connection->rollBack();
        }
    }


    /**
     * Edit Basic Info
     *
     * @param Swap $swap
     */
    public function editBasicInfo(Swap $swap)
    {
        try{
            // begin transaction
            $this->connection->beginTransaction();

            $sql = "UPDATE swapps SET name = ?, description = ? WHERE  id = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute(
                [
                    $swap->getName(),
                    $swap->getDescription(),
                    $swap->getId()
                ]
            );
            
            // commit transaction
            $this->connection->commit();
        }catch (PDOException $e){
            // rollback everything in case of any failure
            $this->connection->rollBack();
        }
    }


    /**
     * Store Swapp Images
     *
     * @param Images $images
     */
    public function storeImages(Images $images)
    {
        try{
            // begin transaction
            $this->connection->beginTransaction();

            $sql = "INSERT INTO swapp_images(source, swapps_id, height, width) VALUES(?, ?, ?, ?)";
            $statement = $this->connection->prepare($sql);
            $statement->execute(
                [
                    $images->getImage(),
                    $images->getParent(),
                    $images->getHeight(),
                    $images->getWidth()
                ]
            );

            // commit transaction
            $this->connection->commit();
        }catch (PDOException $e){
            // rollback everything in case of any failure
            $this->connection->rollBack();
        }
    }


    /**
     * Delete Images
     *
     * @param Images $images
     */
    public function deleteImages(Images $images)
    {
        try{
            // begin transaction
            $this->connection->beginTransaction();

            $sql = "DELETE FROM swapp_images WHERE swapps_id = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute(
                [
                    $images->getParent(),
                ]
            );

            // commit transaction
            $this->connection->commit();
        }catch (PDOException $e){
            // rollback everything in case of any failure
            $this->connection->rollBack();
        }
    }


    /**
     * Store Swapp Tags
     *
     * @param Tags $tags
     */
    public function storeTags(Tags $tags)
    {
        try{
            // begin transaction
            $this->connection->beginTransaction();
                                    
            $sql = "INSERT INTO swapp_tags(tags_id, swapps_id, type) VALUES(?, ?, ?)";
            $statement = $this->connection->prepare($sql);
            $statement->execute(
                [
                    $tags->getId(),
                    $tags->getParent(),
                    $tags->getType()
                ]
            );

            // commit transaction
            $this->connection->commit();
        }catch (PDOException $e){
            die($e->getMessage());
            // rollback everything in case of any failure
            $this->connection->rollBack();
        }
    }
    
    
    /**
     * Store Tag
     * 
     * @param Tags $tags
     */
    public function storeTag(Tags $tags)
    {
        try{      
            // begin transaction
            $this->connection->beginTransaction();
            
            $sql = "INSERT IGNORE INTO tags(name) VALUES(?)";
            $statement = $this->connection->prepare($sql);
            $statement->execute(
                [
                    $tags->getName()
                ]
            );
                        
            // If insert was successfull
            if ($statement->rowCount() > 0)
            {
                $tags->setId($this->connection->lastInsertId());
            }else{
                // duplicate tag insert
                $sql = "SELECT id FROM tags WHERE name LIKE ?";
                $statement = $this->connection->prepare($sql);
                $statement->execute(
                    [
                        '%'.trim($tags->getName()).'%'
                    ]
                );
                
                $tagsTemp = $statement->fetchObject(Tags::class);


                $tags->setId($tagsTemp->getId());
            }
            
            //commit transaction
            $this->connection->commit();
        }catch (PDOException $e){
            // rollback everything in case of any failure
            $this->connection->rollBack();
            
            die($e->getMessage());
        }
    }


    /**
     * Delete Tags
     *
     * @param Tags $tags
     */
    public function deleteTags(Tags $tags)
    {
        try{
            // begin transaction
            $this->connection->beginTransaction();

            $sql = "DELETE FROM swapp_tags WHERE swapps_id = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute(
                [
                    $tags->getParent()
                ]
            );

            // commit transaction
            $this->connection->commit();
        }catch (PDOException $e){
            // rollback everything in case of any failure
            $this->connection->rollBack();

            die(print_r($e->getMessage()));
        }
    }


    /**
     * Get Swapp
     *
     * @param Shared $shared
     */
    public function getSwapp(Shared $shared, Swap $swapp):Swap
    {
        try{
            $sql = "SELECT * FROM swapps WHERE id = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute(
                [
                    $swapp->getId()
                ]
                );
            // fetch object
            $swapp = $statement->fetchObject(Swap::class);
        }catch (PDOException $e){
            
        }
        return $swapp;
    }


    /**
     * Get Swapps
     *
     * @param Shared $shared
     */
    public function getSwapps(Shared $shared, Swap $swapp)
    {
        try{          
            $sql = "SELECT * FROM swapps AS s WHERE s.state = :state ";
            
            $state = $swapp->getState();
            $limit = $swapp->getLimit();
            $from = $swapp->getFrom();
            $last = $swapp->getLast();
                        
            // if from and limit
            if($swapp->getLimit() && $swapp->getFrom() && !$swapp->getLast()){
                $sql = $sql."AND s.id < :id ORDER BY s.date DESC LIMIT :limit";
                $statement = $this->connection->prepare($sql);
                $statement->bindParam("id", $from);
                $statement->bindParam("state", $state);
                $statement->bindParam("limit", $limit,PDO::PARAM_INT);
                $statement->execute();
            }
            // if last and limit
            else if($swapp->getLimit() && !$swapp->getFrom() && $swapp->getLast()){
                $sql = $sql."AND s.id > :id  ORDER BY s.date DESC LIMIT :limit";
                $statement = $this->connection->prepare($sql);
                $statement->bindParam("id", $last);
                $statement->bindParam("state", $state);
                $statement->bindParam("limit", $limit,PDO::PARAM_INT);
                $statement->execute();
            }
            // only limit
            else{
                $sql = $sql."ORDER BY s.date DESC LIMIT :limit";
                $statement = $this->connection->prepare($sql);
                $statement->bindParam("state", $state);
                $statement->bindParam("limit", $limit,PDO::PARAM_INT);
                $statement->execute();
            }
            

            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            
        }catch (PDOException $e){
            // rollback everything in case of any failure
        }

        $shared->setResponse($results);
    }
    
    
    /**
     * Get Swapps By User Id
     *
     * @param Shared $shared
     */
    public function getSwappsByUserId(Shared $shared, Swap $swapp)
    {
        try{
            $sql = "SELECT * FROM swapps AS s WHERE s.state = :state AND user_id = :user_id ";
            
            $state = $swapp->getState();
            $limit = $swapp->getLimit();
            $from = $swapp->getFrom();
            $last = $swapp->getLast();
            $userId = $swapp->getUserId();
            
            // if from and limit
            if($swapp->getLimit() && $swapp->getFrom() && !$swapp->getLast()){
                $sql = $sql."AND s.id < :id ORDER BY s.date DESC LIMIT :limit";
                $statement = $this->connection->prepare($sql);
                $statement->bindParam("user_id", $userId,PDO::PARAM_INT);
                $statement->bindParam("id", $from,PDO::PARAM_INT);
                $statement->bindParam("state", $state);
                $statement->bindParam("limit", $limit,PDO::PARAM_INT);
                $statement->execute();
            }
            // if last and limit
            else if($swapp->getLimit() && !$swapp->getFrom() && $swapp->getLast()){
                $sql = $sql."AND s.id > :id  ORDER BY s.date DESC LIMIT :limit";
                $statement = $this->connection->prepare($sql);
                $statement->bindParam("user_id", $userId,PDO::PARAM_INT);
                $statement->bindParam("id", $last,PDO::PARAM_INT);
                $statement->bindParam("state", $state);
                $statement->bindParam("limit", $limit,PDO::PARAM_INT);
                $statement->execute();
            }
            // only limit
            else{
                $sql = $sql."ORDER BY s.date DESC LIMIT :limit";
                $statement = $this->connection->prepare($sql);
                $statement->bindParam("user_id", $userId,PDO::PARAM_INT);
                $statement->bindParam("state", $state);
                $statement->bindParam("limit", $limit,PDO::PARAM_INT);
                $statement->execute();
            }
            
            
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            
 
        }catch (PDOException $e){
            // rollback everything in case of any failure
            die(print_r($e->getMessage()));
        }
        
        $shared->setResponse($results);
    }
    
    
    /**
     * Get Swapps By User Liked
     *
     * @param Shared $shared
     */
    public function getSwappsByUserIdLiked(Shared $shared, Swap $swapp)
    {
        try{
            $sql = "SELECT s.* 
            FROM swapps AS s 
            INNER JOIN swapp_likes AS sl ON sl.swapp_id = s.id
            WHERE sl.user_id = :user_id ";
            
            $state = $swapp->getState();
            $limit = $swapp->getLimit();
            $from = $swapp->getFrom();
            $last = $swapp->getLast();
            $userId = $swapp->getUserId();
            
            // if from and limit
            if($swapp->getLimit() && $swapp->getFrom() && !$swapp->getLast()){
                $sql = $sql."AND s.id < :id ORDER BY s.date DESC LIMIT :limit";
                $statement = $this->connection->prepare($sql);
                $statement->bindParam("user_id", $userId,PDO::PARAM_INT);
                $statement->bindParam("id", $from,PDO::PARAM_INT);
                $statement->bindParam("limit", $limit,PDO::PARAM_INT);
                $statement->execute();
            }
            // if last and limit
            else if($swapp->getLimit() && !$swapp->getFrom() && $swapp->getLast()){
                $sql = $sql."AND s.id > :id  ORDER BY s.date DESC LIMIT :limit";
                $statement = $this->connection->prepare($sql);
                $statement->bindParam("user_id", $userId,PDO::PARAM_INT);
                $statement->bindParam("id", $last,PDO::PARAM_INT);
                $statement->bindParam("limit", $limit,PDO::PARAM_INT);
                $statement->execute();
            }
            // only limit
            else{
                $sql = $sql."ORDER BY s.date DESC LIMIT :limit";
                $statement = $this->connection->prepare($sql);
                $statement->bindParam("user_id", $userId,PDO::PARAM_INT);
                $statement->bindParam("limit", $limit,PDO::PARAM_INT);
                $statement->execute();
            }
            
            
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        }catch (PDOException $e){
            // rollback everything in case of any failure
            die(print_r($e->getMessage()));
        }
        
        $shared->setResponse($results);
    }
    
    
    /**
     * Get Liked Swapps
     *
     * @param Shared $shared
     */
    public function getLikedSwapps(Shared $shared, Swap $swapp,$tokenUserId)
    {
        try{
            $sql = "SELECT 
            s.*
            FROM swapps AS s 
            INNER JOIN swapp_likes as sl ON sl.swapp_id = s.id
            WHERE sl.user_id = ? 
            ORDER BY s.date DESC";
            $statement = $this->connection->prepare($sql);
            $statement->execute(
                [
                    $tokenUserId
                ]
                );
            
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            
        }catch (PDOException $e){
            // rollback everything in case of any failure
        }
        
        $shared->setResponse($results);
    }


    /**
     * Get number of total swapps
     *
     * @param Counter $counter
     */
    public function getTotalSwapps(Counter $counter)
    {
        try {
            // set database instructions
            $sql = "SELECT COUNT(*) as count FROM swapps";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            // set response
            $response = $statement->fetch(PDO::FETCH_ASSOC);

            $counter->setCount($response['count']);

        }catch(PDOException $e){
            die($e->getMessage());
        }
    }

    /**
     * Get Swapps Simple
     *
     * @param Shared $shared
     */
    public function getSwappsSimple(Shared $shared, Swap $swap)
    {
        try{
            $sql = "SELECT * FROM swapps 
                    WHERE user_id = ? AND state = ?
                    ORDER BY date DESC";
            $statement = $this->connection->prepare($sql);
            $statement->execute(
                [
                    $swap->getUserId(),
                    $swap->getState()
                ]
            );
                        
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            
        }catch (PDOException $e){

            die(print_r($e->getMessage()));
        }

        $shared->setResponse($results);
    }


    /**
     * Get Swapp Images
     *
     * @param Swap $swap
     * @param Shared $shared
     */
    public function getSwappImages(Swap $swap, Shared $shared):ImagesCollection
    {
        $collection = new ImagesCollection();

        try{
            $sql = "SELECT 
                id,
                source as image,
                swapps_id,
                height,
                width
            FROM swapp_images WHERE swapps_id = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute(
                [
                    $swap->getId()
                ]
            );

            while($result = $statement->fetchObject(Images::class)){
                $collection->addEntity($result);
            }


        }catch (PDOException $e){
            // rollback everything in case of any failure
            $this->connection->rollBack();
        }

        return $collection;
    }


    /**
     * Get Swapp Tags
     *
     * @param Swap $swap
     * @param Shared $shared
     */
    public function getSwappTags(Tags $tags, Shared $shared)
    {
        try{
            $sql = "SELECT * 
            FROM swapp_tags AS st
            INNER JOIN tags AS t ON t.id = st.tags_id
            WHERE st.swapps_id = ? AND st.type = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute(
                [
                    $tags->getParent(),
                    $tags->getType()
                ]
            );

            $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        }catch (PDOException $e){
            // rollback everything in case of any failure
            $this->connection->rollBack();
        }

        $shared->setResponse($results);
    }


    /**
     * Insert swapp request
     *
     * @param SwapRequest $swap
     * @param Shared $shared
     * @return Shared
     */
    public function sendSwappRequest(SwapRequest $swappRequest, Shared $shared):Shared {

        try {
            $this->connection->beginTransaction();

            // insert into messages
            $sql = "INSERT INTO messages(from_user, to_user, state) VALUES(?,?,?)";
            $statement = $this->connection->prepare($sql);
            $statement->execute(
                [
                    $swappRequest->getFromUser(),
                    $swappRequest->getToUser(),
                    'UNRED'
                ]
            );
            $messageId = $this->connection->lastInsertId();

            // insert into message swapp request
            $sql = "INSERT INTO message_swapp_request(for_swapps_id, to_swapps_id, messages_id) VALUES(?,?,?)";
            $statement = $this->connection->prepare($sql);
            $statement->execute(
               [
                   $swappRequest->getSwappThis(),
                   $swappRequest->getSwappFor(),
                   $messageId
               ]
            );

            $shared->setState(200);

            $this->connection->commit();
        }catch(PDOException $e){
            $this->connection->rollBack();

            $shared->setState(304);
            die($e->getMessage());
        }

        // return response
        return $shared;
    }


    /**
     * Insert new like in swapp_likes table
     *
     * @param Swap $swap
     * @param Shared $shared
     * @return Shared
     */
    public function likeSwapp(Swap $swapp, Shared $shared):Shared {

        try {
            // set database instructions
            $sql = "INSERT INTO swapp_likes (user_id, swapp_id) VALUES (?,?)";
            $statement = $this->connection->prepare($sql);
            $statement->execute([
                $swapp->getUserId(),
                $swapp->getId()
            ]);
            
            // set state
            $shared->setState(200);

        }catch(PDOException $e){
            // set state
            $shared->setState(304);
        
        }

        // return response
        return $shared;
    }

    
    /***
     * Get If Swapp Is liked
     * 
     * @param Swap $swapp
     * @param Shared $shared
     * @return Shared
     */
    public function getLikedSwappState(Swap $swapp, Shared $shared): Shared
    {
        try{
            // set database instructions
            $sql = "SELECT COUNT(*) AS liked FROM swapp_likes WHERE user_id = ? AND swapp_id = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute(
                [
                    $swapp->getUserId(),
                    $swapp->getId()
                ]
            );
            
            // get state
            $results = $statement->fetch(PDO::FETCH_ASSOC);
                        
            // return liked
            if($results['liked'] == 0){
                $shared->setResponse(["state"=>"NOTLIKED"]);
            }else{
                $shared->setResponse(["state"=>"LIKED"]);
            }
           
        }catch(PDOException $e){
            // set state
            $shared->setState(304);
        }
        
        // return response
        return $shared;
    }


    /**
     * Update swapp request
     *
     * @param SwapRequest $swap
     * @param Shared $shared
     * @return Shared
     */
    public function changeSwappRequestState(SwapRequest $swap, Shared $shared):Shared {

        try {

            // set database instructions
            $sql = "UPDATE message_swapp_request SET state = ? WHERE messages_id = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute([
                $swap->getState(),
                $swap->getId()
            ]);
            
            
            if($swap->getState() == "ACCEPTED"){
                

                // set database instructions
                $sql = "SELECT * FROM message_swapp_request WHERE messages_id = ?";
                $statement = $this->connection->prepare($sql);
                $statement->execute([
                    $swap->getId()
                ]);
                
                $response = $statement->fetch(PDO::FETCH_ASSOC);
                                                
                // set database instructions
                $sql = "UPDATE swapps SET state = 'INACTIVE' WHERE id = ?";
                $statement = $this->connection->prepare($sql);
                $statement->execute([
                    $response['for_swapps_id']
                ]);
                
                // set database instructions
                $sql = "UPDATE swapps SET state = 'INACTIVE' WHERE id = ?";
                $statement = $this->connection->prepare($sql);
                $statement->execute([
                    $response['to_swapps_id']
                ]);
            }
            
            

            // get neccessary data

            if($statement->rowCount() > 0){
                $shared->setState(200);
            }else {
                $shared->setState(304);
            }


        }catch(PDOException $e){
            //die($e->getMessage());
            $shared->setState(304);
            
        }

        // return response
        return $shared;
    }


    /**
     * Delete swapp
     *
     * @param Swap $swap
     * @param Shared $shared
     */
    public function deleteSwapp(Swap $swap, Shared $shared)
    {
        try {
            // set database instructions
            $sql = "DELETE FROM swapps WHERE id = ?";

            $statement = $this->connection->prepare($sql);
            $statement->execute(
                [
                    $swap->getId()
                ]
            );

            $shared->setState(200);
        }catch(PDOException $e){
            die($e->getMessage());
        }
    }
    
    
    /**
     * Get offset from user id
     * 
     * @param $userId
     * @param $swapId
     */
    public function getSwapTimezone($userId, $swapId)
    {
        try {
            // set database instructions
            $sql = "SELECT u.offset 
                    FROM users AS u
                    INNER JOIN swapps AS S ON s.user_id = s.user_id
                    WHERE u.id = ? AND s.id = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute([
                    $userId,
                    $swapId
                ]);
            
            $results = $statement->fetch(PDO::FETCH_ASSOC);
            
            
            return $results;
        }catch(PDOException $e){
            die($e->getMessage());
        }
        
        return $results;
    }
    
    
    
}