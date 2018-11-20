<?php
namespace Model\Entity;

class Range
{
    
    
    private $locationA;
    private $locationB;
    private $locationD;
    private $locationE;
    
    
    /**
     * @return mixed
     */
    public function getLocationA()
    {
        return $this->locationA;
    }

    /**
     * @return mixed
     */
    public function getLocationB()
    {
        return $this->locationB;
    }

    /**
     * @return mixed
     */
    public function getLocationD()
    {
        return $this->locationD;
    }

    /**
     * @return mixed
     */
    public function getLocationE()
    {
        return $this->locationE;
    }

    /**
     * @param mixed $locationA
     */
    public function setLocationA($locationA)
    {
        $this->locationA = $locationA;
    }

    /**
     * @param mixed $locationB
     */
    public function setLocationB($locationB)
    {
        $this->locationB = $locationB;
    }

    /**
     * @param mixed $locationD
     */
    public function setLocationD($locationD)
    {
        $this->locationD = $locationD;
    }

    /**
     * @param mixed $locationE
     */
    public function setLocationE($locationE)
    {
        $this->locationE = $locationE;
    }

   
}

