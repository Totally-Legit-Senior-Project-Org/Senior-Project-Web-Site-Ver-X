<?php

class SPW_Term_Model extends CI_Model
{
	public $id;
	public $name;
	public $description;
	public $start_date;
	public $end_date;
	/* this field is the due date where users can choose projects
	   or leave and join another */
	public $closed_requests;
        

        public static function getInstance()
        {
            static $inst = null;
            if ($inst === null)
                $inst = new SPW_Term_Model();
            return $inst; 
        }
        
	public function __construct()
	{
            //parent::__construct();
	}
	
        public function currentDateUnderDeadline()
        {
              $deadline = $this->getCurrentTermInfo();
              $currentDate = date("m-d-Y");
              $startDate = date("m-d-Y", strtotime($deadline->start_date));
              $endDate = date("m-d-Y", strtotime($deadline->end_date));
              
              if(($startDate <= $currentDate) && ($currentDate <=  $endDate))   
              {
                  return true;
              }
              else return false;           
        }
	/* get the current term information */
	public function getCurrentTermInfo()
	{
		//$sql = 'select * from spw_term
                   // where (start_date <= NOW()) and (end_date > NOW())';
            
                $sql = 'select * from spw_term limit 1';
		
                $query = $this->db->query($sql);

		if ($query->num_rows() > 0)
		{
			$res = $query->row(0, 'SPW_Term_Model');

			return $res;
		}

		return NULL;
	}
        
        public function setDeadline($start, $end)
        {
            $updateData = array('start_date' => $start,
                    'end_date' => $end);
            $this->db->update('spw_term', $updateData);      
        }

	//get all valid terms
	public function getAllValidTerms()
	{
		$sql = 'select *
				from spw_term
				where (end_date > NOW())
				order by end_date ASC';
		$query = $this->db->query($sql);

		$termNum = $query->num_rows();
		$lTerms = array();

		if ($termNum > 0)
        {
        	for ($i = 0; $i < $termNum; $i++)
        	{
        		$row = $query->row($i, 'SPW_Term_Model');
				$lTerms[] = $row;
        	}
        }

        return $lTerms;
	}

	public function searchQueriesOnTermForUsers($keyword)
	{
		$keyword = '%'.$keyword.'%';
		
		$param[0] = $keyword;
		$param[1] = $keyword;

		$sql = "select spw_project.id
				from spw_project, spw_term
				where (spw_project.delivery_term = spw_term.id) and (spw_project.status <> 4)
					   and ((spw_term.name like ?) or (spw_term.description like ?))";
		$query = $this->db->query($sql, $param);

		if ($query->num_rows() > 0)
		{
			$user = new SPW_User_Model();
			return $user->dumpQueryIdsOnArray($query);
		}
		else
			return NULL;
	}

	public function searchQueriesOnTermForProjects($keyword)
	{
		$keyword = '%'.$keyword.'%';
		
		$param[0] = $keyword;
		$param[1] = $keyword;

		$sql = "select spw_user.id
				from spw_user, spw_term
				where (spw_user.graduation_term = spw_term.id)
					   and ((spw_term.name like ?) or (spw_term.description like ?))";
		$query = $this->db->query($sql, $param);

		if ($query->num_rows() > 0)
		{
			$user = new SPW_User_Model();
			return $user->dumpQueryIdsOnArray($query);
		}
		else
			return NULL;
	}

	public function getFutureTerms()
	{
		$queryStr = ' select * 
					  from spw_term
					  where end_date > NOW()	
					';

		$query = $this->db->query($queryStr); 			
					
		if($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		
		return NULL;
	}
        
        /* added in SPW v5 
        * get default email and name to be shown on the table */
        public function getDefaultEmailAndName(){
        
        $query = "SELECT full_name, email " 
                ."FROM spw_vm_default_email ";
        
        $q = $this->db->query($query);
        
        $results = array();
        
        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                array_push($results,$row);
        
        return $results;
    }
}
	
?>
