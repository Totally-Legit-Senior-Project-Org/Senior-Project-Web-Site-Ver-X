<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( !function_exists('getCurrentUserHeaderName'))
{
    function getCurrentUserHeaderName($sender_controller)
    {
        if (is_test($sender_controller))
        {
            return 'Camilo';
        }
        else
        {
            $CI = get_instance();

            $CI->load->model('spw_user_model');

            $session_data = $sender_controller->session->userdata('logged_in');
            $user_id = $session_data['id'];
            return $CI->spw_user_model->get_first_name($user_id);
        }
    }
}

if ( !function_exists('getCurrentUserHeaderImg'))
{
    function getCurrentUserHeaderImg($sender_controller)
    {
        if (is_test($sender_controller))
        {
            return 'https://si0.twimg.com/profile_images/635660229/camilin87_bigger.jpg';
        }
        else
        {
            $CI = get_instance();

            $CI->load->model('spw_user_model');

            $session_data = $sender_controller->session->userdata('logged_in');
            $user_id = $session_data['id'];
            return $CI->spw_user_model->get_picture($user_id);
        }
    }
}

if ( !function_exists('getCurrentUserHeaderFullName'))
{
    function getCurrentUserHeaderFullName($sender_controller)
    {
        if (is_test($sender_controller))
        {
            return 'Camilo Sanchez';
        }
        else
        {
            $CI = get_instance();

            $CI->load->model('spw_user_model');

            $session_data = $sender_controller->session->userdata('logged_in');
            $user_id = $session_data['id'];
            return $CI->spw_user_model->get_fullname($user_id);
        }
    }
}

if ( !function_exists('getGenericUserFullName'))
{
    function getGenericUserFullName($user_id)
    {
            $CI = get_instance();

            $CI->load->model('spw_user_model');

            return $CI->spw_user_model->get_fullname($user_id);
    }
}

if ( !function_exists('getGenericProjectName'))
{
    function getGenericProjectName($proj_id)
    {
            $CI = get_instance();

            $CI->load->model('spw_project_model');

            return $CI->spw_project_model->get_project_title($proj_id);
    }
}

if ( !function_exists('isHeadProfessor'))
{
    function isHeadProfessor($sender_controller)
    {
        if (is_test($sender_controller))
        {
            return true;
        }
        else
        {
            $CI = get_instance();
            //load the current user model
            $CI->load->model('spw_user_model');

            $session_data = $sender_controller->session->userdata('logged_in');
            $user_id = $session_data['id'];
            //call the function that determines if the current user is the head professor
            return $CI->spw_user_model->is_head_professor($user_id);
        }
    }
}

if ( !function_exists('isProfessor'))
{
    function isProfessor($sender_controller)
    {
        if (is_test($sender_controller))
        {
            return true;
        }
        else
        {
            $CI = get_instance();
            //load the current user model
            $CI->load->model('spw_user_model');

            $session_data = $sender_controller->session->userdata('logged_in');
            $user_id = $session_data['id'];
            //call the function that determines if the current user is the head professor
            return $CI->spw_user_model->isUserProfessor($user_id);
        }
    }
}
if ( !function_exists('getCurrentUserHeaderImage'))
{
    function getCurrentUserHeaderImage($sender_controller)
    {
        $CI = get_instance();

        $CI->load->model('spw_user_model');

        $session_data = $sender_controller->session->userdata('logged_in');
        $user_id = $session_data['id'];

        $png_version = $user_id .'.png';
        $jpg_version = $user_id .'.jpg';
        $gif_version = $user_id .'.gif';
        $jpeg_version = $user_id .'.jpeg';
        
        if(file_exists('./img/'. $png_version)) 
            return '/img/'.$png_version;
        else if (file_exists('./img/'.$jpg_version))
            return '/img/'.$jpg_version;
        else if (file_exists('./img/'.$gif_version))
            return '/img/'.$gif_version;
        else if (file_exists('./img/'.$jpeg_version))
            return '/img/'.$jpeg_version;
        else
            return '/img/no-photo.jpeg';
    }
}

/*added in spw v. 5
 *Fuction used to fix pictures not appearing in in certain views (task #299).
 *Function uses user_id, and returns relative address of the user's picture.*/
function getImage($user_id) {

    $png_version = $user_id . '.png';
    $jpg_version = $user_id . '.jpg';
    $gif_version = $user_id . '.gif';
    $jpeg_version = $user_id . '.jpeg';

    if (file_exists('./img/' . $png_version))
        return '/img/' . $png_version;
    else if (file_exists('./img/' . $jpg_version))
        return '/img/' . $jpg_version;
    else if (file_exists('./img/' . $gif_version))
        return '/img/' . $gif_version;
    else if (file_exists('./img/' . $jpeg_version))
        return '/img/' . $jpeg_version;
    else
        return '/img/no-photo.jpeg';
}
?>
