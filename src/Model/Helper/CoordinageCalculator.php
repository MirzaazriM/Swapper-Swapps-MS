<?php
namespace Model\Helper;

use PhpParser\Node\Expr\Cast\Double;

class CoordinageCalculator
{
    
    
    /**
     * Convert Latitude to Killometeres
     * @param Double $latitude
     * @return Double
     */
    public function latitueToKM(Double $latitude):Double // TODO
    {
        $km = $latitude * 111.32;
        return $km;
    }
    
    
    /**
     * Convert Longitude to Killometers
     * @param Double $longitude
     * @return Double
     */
    public function longitudeToKM(Double $latitude, Double $longitude):Double // TODO
    {
        $km = $longitude * 40075 * cos(deg2rad($latitude))/360;
        return $km;
    }
    
    
    /**
     * Convert Killometers to Latitude
     * 
     * @param Double $km
     * @return Double
     */
    public function kmToLatitude(Double $km):Double // TODO
    {
        $lat = $km / 111.32;
        return $lat;
    }
    
    
    /**
     * Convert Killmeters to Longitude
     * 
     * @param Double $km
     * @return Double
     */
    public function kmToLongitude(Double $latitude, Double $km):Double // TODO
    {
        $km = $longitude / 40075 / cos(deg2rad($latitude))*360;
        return $km;
    }
    
    
}

