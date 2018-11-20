<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 7/20/18
 * Time: 4:09 PM
 */

namespace Model\Entity;


class Counter
{

    private $count;

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count): void
    {
        $this->count = $count;
    }

}