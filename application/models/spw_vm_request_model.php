
<?php

class SPW_vm_request_Model extends CI_Model
{
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /* function returns all requests made for a student, they 
     * could be APPROVED, DENIED, PENDING */
    public function getUserRequestsFromProject($student_id){
        
        $q = $this->db->query("SELECT OS, RAM, storage, numb_vm, status "
                            . "FROM spw_vm_request "
                            . "where student_id = $student_id ");
        $requests = array();
        
        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                array_push($requests,$row);
        
        return $requests;
    }
    
    /* Returns the project requests */
    public function getUserRequests($user_id){
        /* calls helper method and returns the requests made */
        return $this->getUserRequestsFromProject($user_id);
    }
    
    /* Inserts vm requests  */
    public function insertVmRequests($requests,$user_id){
        
        $project_id = $this->getProjectId($user_id);
        $current_term = $this->getCurrentTermName();
        /* for each request insert its settings */
        foreach($requests as $request){
            $os = $request->os;
            $ram = $request->ram;
            $hdd = $request->hdd;
            $qty = $request->qty;
            $query = "insert into spw_vm_request (project_id, OS, RAM, storage, numb_vm, status, student_id, term) "
                    . "values ($project_id,'$os',$ram,$hdd,$qty,'PENDING', $user_id, '$current_term' )";
            $q = $this->db->query($query);
            if(!$q) return false;
        }
        return true;
    }
    
    /*checks if student is in project*/
    public function isStudentInProject($user_id,$project_id){
        $query = "select id from spw_user where id='$user_id' and project='$project_id'";
        $q = $this->db->query($query);
        return $q->num_rows() > 0;
    }
    
    /* Returns the project id where a student belongs */
    public function getProjectId($user_id){
        
        $q = $this->db->query("SELECT project "
                            . "FROM spw_user "
                            . "where id = '$user_id'");
        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                return $row->project;
        return null;
    }
    
    /* returns all pending requests made for a project, they 
     * include id project */
    public function getPendingRequestsFromProject($project_id){
        
        $q = $this->db->query("SELECT id,OS, RAM, storage, numb_vm, status "
                            . "FROM spw_vm_request "
                            . "where status = 'PENDING' AND project_id = $project_id");
        $requests = array();
        
        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                array_push($requests,$row);
        
        return $requests;
    }
    
    /* updates vm requests by project */
    public function updateRequestsFromProject($requests){
        /* for each request update its settings */
        foreach($requests as $request){
            $key = $request->key;
            $os = $request->os;
            $ram = $request->ram;
            $hdd = $request->hdd;
            $qty = $request->qty;
            $status = $request->status;
            
            $query = "update spw_vm_request "
                    . "set OS='$os',RAM=$ram,storage=$hdd,numb_vm=$qty,status='$status' "
                    . "where id = $key";
            $q = $this->db->query($query);
            if(!$q) return false;
        }
        return true;
    }
    
    /* returns a student from a project */
    public function getStudentProjectMember($student_id){
        $query = "SELECT first_name, last_name, email " 
                ."FROM spw_user " 
                ."WHERE id = $student_id ";
        
        $q = $this->db->query($query);
        
        $project_members = array();
        
        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                array_push($project_members,$row);
        
        return $project_members;
    }
    
    /* return the project title*/
    public function getProjectTitle($project_id){
        
        $query = "SELECT title "
               . "FROM spw_project "
               . "WHERE id= $project_id ";
        
        $q = $this->db->query($query);
        
        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                return $row->title;
        return NULL;
    }
    
    /*return project description*/
    public function getProjectDescription($project_id){
        
        $query = "SELECT description "
               . "FROM spw_project "
               . "WHERE id= $project_id ";
        
        $q = $this->db->query($query);
        
        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                return $row->description;
        return NULL;
    }
    
    public function getHeadEmail(){
    
        $query = "SELECT email "
               . "FROM spw_user "
               . "WHERE role= 'head' ";
        
        $q = $this->db->query($query);
        
        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                return $row->email;
        return NULL;
    }
    
    /* set default email of a person who's going to be notified 
    * from head professor about a vm creation */
    public function setEmailToDefault($full_name, $email){
        
        $query = "TRUNCATE TABLE spw_vm_default_email ";
        $q = $this->db->query($query);
        $query = "insert into spw_vm_default_email (full_name, email) "
               . "values ('$full_name', '$email')";
            $q = $this->db->query($query);
            if(!$q) return false;
    }
    
    /* get default name to be shown on the professor interface */
    public function getDefaultName(){
        
        $query = "SELECT full_name " 
                ."FROM spw_vm_default_email ";
        
        $q = $this->db->query($query);
        
        $results = array();
        
        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                return $row->full_name;
        
        return $results;
    }
    
