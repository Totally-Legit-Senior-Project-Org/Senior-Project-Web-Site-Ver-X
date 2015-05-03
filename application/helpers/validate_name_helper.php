<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( !function_exists('validateName'))
{
    function validateName($name)
    {
        $name = str_replace(' ', '_', $name);
        $name = str_replace('\\','_', $name);
        $name = str_replace('^','_', $name);
        $name = str_replace('/','_', $name);
        $name = str_replace('?','_', $name);
        $name = str_replace(':','_', $name);
        $name = str_replace('|','_', $name);
        $name = str_replace('"','_', $name);
        $name = str_replace('<','_', $name);
        $name = str_replace('>','_', $name);
        $name = str_replace('.','_', $name);
        $name = str_replace('*','_', $name);
        $name = str_replace("'","_", $name);  
        
        return $name;
    }
}