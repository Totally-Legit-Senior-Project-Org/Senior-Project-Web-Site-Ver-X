<?php
class SPW_Milestones_Model extends CI_Model
{
    public $id;
    public $milestone_name;
    public $due_date;
        
    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
        $this->load->helper('file');
    }    
    
    public function insert_milestones($id, $name, $due_date)
    {
        $data = array(  'milestone_id'   => $id,
                        'milestone_name' => $name,        
                        'due_date'       => $due_date,
                        'deleted'        => 'false'
                      );
        $this->db->insert('spw_milestones', $data); 
        $this->db->insert_id();
    }
         
    public function get_all_milestones()
    {
        $query = $this->db              
                ->order_by('due_date')
                ->get('spw_milestones');        
        if($query->num_rows() > 0)
        {
            return $query->result_array(); 
        }
        else { return null; }
    }
    
    public function get_row($id)
    {
        $query = $this->db
            ->where('milestone_id', $id)
            ->get('spw_milestones');   
        return $query;
    }
    
    public function update_row($id, $name, $due_date, $deleted)
    {
        $oldName = "";
        $newPath = "";
        
        //retrieving old milestone name before change
        $this->db->select('milestone_name');
        $this->db->from('spw_milestones');
        $this->db->where('milestone_id', $id); 
        $query = $this->db->get();                               
                                
        foreach($query->result_array() as $row)
        {
            $oldName = $row['milestone_name'];           
        }
        
        //retrieving all files inside milestone
        $this->db->select('id, path_to_file');
        $this->db->from('spw_uploaded_file');
        $this->db->where('milestone_name', $oldName); 
        $query2 = $this->db->get();
        
        foreach($query2->result_array() as $row)
        {
            $path = $row['path_to_file'];

            $name2 = validateName($name);  
            $oldName2 = validateName($oldName);

            $new = array($name2);
            $old = array($oldName2);

            $newPath = str_replace($old, $new, $path);                  
        }
        
        //update DB for spw_milestones
        $data = array(  'milestone_id'   => $id,
                        'milestone_name' => $name,      
                        'due_date'       => $due_date,
                        'deleted'        => $deleted,
                    );
        $this->db->where('milestone_id', $id);
        $this->db->update('spw_milestones', $data);
        
        //update DB for spw_uploaded_file
        $data2 = array(  'milestone_name' => $name,      
                        'path_to_file'   => $newPath,
                    );
        $this->db->where('milestone_name', $oldName);
        $this->db->update('spw_uploaded_file', $data2);
    }    

    public function delete_milestone($milestone_id)
    {  
        $milestone_name = '';
                    
        //change DB for spw_milestones
        $data = array(  'milestone_id' => $milestone_id,
                        'deleted'      => 'true'
                      );
        $this->db->where('milestone_id', $milestone_id);
        $this->db->update('spw_milestones', $data);        
    }
        
    public function destroy($milestone_id)
    {  
        //retrieving milestone name to be destroyed
        $this->db->select('milestone_name');
        $this->db->from('spw_milestones');
        $this->db->where('milestone_id', $milestone_id); 
        $query = $this->db->get();                               
                
        //update DB for spw_milestones
        $data = array(  'milestone_id' => $milestone_id
                      );
        $this->db->where('milestone_id', $milestone_id);
        $this->db->delete('spw_milestones', $data);
        
        foreach($query->result_array() as $row)
        {
            //update DB for spw_uploaded_file
            $this->db->where('milestone_name', $row['milestone_name']);
            $this->db->delete('spw_uploaded_file');
        }
    }
    
    public function get_files_dirs($milestone_id)
    {  
        $milestone_name = '';
        $paths = array();
        
        //retrieving milestone name
        $this->db->select('milestone_name');
        $this->db->from('spw_milestones');
        $this->db->where('milestone_id', $milestone_id); 
        $query = $this->db->get();           
        
        foreach($query->result_array() as $row)
        {
            $milestone_name = $row['milestone_name'];           
        }        
        
        $query2 = $this->db
               ->where('milestone_name', $milestone_name)
               ->select('path_to_file, file_name')
               ->get('spw_uploaded_file');

        if($query2->num_rows() > 0)
        {
            $i = 0;
            foreach($query2->result_array() as $row)
            {
                $paths[$i] = $row['path_to_file'].$row['file_name'];
                $i++;
            }
            return $paths;
        }
        else
        {
            return null; 
        }
    }
    

    public function get_milestone_dirs($milestone_id)
    {
        $milestone_name = '';
        $paths = array();
        
        //retrieving milestone name
        $this->db->select('milestone_name');
        $this->db->from('spw_milestones');
        $this->db->where('milestone_id', $milestone_id); 
        $query = $this->db->get();           
        
        foreach($query->result_array() as $row)
        {
            $milestone_name = $row['milestone_name'];           
        }        
        
        $query2 = $this->db
               ->where('milestone_name', $milestone_name)
               ->select('path_to_file')
               ->get('spw_uploaded_file');

        if($query2->num_rows() > 0)
        {
            $i = 0;
            foreach($query2->result_array() as $row)
            {
                $paths[$i] = $row['path_to_file'];
                $i++;
            }
            return $paths;
        }
        else
        {
            return null; 
        }
    }
    
    public function get_project_dirs($milestone_id)
    {
        $project_name = '';
        $projects = array();
        
        //retrieving milestone name
        $this->db->select('milestone_name');
        $this->db->from('spw_milestones');
        $this->db->where('milestone_id', $milestone_id); 
        $query = $this->db->get();           
        
        foreach($query->result_array() as $row)
        {
            $milestone_name = $row['milestone_name'];           
        }        
        
        $query2 = $this->db
               ->where('milestone_name', $milestone_name)
               ->select('project_name')
               ->get('spw_uploaded_file');

        if($query2->num_rows() > 0)
        {
            $i = 0;
            foreach($query2->result_array() as $row)
            {
                $projects[$i] = $row['project_name'];
                $i++;
            }
            return $projects;
        }
        else
        {
            return null; 
        }  
    }
        
    public function get_milestone_name($milestone_id)
    {
        $milestone_name = '';
    
        $this->db->select('milestone_name');
        $this->db->from('spw_milestones');
        $this->db->where('milestone_id', $milestone_id); 
        $query = $this->db->get();                               
                                
        foreach($query->result_array() as $row)
        {
            $milestone_name = $row['milestone_name'];  
        }
        return $milestone_name;                        
    }
}
?>