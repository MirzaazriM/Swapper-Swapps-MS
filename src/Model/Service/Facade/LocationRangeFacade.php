<?php
namespace Model\Service\Facade;

use Model\Entity\Range;
use PhpParser\Node\Expr\Cast\Double;

class LocationRangeFacade
{
    
    private $latitude;
    private $longitude;
    private $range;
    
    public function __construct(Double $latitude, Double $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        
        $this->range = new Range();
    }
    
    
    /**
     * Get Range in Square
     * @return Range
     */
    public function getRange():Range
    {
        return $this->range();
    }
    
    
    public function convertLocationToKm()
    {
        
    }
    
    
    public function calculatePositionA()
    {
        
    }
    
    
    public function calculatePositionB()
    {
        
    }
    
    
    public function calculatePositionC()
    {
        
    }
    
    
    public function calcualtePositionD()
    {
        
    }
    
    
    
}

