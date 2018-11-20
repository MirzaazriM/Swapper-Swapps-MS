<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 7/20/18
 * Time: 4:08 PM
 */

namespace Model\Entity;


class Shared
{

    private $state;
    private $response;

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
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response): void
    {
        $this->response = $response;
    }



}