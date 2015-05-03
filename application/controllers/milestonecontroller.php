<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start();

class MilestonesController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('nav_top');
        $this->load->helper('flash_message');
        $this->load->helper('request');
        $this->load->helper('file');
        $this->load->helper('download');
        $this->load->helper('deadline_term');
        $this->load->helper('flash_message');
        $this->load->helper("validate_name");        
        $this->load->model('spw_user_model');
        $this->load->model('spw_project_model');
        $this->load->model('spw_milestones_model');
        $this->load->model('spw_uploaded_file_model');        
        $this->load->library('unit_test');        
    }
    
    public function milestones_view() 
    {        
        //if the user is logged in, then grant access
        if(isUserLoggedIn($this) && isHeadProfessor($this))         
            $this->load->view('milestones_view');            
        else
           redirect('home','refresh');     
    }    
    
    //Function to edit, restore, soft-delete,add or destroy milestones
    public function requestUpdate()
    {     
        $count = 0;
           
        
        //************************SOFT DELETE MILESTONES***************************
        if ($this->input->post('action') === 'Delete')
        {
            if (is_array($this->input->post('delete_milestones'))) 
            {                
                $directory = './deleted/';
                
                //make directory to store soft deleted files
                if (!file_exists($directory) and !is_dir($directory)) 
                {
                    mkdir($directory, 0777);      
                } 
                foreach ($this->input->post('delete_milestones') as $key => $value) 
                {
                    $count++;
                    
                    //change DB for spw_milestones
                    $this->spw_milestones_model->delete_milestone($value);
                    
                    //retrieve all files contained in the milestone to be deleted
                    $milestone_name = $this->spw_milestones_model->get_milestone_name($value);
                    $milestones_dirs = $this->spw_milestones_model->get_milestone_dirs($value);
                    $projects_dirs = $this->spw_milestones_model->get_project_dirs($value);
                    $files_dirs = $this->spw_milestones_model->get_files_dirs($value);
                    
                    if (is_array($projects_dirs))
                    {                    
                        foreach ($projects_dirs as $key => $value)
                        {
                            $project_name = validateName($value);

                            $directory2 = $directory.$project_name.'/';

                            //make directory to store deleted files by project
                            if (!file_exists($directory2) and !is_dir($directory2)) 
                            {
                                mkdir($directory2, 0777);      
                            } 
                        }
                    }
                    
                    $new = array('deleted');               
                    $old = array('uploads');
                    
                    if (is_array($milestones_dirs))
                    {
                        foreach ($milestones_dirs as $key => $value)
                        {
                            $newPath = str_replace($old, $new, $value);
                            

                            
                            //make directory to store deleted files by milestone
                            if (!file_exists($newPath) and !is_dir($newPath)) 
                            {
                                mkdir($newPath, 0777);      
                            } 
                        }
                    }

                    if (is_array($files_dirs))
                    {
                        foreach($files_dirs as $key => $value) 
                        {
                            if (file_exists($value) and !is_dir($value)) 
                            {                             
                                //change DB for spw_uploaded_file paths
                                $newPath = str_replace($old, $new, $value);     
                                rename($value, $newPath);
                            }
                        }
                    }
                                        
                    if (is_array($milestones_dirs))
                    {
                        foreach ($milestones_dirs as $key => $value)
                        {
                            if (file_exists($value) and is_dir($value)) 
                            {                            
                                //unlink milestone folders
                                rmdir($value);
                            }
                        }
                    }                                        
                } 
                $this->spw_uploaded_file_model->updateFilePath('delete', $milestone_name);
            }
            if ($count > 0)
            {
                $msg = 'Successfully deleted ' . $count . ' milestone(s).';
                setFlashMessage($this, $msg);
            }
            else 
            {
                $msg = 'No milesetones were selected for deletion.';
                setErrorFlashMessage($this, $msg);
            }                                           
        }        
        //************************UPDATE MILESTONES***************************
        elseif ($this->input->post('action') === 'Save Milestones') 
        {

            $milestones = $this->input->post('milestone');

            //search for duplicate entries
            $i = 1;
            $j = 1;
            if (is_array($milestones))
            {
                foreach($milestones as $key => $value1)
                {                            
                    foreach($milestones as $key => $value2)
                    {
                        if ($i != $j)
                        {
                            if(($value1['name'] == $value2['name']) && 
                                    ($value1['name'] != null) && 
                                    ($value2['name'] != null))
                            {
                                //invalid duplicate names found
                                $count = -1;
                            }
                        }    
                        $j++;
                    }
                    $j = 1;
                    $i++;
                } 
                if ($count != -1)
                {
                    //input contains all valid milestone names, proceed to save them
                    foreach($milestones as $key => $value1)
                    {         
                        if (($value1['id'] == null) && ($value1['name'] != null))
                        {                                                      
                            $this->spw_milestones_model->insert_milestones("", $value1['name'], $value1['due']);                                                       
                            $count++;              
                        }                
                        elseif($value1['id'] != null)
                        {
                            $query = $this->spw_milestones_model->get_row($value1['id']);
                             
                            foreach($query->result() as $row)
                            {
                                $old_id   = $row->milestone_id;
                                $old_name = $row->milestone_name;
                                $old_date = $row->due_date;
                                
                                if(($old_id == $value1['id']) && 
                                        ($old_name != $value1['name']) &&
                                        ($value1['name'] != null))
                                {                           
                                    //update path name
                                    $milestones_dirs = $this->spw_milestones_model->get_milestone_dirs($value1['id']);
                                    $files_dirs = $this->spw_milestones_model->get_files_dirs($value1['id']);
                                    
                                    $old = validateName($old_name);  
                                    $new = validateName($value1['name']); 
                                            
                                    if (is_array($milestones_dirs))
                                    {
                                        foreach ($milestones_dirs as $key => $value)
                                        {
                                            $newPath = str_replace($old, $new, $value);

                                            //make directory to restore files by milestone
                                            if (!file_exists($newPath) and !is_dir($newPath)) 
                                            {
                                                mkdir($newPath, 0777);      
                                            } 
                                        }
                                    }
                                    
                                    if (is_array($files_dirs))
                                    {
                                        foreach($files_dirs as $key => $value) 
                                        {
                                            //change DB for spw_uploaded_file paths
                                            $newPath = str_replace($old, $new, $value);    

                                            rename($value, $newPath);
                                        }
                                    }
                                                                        
                                    if (is_array($milestones_dirs))
                                    {
                                        foreach ($milestones_dirs as $key => $value)
                                        {
                                            if (file_exists($value) and is_dir($value)) 
                                            {
                                                rmdir($value);
                                            }
                                        }
                                    }      
                                                                                
                                    $this->spw_milestones_model->update_row($value1['id'], $value1['name'],$value1['due'], 'false');
                                    $count++; 
                                }
                                elseif(($old_id == $value1['id']) && ($old_date != $value1['due']))
                                {
                                    $this->spw_milestones_model->update_row($value1['id'], $value1['name'],$value1['due'], 'false');
                                    $count++; 
                                } 
                                elseif(isset($value1['restore']))
                                {
                                    $milestones_dirs = $this->spw_milestones_model->get_milestone_dirs($value1['id']);
                                    $files_dirs = $this->spw_milestones_model->get_files_dirs($value1['id']);
                                    $projects_dirs = $this->spw_milestones_model->get_project_dirs($value1['id']);
                                    
                                    $this->spw_milestones_model->update_row($value1['id'], $value1['name'],$value1['due'], 'false');
                                    
                                    
                                    $directory = './uploads/';

                                    if (!file_exists($directory) and !is_dir($directory)) 
                                    {
                                        mkdir($directory, 0777);      
                                    }    
                                    if (is_array($projects_dirs))
                                    {                    
                                        foreach ($projects_dirs as $key => $value)
                                        {
                                            $project_name = validateName($value);                                     

                                            $directory2 = $directory.$project_name.'/';

                                            //make directory to restore files by project
                                            if (!file_exists($directory2) and !is_dir($directory2)) 
                                            {
                                                mkdir($directory2, 0777);      
                                            } 
                                        }
                                    }

                                    $new = array('uploads');               
                                    $old = array('deleted');

                                    if (is_array($milestones_dirs))
                                    {
                                        foreach ($milestones_dirs as $key => $value)
                                        {
                                            $newPath = str_replace($old, $new, $value);

                                            //make directory to restore files by milestone
                                            if (!file_exists($newPath) and !is_dir($newPath)) 
                                            {
                                                mkdir($newPath, 0777);      
                                            } 
                                        }
                                    }

                                    if (is_array($files_dirs))
                                    {
                                        foreach($files_dirs as $key => $value) 
                                        {
                                            //change DB for spw_uploaded_file paths
                                            $newPath = str_replace($old, $new, $value);     
                                            rename($value, $newPath); 
                                        }
                                    }

                                    if (is_array($milestones_dirs))
                                    {
                                        foreach ($milestones_dirs as $key => $value)
                                        {
                                            if (file_exists($value) and is_dir($value)) 
                                            {
                                                rmdir($value);
                                            }
                                        }
                                    }                                                                                                           
                                    $this->spw_uploaded_file_model->updateFilePath('restore', $value1['name']);
                                    $count++;                                                             
                                }
                                elseif(isset($value1['destroy']))
                                {
                                    $files_dirs = $this->spw_milestones_model->get_files_dirs($value1['id']);
                                    $milestones_dirs = $this->spw_milestones_model->get_milestone_dirs($value1['id']);
                                    
                                    if (is_array($files_dirs))
                                    {
                                        foreach ($files_dirs as $key => $value)
                                        {
                                            if (file_exists($value) and !is_dir($value)) 
                                            {
                                                unlink($value);
                                            }
                                        }
                                    }       
                                    
                                    if (is_array($milestones_dirs))
                                    {
                                        foreach ($milestones_dirs as $key => $value)
                                        {
                                            if (file_exists($value) and is_dir($value)) 
                                            {
                                                rmdir($value);
                                            }
                                        }
                                    }                                            
                                    
                                    $this->spw_milestones_model->destroy($value1['id'], $value1['name'],$value1['due'], 'false');
                                    $count++;
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($count > 0)
        {
            $msg = 'Successfully updated ' . $count . ' milestone(s).';
            setFlashMessage($this, $msg);
            redirect('admin/milestones_view', 'refresh');  
        }
        elseif ($count == -1)
        {
            $msg = 'Duplicate names are not allowed.';
            setErrorFlashMessage($this, $msg);
            redirect('admin/milestones_view');
        }
        else
        {
            $msg = 'No milestone(s) were updated.';
            setErrorFlashMessage($this, $msg);
            redirect('admin/milestones_view');                
        }
    }    
}