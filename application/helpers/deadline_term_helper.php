<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( !function_exists('isUnderDeadline'))
{
        function isUnderDeadline($sender_controller)
        {
             $CI = get_instance();
             $CI->load->model('spw_term_model');

             $tempTerm = SPW_Term_Model::getInstance();
             $deadline = $tempTerm->getCurrentTermInfo();
             $currentDate = date("m-d-Y");
             $startDate = date("m-d-Y", strtotime($deadline->start_date));
             $endDate = date("m-d-Y", strtotime($deadline->end_date));

             if(($startDate <= $currentDate) && ($currentDate <=  $endDate))   
             {
                 return true;
             }
             else return false;           
        }
}

if ( !function_exists('isAfterDeadline'))
{
        function isAfterDeadline($sender_controller)
        {
            $CI = get_instance();
            $CI->load->model('spw_term_model');

            $tempTerm = SPW_Term_Model::getInstance();
            $deadline = $tempTerm->getCurrentTermInfo();
            $currentDate = date("m-d-Y");
            $endDate = date("m-d-Y", strtotime($deadline->end_date));

            if($currentDate >  $endDate)   
                return true;
            else 
                return false;           
        }
}