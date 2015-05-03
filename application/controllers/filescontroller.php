<?php    
include("file_tree_library.class.php"); 

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

session_start();

class FilesController extends CI_Controller {

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

    public function project_files() 
    {        
        //if the user is logged in, then grant access
        if( isUserLoggedIn($this))
            $this->load->view('project_files');
        else
           redirect('home','refresh');     
    }     
    
    public function head_guide()
    {           
        $this->download_single_file("./UserGuide/Head.pdf") ;
        exit;
    }
    public function mentors_guide()
    {           
        $this->download_single_file("./UserGuide/Mentor.pdf") ;
        exit;
    }
    
     public function students_guide()
    {           
        $this->download_single_file("./UserGuide/Student.pdf") ;
        exit;
    }
    public function match_guide() {
        $this->download_single_file("./UserGuide/Matchmaking.pdf") ;
        exit;
    }
        
    public function download_single_file($file)
    {       
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
    }
    
    //Function that handles the uploading of documents
    public function do_upload()
    {
        if(isset($_FILES['userfile']['tmp_name']))
        {
            $theProject = $this->input->post('projects');
            
            $filecheck = basename($_FILES['userfile']['name']);
            
            $file = str_replace(' ', '_', $filecheck);
            
            $ext = strtolower(substr($filecheck, strrpos($file, '.') + 1));      
           
            if ($ext == "")
            {
                $msg = 'Please choose a file to upload.';
                
                setErrorFlashMessage($this, $msg);

                redirect('files/project_files');
            }
            $filename = substr($file, 0, strrpos($file, '.'));
            $dir = './uploads/';

            //check that the uploads directory exists, if not then create it and grant all permissions
            if (!file_exists($dir) and !is_dir($dir)) 
            {
                mkdir($dir, 0777);  
                chmod($dir, 0777);
            } 
            
            //validates path for the <project_name> directory      
            $project_name = validateName($theProject);
            
            $directory2 = $dir.$project_name.'/';
            
            //check that the project directory exists, if not then create it and grant all permissions
            if (!file_exists($directory2) and !is_dir($directory2)) 
            {
                mkdir($directory2, 0777);   
                chmod($directory2, 0777);
            } 
            
            $theMilestone = $this->input->post('milestones');
            if(!$theMilestone == '')
            {                
                //validates path for the <milestone_name> directory                           
                $milestone_name = validateName($theMilestone);
                $directory3 = $directory2.$milestone_name.'/';

                //check that the <milestone_name> directory exists, if not then create it and grant all permissions
                if (!file_exists($directory3) and !is_dir($directory3)) 
                {
                    mkdir($directory3, 0777);    
                    chmod($directory3, 0777);
                }   

                $config['upload_path'] = $directory3;
            }
            else 
            {
                $config['upload_path'] = $directory2;
            }

            $user = getCurrentUserId($this);

            $config['allowed_types'] = '*';
            $config['file_name'] = $filename;
            $config['overwrite'] = true;            

            $this->load->library('upload', $config);
  
            if ( !$this->upload->do_upload())
            {
                $error = array('error' => $this->upload->display_errors());
                
                $msg = 'File upload was unsuccessful.'.$this->upload->display_errors().' '.$directory3;
                
                setErrorFlashMessage($this, $msg);
                
                redirect('files/project_files');
              
                return;
            }
            else
            {
                $uploaded_file =  new SPW_Uploaded_File_Model();
                if(!$theMilestone == '')
                {
                    $uploaded_file->insert($file, $directory3, $user, $theProject, $theMilestone);
                }
                else 
                {
                    $uploaded_file->insert($file, $directory2, $user, $theProject, $theMilestone);
                }
                $data = array('upload_data' => $this->upload->data());
                
                $msg = 'Your upload was successful!';
                
                setFlashMessage($this, $msg);

                redirect('files/project_files', 'refresh');          
            }        
        }
        else
        {
            $msg = 'This file exceeds the upload size limit of 8MB.';
            
            setErrorFlashMessage($this, $msg);

            redirect('files/project_files'); 
        }
    }
    
    public function download_delete_files()
    {
        $count = 0;
        if ($this->input->post('action') === 'Delete')
        {
            if (is_array($this->input->post('delete_files'))) 
            {
                //retrieve all the ids from the array
                foreach ($this->input->post('delete_files') as $key => $value) 
                {
                    $file_path = $this->spw_uploaded_file_model->get_file_path($value);
                    
                    $this->spw_uploaded_file_model->delete($value);
                    
                    unlink($file_path);
                    
                    $count++;
                } 
            }
            if ($count > 0)
            {
                $msg = 'Successfully deleted ' . $count . ' file(s)';
                
                setFlashMessage($this, $msg);
            }
            else 
            {
                $msg = 'No files were selected for deletion';
                
                setErrorFlashMessage($this, $msg);
            }
        }
        elseif ($this->input->post('download_files'))
        {
            foreach ($this->input->post('download_files') as $key => $value) 
            {
                $file_path = $this->spw_uploaded_file_model->get_file_path($value);
                
                $this->download_single_file($file_path);
                exit;
            } 
        }
        elseif ($this->input->post('download_project'))
        {
            foreach ($this->input->post('download_project') as $key => $value) 
            {
                $dir = FCPATH.'/uploads/';
                
                //validates path for the <project_name> directory      
                $project_name = validateName($value);

                $rootPath = $dir.$project_name.'/';
                $zipFolder = $dir.$project_name.'/'.$project_name.'.zip';
                
                $this->zip($rootPath, $zipFolder);
                $this->download_single_file($zipFolder);
                unlink($zipFolder);
                exit;
            }
        }        
        redirect('files/project_files');
    }                    
    //this function returns a list of files of a specific category from all projects
    public function getFilesFrom($category)
    {
        return;
    }
    
    public function getMyProjects($id)
    {
        $list = $this->spw_uploaded_file_model->getMyProjects($id);
        
        return $list;
    }
    
    public function getProjects()
    {
        $list = $this->spw_uploaded_file_model->getProjectList();
        
        return $list;        
    }
    
    public function getMilestones()
    {
        $list = $this->spw_uploaded_file_model->getMilestoneList();
        
        return $list;
    }
    
    function zip($source, $destination)
    {
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        $zip = new ZipArchive();
        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
            return false;
        }

        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source) === true)
        {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file)
            {
                $file = str_replace('\\', '/', $file);

                // Ignore "." and ".." folders
                if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                    continue;

                $file = realpath($file);

                if (is_dir($file) === true)
                {
                    $file = str_replace('\\', '/', $file);
                    
                    $zip->addEmptyDir(str_replace($source . '/', '', $file ));
                }
                else if (is_file($file) === true)
                {
                    $file = str_replace('\\', '/', $file);
                    
                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
            }
        }
        else if (is_file($source) === true)
        {
            $zip->addFromString(basename($source), file_get_contents($source));
        }
        return $zip->close();        
    }    
}
