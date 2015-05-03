<?php

class SPW_Uploaded_File_Model extends CI_Model
{
    public $id;
    public $path_to_file;
    public $uploaded_by_user;
    public $project;
    public $upload_date;
        
    public function __construct()
    {
        parent::__construct();
    }

    public function insert($filename, $path, $user_id, $proj_name, $milestone_name)
    {                  
        $data = array(
                        'file_name' => $filename,
                        'path_to_file'  => $path,
                        'uploaded_by_user' => $user_id,
                        'project_name'  => $proj_name,
                        'milestone_name' => $milestone_name
                     );
        
        $query = $this->db
                      ->where('path_to_file', $path)
                      ->where('file_name', $filename)
                      ->select('path_to_file')
                      ->get('spw_uploaded_file');
        
        //check that a record with the same path doesn't exists
        if($query->num_rows() > 0)
        {
            return;            
        }
        else
        {
            $this->db->insert('spw_uploaded_file', $data); 
            $id =  $this->db->insert_id();         

            $sql = 'UPDATE spw_uploaded_file  SET upload_date = CURRENT_TIMESTAMP WHERE id  = ?';
            $this->db->query($sql, $id);		
            return;
        }   
    }
    
    public function updateFilePath($action, $milestone_name)
    {
        if($action == 'delete')
        {
            $new = array('deleted');               
            $old = array('uploads');
            
            
        }
        elseif($action == 'restore')
        {
            $new = array('uploads');               
            $old = array('deleted');            
        }
        
        $files = $this->db->query("SELECT id, path_to_file
                                    FROM spw_uploaded_file
                                    WHERE milestone_name = '".$milestone_name."'");
        
        foreach($files->result_array() as $row)
        {     
            $newPath = str_replace($old, $new, $row['path_to_file']);
        
            $data = array(
                            'path_to_file'  => $newPath,
                     );

            $this->db->where('id', $row['id']);
            $this->db->update('spw_uploaded_file', $data); 
        }        
    }
    
    public function get_files($project_name)
    {
        $query = $this->db
               ->where('project_name', $project_name)
               ->get('spw_uploaded_file');

        if($query->num_rows() > 0)
        {
            return $query;
        }    
        else
        {
            return null;
        }        
    }   
    
    public function get_file_path($file_id)
    {  
        $query = $this->db
               ->where('id', $file_id)
               ->select('path_to_file, file_name')
               ->get('spw_uploaded_file');

        if($query->num_rows() > 0)
        {
            return $query->row()->path_to_file.$query->row()->file_name;
        }
        else
        {
            return null; 
        }
    }
    
//    public function get_path_only($file_id)
//    {  
//        $query = $this->db
//               ->where('id', $file_id)
//               ->select('path_to_file')
//               ->get('spw_uploaded_file');
//
//        if($query->num_rows() > 0)
//        {
//            return $query->row()->path_to_file;
//        }
//        else
//        {
//            return null; 
//        }
//    }
    
    public function delete($id)
    {  
        $this->db->delete('spw_uploaded_file', array('id' => $id));
    }
 
    public function getAllProjects()
    {
        $projs = $this->db->query('SELECT id, title 
                     FROM spw_project
                     WHERE status = "APPROVED" OR status = "PENDING APPROVAL"
                     ORDER BY title'); 
        return $projs;
    }
    
    public function getProfProjects()
    {
        $projs = $this->db->query('SELECT id, title
                                    FROM spw_project
                                    WHERE mentor = "'.getCurrentUserId($this).'"
                                    AND status = "APPROVED" OR status = "PENDING APPROVAL"');
        return $projs;        
    }
    
    public function getStudentProjects()
    {
        $project_num = $this->db->query('SELECT project
                                        FROM spw_user
                                        WHERE id = "'.getCurrentUserId($this).'"');   
        foreach($project_num->result_array() as $row)
        {     
            $projs = $this->db->query('SELECT id, title 
                                        FROM spw_project
                                        WHERE id = "'.$row['project'].'"
                                        AND status = "APPROVED" OR status = "PENDING APPROVAL"');
        }
        return $projs;
    }
    
    public function getFilesByMilestone($milestone_name)
    {
        $files = $this->db->query("SELECT file_name, path_to_file, project_name
                                    FROM spw_uploaded_file
                                    WHERE milestone_name = '".$milestone_name."'
                                    AND status = 'APPROVED' OR status = 'PENDING APPROVAL'");
        return $files;       
    }    
}	
?>