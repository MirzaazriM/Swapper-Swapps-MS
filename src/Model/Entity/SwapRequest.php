<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 7/20/18
 * Time: 4:21 PM
 */

namespace Model\Entity;

use Model\Contract\HasId;

class SwapRequest implements HasId
{

    private $id;
    private $fromUser;
    private $toUser;
    private $state;
    private $swappThis;
    private $swappFor;


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }


    /**
     * @return mixed
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }


    /**
     * @param mixed $fromUser
     */
    public function setFromUser($fromUser): void
    {
        $this->fromUser = $fromUser;
    }


    /**
     * @return mixed
     */
    public function getToUser()
    {
        return $this->toUser;
    }


    /**
     * @param mixed $toUser
     */
    public function setToUser($toUser): void
    {
        $this->toUser = $toUser;
    }


    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }


    /**
     * @param mixed $state
     */
    public function setState($state): void
    {
        $this->state = $state;
    }


    /**
     * @return mixed
     */
    public function getSwappThis()
    {
        return $this->swappThis;
    }


    /**
     * @param mixed $swappThis
     */
    public function setSwappThis($swappThis): void
    {
        $this->swappThis = $swappThis;
    }


    /**
     * @return mixed
     */
    public function getSwappFor()
    {
        return $this->swappFor;
    }


    /**
     * @param mixed $swappFor
     */
    public function setSwappFor($swappFor): void
    {
        $this->swappFor = $swappFor;
    }


}