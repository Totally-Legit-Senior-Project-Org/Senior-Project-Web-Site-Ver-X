<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start();
class GuestController extends CI_Controller 
{

    public function __construct()
    {
        parent::__construct();        
    }

    public function index()
    {
        $this->session->set_userdata('logged_in', null);
        $this->session->set_userdata('guest', true);
//	 if(!isUserLoggedIn($this))
//         {
//             //$sess_array;
//             //$_SESSION['guest'] = 1;
//             $newdata = array(
//                   'username'  => 'guest',                   
//                   'logged_in' => FALSE,
//               );
//             
//             //$this->session->set_userdata($newdata);             
//             $this->load->view('home_index');	
//         }
        // $this->load->view('home_index');
        redirect('home', 'refresh');
     }
     
     public function logoutGuest()
     {
        //if(isset($_SESSION['guest']))
        //{
          //  unset($_SESSION['guest']);
       // }
        session_destroy();
         //$this->session->unset_userdata('guest');
        redirect('login', 'refresh');         
     }
     
     
}