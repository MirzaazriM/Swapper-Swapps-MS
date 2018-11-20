<?php
namespace Helper;

class InputChecker
{
    
    
    /**
     * This class can handle the comparisment of the input and the type it should be.
     * For exmaple:
     *      $input = "string" $type = 'STRING' 
     *      it will return a true value
     * The same thing for integer and array is available.
     * 
     * The array has an format field available which gives the abiblity to compare two arrays.
     *
     * You can compare two arrays if they are simple arrays with string or int input values only, or you could compare a complex associative array
     * or just a complex object array.
     * 
     */
   
    
    /**
     * Check Input
     * 
     * @param $input
     * @param string $type
     * @param $format
     * @return bool
     */
    public function checkInput($input,string $type, array $format = null):bool
    {
        $state = false;
        // check type copatitlibty
        switch($type){
            // check if numberic
            case 'INT':
                if(is_numeric($input)){
                    $state  = true;
                }
                break;
            // check if string
            case 'STRING': 
                if(is_string($input)){
                    $state  = true;
                }
                break;
            // check if array
            case 'ARRAY':
                if(is_array($input) && !is_null($format)){
                    // check format
                    $state = $this->checkFormat($input, $format);
                }
                break;
        }
        
        return $state;
    }
    
    
    /**
     * Check format
     * 
     * @param array $input
     * @param array $format
     * @return bool
     */
    public function checkFormat(array $input, array $format):bool
    {
        $state = false;
        
        // if simple array string
        if(@$this->simpleArrayString($format)){
            $state = @$this->simpleArrayString($input);
        }
        // if simple array int
        else if(@$this->simpleArrayInt($format)){
            $state = @$this->simpleArrayInt($input);
        }
        // if associative array
        else if(@$this->associativeArray($format)){
            $state = @$this->compareKeys($input[0],$format[0]);
        }
        // complex array
        else if(@$this->complexArray($format)){
            $state = $this->compareKeys($input,$format);
        }
        
        return $state;
    }
    

    /**
     * Return Simple Array String
     * 
     * @param array $format
     * @return bool
     */
    public function simpleArrayString(array $format):bool
    {
        return is_string($format[0]);
    }
    
    
    /**
     * Return Simple Array Numeric
     * 
     * @param array $format
     * @return bool
     */
    public function simpleArrayInt(array $format):bool
    {
        return is_numeric($format[0]);
    }
    
    
    /**
     * Complex Array
     * 
     * @param array $format
     * @return bool
     */
    public function complexArray(array $format):bool
    {
        $state = false;
        
        if(sizeof(array_keys($format)) > 0){
            $state = true;
        }
        
        return $state;
    }
    

    /**
     * Associative Array 
     * 
     * @param array $format
     * @return bool
     */
    public function associativeArray(array $format):bool
    {
        $state = false;
        
        $count = 0;
        
        foreach($format as $format){
            $state = is_array($format);
            break;
        }
        
        return $state;
    }
    
    
    /**
     * Compare Input and Format Keys
     * 
     * @param array $inputKeys
     * @param array $formatKeys
     * @return string
     */
    public function compareKeys(array $inputKeys, array $formatKeys):string
    {
        $state = false;
        
        $inputKeysTemp = array_keys($inputKeys);
        $formatKeysTemp = array_keys($formatKeys);
                
        if(sizeof(array_diff($formatKeysTemp, $inputKeysTemp)) == 0){
            $state = true;
        }
        
        return $state;
    }
    
    
}