    /* get default email shown on the input field */
    public function getVMDefaultEmailCreation(){

        $query = "SELECT email " 
                ."FROM spw_vm_default_email ";

        $q = $this->db->query($query);

        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                return $row->email;
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
    
    public function getStudentid($req_id){
     
        $query = "SELECT student_id "
                . "FROM spw_vm_request "
                . "WHERE id = $req_id ";
        
        $q = $this->db->query($query);
        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                return $row->student_id;
        return NULL;
    }
    
    
    /*adds new image name to the system*/
    public function addImage($image){
        
        if(!$this->isImageOnSystem($image)){
            $query = "insert into spw_vm_images (image_name, status) "
               . "values ('$image', 'ACTIVE')";
            $q = $this->db->query($query);
            return true;
        }
        return false;
    }
    
    /*checks if an image already exitis in the system*/
    public function isImageOnSystem($image){
        
        $query = "SELECT image_name COLLATE utf8_general_ci " 
                ."FROM spw_vm_images "
                . "WHERE image_name = '$image' ";
        $q = $this->db->query($query);
        if($q->num_rows() > 0){
            return true;
        }
        return false;
    }
    
    /*filters images in the system*/
    public function searchFilteredImages($where){
        
        if($where == ""){
            $query = "SELECT id, image_name, status " 
                ."FROM spw_vm_images ";
        }else{
            $query = "SELECT id, image_name, status " 
                    ."FROM spw_vm_images "
                    . "WHERE ".$where." ";
        }
        $q = $this->db->query($query);
        
        $results = array();
        
        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                array_push($results,$row);
        
        return $results;
    }
    
    
    
    /*return all active images in the system */
    public function getActiveImages(){
        
         $query = "SELECT image_name " 
                ."FROM spw_vm_images "
                . "WHERE status = 'ACTIVE' ";
        $q = $this->db->query($query);
        $results = array();
        
        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                array_push($results,$row);
        
        return $results;
    }
    
    /*return all images in the system */
    public function getAllImages(){
        
         $query = "SELECT image_name " 
                ."FROM spw_vm_images ";
        $q = $this->db->query($query);
        $results = array();
        
        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                array_push($results,$row);
        
        return $results;
    }
    
    /*update image status*/
    public function updateImageStatus($status,$image){
        
        $query = "update spw_vm_images "
                    . "set status = '$status' "
                    . "where image_name = '$image' ";
            $q = $this->db->query($query);
            if($q) return true;
            else return false;
    }
    
    /*delete an image name from the system*/
    public function deleteImage($image){
        
        /*delete any request wth image matching*/
        $query = "DELETE FROM spw_vm_request "
               . "where OS = '$image' ";
        $q = $this->db->query($query);
        /*delete image name from system*/
        $query = "DELETE FROM spw_vm_images "
                 . "where image_name = '$image' ";

        $q = $this->db->query($query);
        
        if($q) return true;
        else return false;
    }
    
    /* returns al requests in the system */
    public function getVMRequests(){

        $q = $this->db->query("SELECT r.id, r.OS, r.RAM, r.storage, r.numb_vm, CONCAT_WS(' ', u.first_name, u.last_name) AS student_name, r.status, r.term "
                            . "FROM spw_vm_request r INNER JOIN spw_user u ON r.student_id = u.id ");
        $requests = array();
        
        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                array_push($requests,$row);
        
        return $requests;
    }
    
    /*filters vm in the system*/
    public function searchFilteredVms($where){
        
        if($where == ""){
            $query = "SELECT r.id, r.OS, r.RAM, r.storage, r.numb_vm, CONCAT_WS(' ', u.first_name, u.last_name) AS student_name, r.status, r.term "
                   . "FROM spw_vm_request r INNER JOIN spw_user u ON r.student_id = u.id ";
        }else{
        $query = "SELECT r.id, r.OS, r.RAM, r.storage, r.numb_vm, CONCAT_WS(' ', u.first_name, u.last_name) AS student_name, r.status, r.term "
               . "FROM spw_vm_request r INNER JOIN spw_user u ON r.student_id = u.id "
                . "WHERE ".$where." ";
        }
        $q = $this->db->query($query);
        
        $results = array();
        
        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                array_push($results,$row);
        
        return $results;
    }
    
    /*retrive student's full name*/
    public function getStudentName($user_id){
        
        $query = "SELECT CONCAT_WS(' ', first_name, last_name) AS full_name "
       . "FROM spw_user "
       . "WHERE id = $user_id ";

        $q = $this->db->query($query);
        
        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                return $row->full_name;
        return NULL;
    }
    
    /*retrive current term*/
    public function getCurrentTermName(){
        
        $query = "SELECT name "
                . "FROM spw_term "
                . "WHERE start_date IN "
                         . "(SELECT MAX(start_date) "
                         . " FROM spw_term) ";
        
        $q = $this->db->query($query);
        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                return $row->name;
        return NULL;

    }
    
    public function getStudentEmail($req_id){

        $query = "SELECT email " 
        ."FROM spw_user " 
        ."WHERE id IN "
                . "(SELECT student_id "
                . " FROM spw_vm_request "
                . " WHERE id = $req_id ) ";
        
        $q = $this->db->query($query);
        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                return $row->email;
        return NULL;
        
    }
    
    /*update an image name in the system*/
    public function editImage($old_image, $new_image){
        
        $query = "update spw_vm_images "
               . "set image_name = '$new_image' "
               . "where image_name = '$old_image' ";
        $q = $this->db->query($query);
        if($q){
            $query = "update spw_vm_request "
                   . "set OS='$new_image' "
                   . "where OS = '$old_image' ";
            $q = $this->db->query($query);
        }
        if($q) return true;
        else return false;
    }
    
    /*return all images attributes*/
    public function allImages(){
        
        $query = "SELECT id, image_name, status " 
                    ."FROM spw_vm_images ";
        $q = $this->db->query($query);
        
        $results = array();
        
        if($q->num_rows() > 0)
            foreach ($q->result() as $row)
                array_push($results,$row);
        
        return $results;
    }
    
    /* updates images requests by project */
    public function updateImageRequests($requests){
        /* for each request update its settings */
        foreach($requests as $request){
            $key = $request->key;
            $image = $request->image;
            
            $query = "update spw_vm_images "
                    . "set image_name = '$image' "
                    . "where id = $key ";
            $q = $this->db->query($query);
            if(!$q) return false;
        }
        return true;
    }
    
    public function deleteVMRequest($request_id){
        
        $query = "DELETE FROM spw_vm_request "
               . "where id = $request_id ";
        $q = $this->db->query($query);
        
        if($q) return true;
        else return false;
    }
   
}

