<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( !function_exists('show_hide_deleted'))
{
    function show_hide_deleted($show_hide)
    {
        if (!isset($show_hide) || $show_hide == 'Hide Deleted')
        {
            $sqlAdd = "WHERE deleted = 'false'";
            return $sqlAdd;
        }
        else
        {        
            $sqlAdd = '';
            return $sqlAdd;
        }
    }
}