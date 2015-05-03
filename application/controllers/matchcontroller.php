<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
session_start();

class Student {

    public $id;
    public $name;
    public $skills = array();
    public $wildcard = false; //if student has not ranked rank mimimum projects
    public $scoreList = array();//score of all projects if less than 1 its unwanted
    public $projList = array();//list of project this student wants
    public $PR = array();//PR[id] = project rating for project id
    public $relevant = array();//current relevant skills in a tree
    public $amountContributing = 0;
    public $traversal = 0; //index of current traversal
    public $iProjList = array();//iterative array for projList
    public $missingSkills = array();//skills student missing in project currently added
    public $fulfilledSkills = array();//skills sutdent has for current project
    public $overflowSkills = array();//skills not contributing to current project
    public $forced = false;//if student was forced into project
    public function __construct($id, $n, $skills, $scoreList) {
        $this->id = $id;
        $this->name = $n;
        $this->skills = $skills;
        $this->scoreList = $scoreList;
        asort($this->scoreList);
    }

}

//class to record a project list's metadata in order to report match consequence
class ProjectListMetaData{
    public $avgInterest;
    public $avgTotalSkill;
    public $avgAvgFulfillment;
    public $totalOverflow;
    
    public function __construct($interest,$totalS,$avg,$totalO) {
        $this->avgAvgFulfillment = $avg;
        $this->avgInterest = $interest;
        $this->totalOverflow = $totalO;
        $this->avgTotalSkill = $totalS;
    }
}

class Project {

    public $id;
    public $name;
    public $skills = array();
    public $desiredStudents = array();
    public $score;
    public $min = 1;//might change in future
    public $max;
    public $studScore = 1; //student score , note these are for projects stored in students 
    public $studSkillPercent = 0;//student skill percent acheived
    public $missingSkills = array();
    public $fulfilledSkills = array();
    public $skillAmount = 0;
    public function __construct($id, $name, $skills, $score, $max) {
        $this->id = $id;
        $this->name = $name;
        $this->skills = $skills;
        $this->score = $score;
        $this->max = $max;
    }

    public function checkStudent(Student $studentToAdd) {
        $intersect = array_intersect($this->skills, $studentToAdd->skills);
        if (count($intersect) > 0) {
            return true;
        }
        return false;
    }
    
    //evaluate s as worse than sw (true or false) given criteria compromise
    public function evaluateAsWorse($s,$sw,$compromise) {
        
        $sPercent = calculateSkillPercent($this, $s);
        $swPercent = calculateSkillPercent($this, $sw);
        $sScore = $s->scoreList[$this->id];
        $swScore = $sw->scoreList[$this->id];
        $sAmount = count($s->skills);
        $swAmount = count($s->skills);
        
        //in case of double tie whoever has less skills overall gets in
        if($compromise){//percent more important than score
            if($sPercent == $swPercent){
                
                if($sScore == $swScore){
                    return $sAmount > $swAmount;
                }
                
                return $sScore < $swScore;
            }
            return $sPercent < $swPercent;
        }
        else{//vice vera
            if($sScore == $swScore){
                
                if($sPercent == $swPercent){
                    return $sAmount > $swAmount;
                }
                
                return $sPercent < $swPercent;
            }
            return $sScore < $swScore;
        }
    }
    
    //get worst student of p given criteria
    public function getWorst($compromise) {
        
        $sw = null;
        
        foreach($this->desiredStudents as $s){
            if($sw == null){
                $sw = $s;
            }
            elseif($this->evaluateAsWorse($s,$sw,$compromise)){
                $sw = $s;
            }
        }
        return $sw;
        
    }
    
    //see if s is better fit for p then sw
    public function betterFit($s,$sw,$compromise) {
        
        if(!$this->evaluateAsWorse($s,$sw,$compromise)){//if not worse
            
            return true;//remove sw put in s, return true s is better fit
        }
        return false;//s not better fit
        
    }
    
    //is filled?
    public function filled() {
        return (count($this->desiredStudents) == $this->max);
    }
    public function addStudent($s) {
        $this->desiredStudents[$s->id] = $s;
    }
    public function removeStudent($s) {
        unset($this->desiredStudents[$s->id]);
    }

    public function addDesiredStudent(Student $studentToAdd) {
        array_push($this->desiredStudents, $studentToAdd);
    }
    
    //Calculate skill metadata for later display
    public function generateSkillMetaData(){
        $studentsSkills = array();
        
        //get student culmulative skills
        foreach($this->desiredStudents as $s){
            $studentsSkills = array_unique(array_merge($studentsSkills,$s->skills));
        }
        //get skills  project still needs and has fulfilled
        $this->missingSkills = array_diff($this->skills,$studentsSkills);
        $this->fulfilledSkills = array_diff($this->skills, $this->missingSkills);
        sort($this->skills);
        //for each student find out what skills they're missing for this project
        //what skills they have for it and what skills are not needed
        foreach ($this->desiredStudents as $k => $s) {
            $s->missingSkills = array_diff($this->skills, $s->skills);
            $s->fufilledSkills = array_diff($this->skills, $s->missingSkills);
            $s->overflowSkills = array_diff($s->skills, $this->skills);
            $this->desiredStudents[$k] = $s;
        }
        sort($this->missingSkills);
        sort($this->fulfilledSkills);
    }
    public function calculateAvgInterest() {
        if(count($this->desiredStudents)==0){
            return 0;
        }
        $avg = 0;
        foreach ($this->desiredStudents as $s) {
            $avg = $s->scoreList[$this->id] + $avg;
        }
        return round($avg/count($this->desiredStudents));
        
    }
    public function calculateTotalFulfillment() {
        if(count($this->skills)==0){
            return 100;
        }
        
        return round(100*(count($this->fulfilledSkills)/count($this->skills)));
    }
    public function calculateAvgFulfillment() {
        if(count($this->desiredStudents)==0){
            return 0;
        }
        $avg = 0;
        foreach ($this->desiredStudents as $s) {
            $avg = $this->figureSkillContribution($s) + $avg;
        }
        return round($avg/count($this->desiredStudents));
    }
    public function calculateTotalOverflow() {
        $total = 0;
        foreach ($this->desiredStudents as $s){
            $total += count($s->overflowSkills);
        }
        return $total;
    }
    public function figureSkillContribution($s){
        
        if(count($this->skills)==0){
            return 100;
        }
        
        $diff = array_diff($this->skills, $s->skills);
        $diff2 = array_diff($this->skills, $diff);
        
        return round(100*(count($diff2)/count($this->skills)));
    }
}

class Team {

    public $projectID;
    public $projName;
    public $score;
    public $members = array();
    public $skillsNeeded = array();
    public $studentIDs = array();

    public function __construct($projectID, $projName, $score, $skills) {
        $this->projectID = $projectID;
        $this->projName = $projName;
        $this->score = $score;
        $this->skillsNeeded = $skills;
    }
 
    public function addStudents($students) {
        foreach ($students as $s) {
            $this->members[$s->id] = $s;
            $this->studentIDs[$s->id] = $s->id;
            $this->skillsNeeded = array_diff($this->skillsNeeded, $s->skills);
        }
    }

    public function getStudents() {
        return $this->members;
    }

    public function checkForStudent(Student $student) {
        return in_array($student->id, $this->studentIDs);
    }

}

class TentativeMatch {

    public $projects = array();
    public $studentScore = 0;
    public $profScore = 0;

    public function __construct($projects) {
        $this->projects = $projects;
    }

}

//sort by professor score
function compare_ranks($a, $b) {
    if ($a->score == $b->score) {//sort by prof score first
        if(count($a->skills) == count($b->skills)){//by skill amount second
            if($a->max == $b->max){//by number of positions last
                return 0;
            }
            return ($a->max > $b->max) ? -1 : 1;
        }
            
        return(count($a->skills) > count($b->skills)) ? -1 : 1;
    }
    return ($a->score > $b->score) ? -1 : 1;
}

function compare_prof_score($a, $b) {
    if ($a->profScore == $b->profScore) {
        return 0;
    }
    return ($a->profScore < $b->profScore) ? 1 : -1;
}
function compare_students($a,$b){//ERROR maybe
    $s1 = count($a->relevant);//first critria current relevancy
    $s2 = count($b->relevant);
    $s11 = $a->amountContributing;//next criteria total relevancy
    $s22 = $b->amountContributing;
    $s111= count($a->skills);//last criteria amount of overflow
    $s222 = count($b->skills);
    
    if($s1 == $s2){
        if($s11 == $s22){
            if($s111==$s222){
                return 0;
            }
            return ($s111 < $s222) ? -1 : 1;//less wasting of skills more prominance
        }
        return ($s11 < $s22) ? 1 : -1;//more total relevant more prominance
    }
    
    return ($s1 < $s2) ? 1 : -1;//more relevant skill higher prominance
}

//sorting student's projects to traverse their interested in projects
//second layer of comparison mainly for wildcard students
function compare_student_project($a,$b) {
    
    if($a->studScore == $b->studScore){
        
        if(count($a->skills) == 0 && count($b->skills)==0){//special case to avoid 100% on no skill projects
            return 0;
        }
        elseif (count($a->skills) == 0) {
            return 1;
        }
        elseif (count($b->skills) == 0) {
            return -1;
        }
        
        if($a->skillAmount == $b->skillAmount){
            return 0;
        }
        
        return ($a->skillAmount < $b->skillAmount) ? 1:-1;
        
    }
    
    return ($a->studScore < $b->studScore) ? 1: -1;
    
}

//calculate the amount of skills students have for project over skills for project as percentage
function calculateSkillPercent($p,$s){
    
    $pSkills = $p->skills;
    if(count($pSkills)==0){
        return 100;
    }
    $sSkills = $s->skills;
    $diff = array_diff($pSkills, $sSkills);
    $intersect = array_diff($pSkills,$diff);
    
    
    
    return round(100*(count($intersect)/count($pSkills)));
}

function array_count_values_ignore_case($array) {
    $countArr = array();
    foreach ($array as $value) {
        foreach ($countArr as $key2 => $value2) {
            if (strtolower($key2) == strtolower($value)) {
                $countArr[$key2] ++;
                continue 2;
            }
        }
        $countArr[$value] = 1;
    }
    return $countArr;
}

function checkForDups($array) {
    $subset_of_two = subsets_equal_to_n($array, 2);
    foreach ($subset_of_two as $s) {
        $values = call_user_func_array('array_intersect', $s);
        if (!empty($values))
            return true;
    }
    return false;
}

function countOnes($str) {
    return strlen(str_replace("0", "", $str));
}

function subsets_up_to_n($arr, $n) {
    $len = pow(2, count($arr));
    $subset = array();
    $a = array();
    $one = '1';
    for ($i = 1; $i < $len; $i++) {
        $bin = decbin($i);
        $a = array();
        $pos = 0;
        if (countOnes($bin) <= $n) {
            for ($j = count($arr) - strlen($bin); $j < count($arr); $j++) {
                if ($bin[$pos] == $one) {
                    array_push($a, ($arr[$j]));
                }
                $pos++;
            }
            //var_dump($a);
            array_push($subset, ($a));
        }
    }
    return $subset;
}

function subsets_equal_to_n($arr, $n) {
    $len = pow(2, count($arr));
    $subset = array();
    $a = array();
    $one = '1';
    for ($i = 0; $i < $len; $i++) {
        $bin = decbin($i);
        $a = array();
        $pos = 0;
        if (countOnes($bin) == $n) {
            for ($j = count($arr) - strlen($bin); $j < count($arr); $j++) {
                if ($bin[$pos] == $one) {
                    array_push($a, ($arr[$j]));
                }
                $pos++;
            }
            array_push($subset, ($a));
        }
    }
    return $subset;
}

function combinations($arr, $max) {
    $indexes = array_fill(0, count($arr), 0);//get count entries of 0's
    $tot = array();
    while (true) {
        $vm = array();
        for ($k = 0; $k < count($indexes); $k++) {
            array_push($vm, $arr[$k][$indexes[$k]]);//push [0][0] = 0? [1][0] = 0? i.e. first team for each project?
        }
        $numStudents = 0;
        $memberIDs = array();
        foreach ($vm as $team) {
            $numStudents += count($team->members);//counter number of students
            array_push($memberIDs, $team->studentIDs);//push array of id to member id's
        }
        if ($numStudents == $max) {//if it is max? huh shouldnt it always be true?
            if (!(checkForDups($memberIDs))) {//check if student in more than one team
                $tm = new TentativeMatch($vm);//mark this matching as posssible
                $profScore = 0;
                foreach ($tm->projects as $p) {//for each project in tm
                    $projectRank = $p->score;//get priority rank
                    $pScore = 0;
                    foreach ($p->members as $member) {//for each member in p
                        if (array_key_exists($p->projectID, $member->scoreList)) {//check if member want this project
                            $studentScore = $member->scoreList[$p->projectID];
                            $tm->studentScore += $studentScore;//student score of project overall
                            $pScore += $studentScore;//pscore is equal to all score of students in team for this project
                        }
                    }
                    $profScore += ($pScore * $projectRank);//count both project rank and student score for match score
                }
                $tm->profScore += $profScore;//update match's score
                if (count($tot) < 20) {//if less than 20 matchings keep pushing
                    array_push($tot, $tm);
                    usort($tot, 'compare_prof_score');//sort
                } else {
                    if ($tm->profScore > $tot[count($tot)-1]->profScore) {
                        unset($tot[count($tot)-1]);
                        array_push($tot, $tm);
                        usort($tot, 'compare_prof_score');//sort
                    }
                }
                // sort by professor score from highest score to lowest
            }
        }
        $j = count($indexes) - 1;
        while (true) {
            $indexes[$j] ++;
            if ($indexes[$j] < count($arr[$j])) {
                break;
            }
            $indexes[$j] = 0;
            $j--;
            if ($j < 0) {//exit when all matches considered?
                //print_r($tot[0]);
                return $tot;
            }
        }
    }
}

class MatchController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('flash_message');
        $this->load->helper('project_summary_view_model');
        load_project_summary_models($this);
        $this->load->model('spw_match_model');
        
    }

    public function index() {
        $_SESSION['text'] = 'sadfa';
        if (isUserLoggedIn($this)) {
            $tempProject = new SPW_Project_Model();
            $tempUser = new SPW_User_Model();
            $user_id = getCurrentUserId($this);
            $maxNums = $this->spw_match_model->getAllProjectsMaxStudents();
            $ranks = $this->spw_match_model->getRanks($user_id);

            if (empty($ranks)) {
                $no_ranks = true;
            } else {
                $no_ranks = false;
            }

            if ($user_id) {
                $lRegularProjectsIds = $tempProject->getApprovedProjectsIds();
            }

            $lRegularProjects = $this->prepareProjectsToShow($lRegularProjectsIds);


            if ((!isset($lRegularProjects) || count($lRegularProjects) == 0)) {
                $no_results = true;
            } else {
                $no_results = false;
            }

            $data['title'] = 'Current Project';
            $data['no_results'] = $no_results;

            $projects = $lRegularProjects;
            $data['lRegularProjects'] = $lRegularProjects;
            $data['maxNums'] = $maxNums;
            $data['no_ranks'] = $no_ranks;
            $data['ranks'] = $ranks;

            $this->load->view('match_page', $data);
        }
    }

    private function getRegularProjectsForCurrentUser($lSuggProjectIds) {
        return $this->SPW_User_Model->getRegularProjectIds($lSuggProjectIds, getCurrentUserId($this));
    }

    private function prepareProjectsToShow($lProjectsIds) {
        $user_id = getCurrentUserId($this);

        if ($this->SPW_User_Model->isUserAdmin($user_id)) {
            //do nothing for now
        } else {
            //$belongProjectIdsList = $this->SPW_User_Model->userHaveProjects($user_id);
            return $this->SPW_Project_Summary_View_Model->prepareProjectsDataToShow($user_id, $lProjectsIds, NULL, FALSE);
        }
    }
    
    public function matchStart() {
        $this->load->view('match_start_page');
    }

    public function saveRank() {
        if (isUserLoggedIn($this)) {
            $tempProject = new SPW_Project_Model();
            $user_id = getCurrentUserId($this);
            $tempUser = new SPW_User_Model();
            $lRegularProjectsIds = $tempProject->getApprovedProjectsIds();
            $min = $this->spw_match_model->getMinimum();
        }

        $rank = array();
        foreach ($lRegularProjectsIds as $id) {
            $rank[$id] = $this->input->post($id);
        }

        foreach ($rank as $id => $r) {
            if (!is_numeric($r)) {
                $msg = "Nothing saved; non-numeric characters are disallowed.";
                setErrorFlashMessage($this, $msg);
                if ($tempUser->isUserAStudent($user_id)) {
                    redirect('home');
                } else {
                    redirect('match');
                }
            }
            if($r > 100){
                $msg="Please rank between 1 and 100.";
                if ($tempUser->isUserAStudent($user_id)) {
                    setErrorFlashMessage($this, $msg);
                    redirect('home');
                } else {
                    $msg = $msg." Remember 0 for no consideration, 1 for not important, 2-100 for raning VIPs.";
                    setErrorFlashMessage($this, $msg);
                    redirect('match');
                }
            }
            if ($tempUser->isUserAStudent($user_id)) {
                if ($r == 0) {
                    $msg = "Nothing saved; zero is disallowed. Need to interest rank projects greater than 0.";
                    setErrorFlashMessage($this, $msg);
                    redirect('home');
                }
            } else {
                if ($r < 0) {
                    $msg = "Nothing saved; negatives are disallowed. Need to priority rank projects greater than -1.";
                    setErrorFlashMessage($this, $msg);
                    redirect('match');
                }
            }
        }

        $data = array(
            'user' => $user_id,
            'rank' => $rank,
        );

        if ($tempUser->isUserAStudent($user_id)) {
            $rem = 0;
            foreach ($rank as $id => $r) {
                if ($r > -1) {
                    $rem++;
                }
            }
            $this->spw_match_model->insertRanks($data);

            if ($rem < $min) {
                $need = $min - $rem;
                $msg = "Saved interest ranking information successfully, but you need to rank $min project(s) because it is the required minimum."
                        . "<br><br>You need to rank $need more project(s).";
                setErrorFlashMessage($this, $msg);
            } else {
                setFlashMessage($this, "Saved interest ranking information successfully.");
            }

            redirect('home');
        } else {
            foreach ($data['rank'] as $r) {
                if (!is_numeric($r)) {
                    $msg = "Nothing saved; non-numeric characters are disallowed.";
                    setErrorFlashMessage($this, $msg);
                    redirect('match');
                }
            }
            $this->spw_match_model->insertRanks($data);
            setFlashMessage($this, "Saved priority ranking information successfully.");
            redirect('match');
        }
    }
    
    public function saveMatchings() {
        $PL = array_merge($_SESSION['globalMC']['VIPfinal'],$_SESSION['globalMC']['OtherP']);
        
        foreach ($PL as $p) {
            foreach ($p->desiredStudents as $s) {
                /*$data = array(
                'user' => $s->id,
                'rank' => $p->id,
                );*/
                $this->spw_match_model->addStudentToProject($s->id,$p->id);
            }
        }
                    
            setFlashMessage($this, "Saved match configuration successfully.");
            redirect('match');
        
    }

    public function saveMinimum() {
        $rank = htmlspecialchars($this->input->post('rank'));
        if (!is_numeric($rank)) {
            $msg = "Nothing saved; non-numeric characters are disallowed.";
        }
        if ($rank < 0) {
            $msg = "Nothing saved; negatives are disallowed.";
        }
        if (isset($msg)) {
            setErrorFlashMessage($this, $msg);
        } else {
            $this->spw_match_model->setMinimum($rank);
            setFlashMessage($this, "Saved minimum successfully.");
        }
        redirect('admin/admin_dashboard');
    }
    
    public function prepareProjects() {
        $approvedProjects = $this->spw_match_model->getAllApprovedProjectIDs(); //array for approved project's id numbers
        $projectsMaxStudents = $this->spw_match_model->getAllProjectsMaxStudents(); //array for all projects max student that can joined ([id] gives number for id)
        $projectRanks = $this->spw_match_model->getRanksForProjects();//array by [project id] = {rank1}? only head prof?
        $projectTitles = $this->spw_match_model->getProjectNames();//[project id] = name

        $PL = array();//project list
        foreach ($approvedProjects as $p => $id) {//have each project p identify for id p not used?
            if(isset($projectRanks[$id])){
                $rank = $projectRanks[$id];//get rank
            }
            else{
                $rank=0;
            }
            if ($rank > 0) {//if rank non-zero
                $tempProject = new Project($id, $projectTitles[$id], $this->spw_match_model->getSkillsForProject($id), $rank, $projectsMaxStudents[$id]);//construct project
                array_push($PL, $tempProject);//push project to list, note project is made of id, name, skill array project req, head prof rank, max students that can join
            }
        }
        return $PL;
    }

    public function prepareStudents() {
        $activeStudents = $this->spw_match_model->getAllActiveStudentIDs();//arr[i] = id?
        $studentNames = $this->spw_match_model->getStudentNames();// arr[id] = fname + " " + lname

        $SL = array();
        foreach ($activeStudents as $id) {
            $tempStudent = new Student($id, $studentNames[$id], $this->spw_match_model->getSkillsForStudent($id), $this->spw_match_model->getRanksForStudent($id));
            $SL[$id] = $tempStudent;
        }
        return $SL;//students are id, name, arr skills, arr of ranks [pro id] = rank
    }
    public function gotoProjectPriority() {
                $_SESSION['text'] = 'sadfa';
        if (isUserLoggedIn($this)) {
            $tempProject = new SPW_Project_Model();
            $tempUser = new SPW_User_Model();
            $user_id = getCurrentUserId($this);
            $maxNums = $this->spw_match_model->getAllProjectsMaxStudents();
            $ranks = $this->spw_match_model->getRanks($user_id);

            if (empty($ranks)) {
                $no_ranks = true;
            } else {
                $no_ranks = false;
            }

            if ($user_id) {
                $lRegularProjectsIds = $tempProject->getApprovedProjectsIds();
            }

            $lRegularProjects = $this->prepareProjectsToShow($lRegularProjectsIds);


            if ((!isset($lRegularProjects) || count($lRegularProjects) == 0)) {
                $no_results = true;
            } else {
                $no_results = false;
            }

            $data['title'] = 'Current Project';
            $data['no_results'] = $no_results;

            $projects = $lRegularProjects;
            $data['lRegularProjects'] = $lRegularProjects;
            $data['maxNums'] = $maxNums;
            $data['no_ranks'] = $no_ranks;
            $data['ranks'] = $ranks;

        
        $this->load->view('project_priority_page', $data);
        }
    }
    
    /*
     * Generate random student data for testing purposes. This function takes
     * real students/project and generates random scoring for projects by students
     * random declartion of students as wildcard random chance to not rank project etc
     */
    public function generateDemoData($PL,$SL,$min) {
        $skillBank =array();
        foreach ($PL as $p){
            foreach ($p->skills as $s) {
                array_push($skillBank, $s);
            }
        }
        foreach($SL as $s){
            $wildcard = mt_rand(1, 100);
            $skillAmount = mt_rand(1,25);
            
            if($wildcard < 11){               
                foreach ($PL as $v) {
                        $s->scoreList[$v->id] = -1;
                }
            }
            else{
                foreach ($PL as $v) {
                    if(mt_rand(1, 100) > 25){
                        $s->scoreList[$v->id] = mt_rand(-1, 100);
                    }
                    else{
                        $s->scoreList[$v->id] = -1; 
                    }
                }
            }
            foreach ($skillBank as $skill){
                if(mt_rand(1, 100) < 11){
                    array_push($s->skills,$skill);
                    if(count($s->skills) > $skillAmount){
                        break;
                    }
                }
            }
            $s->skills = array_unique($s->skills);
            
        }
        return $SL;
        
    }
    
    public function gotoAuto() {
        $this->preProcessSteps(true);
    }
    public function gotoManual() {
        $this->preProcessSteps(false);
    }
    
    //Prepare database data  to data for matchmaking V4
    public function preProcessSteps($auto) {
        // PREPARE PROJECTS
        unset($_SESSION['globalMC']);//global match controller variable (unset to refresh sessions)
        $PL = $this->prepareProjects();
        /*foreach($PL as $p){
        //    $p->score =0;
        */ //use this to test no ranked projects
        //PREPARE STUDENTS
        $SL = $this->prepareStudents();
        //minimum projects a student should have ranked
        $min = $this->spw_match_model->getMinimum();
        
        
        //IMPORTANT: Future developers uncomment the following to run test data for students change head prof data manually
        //change the function if you want to alter randomization details
        //$SL = $this->generateDemoData($PL,$SL,$min);
        
        $VIP = array();//very important projects
        $PPL = array();//processed project list
        $PSL = array();//processed student list
    
        foreach ($SL as $s){//populate student data
            $valid = false;//see if s ranked "rank mimimum"
            $count = 0; //count for rank mimimum
            sort($s->skills);//sort student skills useful for heuristic time save
            
            foreach ($s->scoreList as $score){//cehck if they ranked rank min
                if($score > 0){
                    $count++;
                }
                if($count == $min){
                    $valid = true; break;
                }
            }
            
            foreach($PL as $p){//for each project (assuming valid if not this is overwritten)
                if(array_key_exists($p->id, $s->scoreList) && $s->scoreList[$p->id] > 0){//check if student ranked it and project is considered
                    $s->projList[$p->id] = $p;//push to student's project list
                }
                else{
                    $s->projList[$p->id] = $p;//push to student's project list
                    $s->scoreList[$p->id] = -1;//reaffirm ranked as -1
                }
            }
            if(!$valid){//make student wildcard (did not rank rank minimum)
                $s->wildcard = true;// this will be used for special things
                $s->projList = array();
                foreach($PL as $p){//push all projects onto student
                    $s->projList[$p->id] = $p;;
                    $s->scoreList[$p->id] = 1;//wildcard students don't have opinions
                }                
            }
            foreach($s->projList as $p){
                    $p->studScore = $s->scoreList[$p->id];
                    $p->studSkillPercent = round(calculateSkillPercent($p,$s));
                    $p->skillAmount = count(array_intersect($p->skills,$s->skills));
            }
      
            $s->iProjList = $s->projList;
            usort($s->iProjList, 'compare_student_project');
            $s->iProjList = array_values($s->iProjList);//iterative project list
            
            array_push ($PSL, $s);
        }
        //put projects in VIP listing or normal listing
        foreach($PL as $p){
            if($p->score > 1){
                $VIP[$p->id] = $p;
            }
            elseif($p->score == 1){
                $PPL[$p->id] = $p;
            }
        }
        
        usort($VIP,"compare_ranks");//sort VIP from most to least important
        
        //$VIP = $this->doMatchPhase1($VIP,$SL);//do matching for VIP
        
        //$data['VIP'] = $VIP;
        
        //$SL = $this->
        $VIP = array_values($VIP);
        $_SESSION['globalMC']['VIP'] = $VIP;
        $_SESSION['globalMC']['PL2'] = $PPL;
        $_SESSION['globalMC']['SL'] = $PSL;
        if(count($VIP) == 0){
            $_SESSION['globalMC']['SLcombo'] = $PSL;
            $_SESSION['globalMC']['VIPfinalMD'] = new ProjectListMetaData(0, 0, 0, 0);
            $_SESSION['globalMC']['VIPfinal'] = $VIP;
            $this->doMatchPhase2();
        }
        elseif($auto){
            $this->doMatchPhase1Auto($VIP, $PSL);
        }
        else{
            $this->doMatchPhase1Manual($VIP, $PSL,0);
        }
        
    }
    //helps move things along with view
    public function matchPhase1Helper() {
        $index = $_SESSION['globalMC']['indexM'];
        $goauto = array_pop($_POST);
        $studentReturn = $_POST;
        
        foreach ($studentReturn as $key => $value) {
            $_SESSION['globalMC']['VIPfinal'][$index-1]->addStudent($this->unsetStudent($_SESSION['globalMC']['SLcombo'],$key));
        }
        if($goauto == "Do Rest Automatically"){//tied to button press
            for($i=0; $i< $index; $i++){
                $_SESSION['globalMC']['VIPman'][$i] = clone $_SESSION['globalMC']['VIPfinal'][$i];
                unset($_SESSION['globalMC']['VIPfinal'][$i]);
            }
            $this->doMatchPhase1Auto($_SESSION['globalMC']['VIPfinal'], $_SESSION['globalMC']['SLcombo']);
        }
        else{
         $this->doMatchPhase1Manual(NULL, NULL, $index);   
        }
        
    }
    //unset array value the hard way
    public function unsetStudent($arr,$id) {
        $theV = null;
        foreach ($arr as $key => $value) {
            if($value->id == $id){
                $theV = $value;
                unset($_SESSION['globalMC']['SLcombo'][$key]);
            }
        }
        return $theV;
    }
    
    //VIP match v4 manual
    public function doMatchPhase1Manual($PL, $SL, $indexM) {
        
            if($indexM == 0){//generate start data
                $VIPf = $this->cloneProjects($PL);//VIP project to have friendly result
                $VIPs = $this->cloneProjects($PL);//VIP project to have scientific result   
                $VIPfinal = $this->cloneProjects($PL);
                $SLf = $this->cloneStudents($SL);
                $SLs = $this->cloneStudents($SL);
                $SLcombo = $this->cloneStudents($SL);
            }
            else{//load from session
                $VIPf = $_SESSION['globalMC']['VIPf'];
                $VIPs = $_SESSION['globalMC']['VIPs'];
                $VIPfinal = $_SESSION['globalMC']['VIPfinal'];
                $SLf = $this->cloneStudents($_SESSION['globalMC']['SLcombo']);
                $SLs = $this->cloneStudents($_SESSION['globalMC']['SLcombo']);
                $SLcombo = $_SESSION['globalMC']['SLcombo'];
            }
            if($indexM == count($VIPf)){
                //generate metadata here
                $_SESSION['globalMC']['VIPfinalMD'] = $this->generateMatchMetaData($_SESSION['globalMC']['VIPfinal']);
                $data['VIPfinalMD'] = $_SESSION['globalMC']['VIPfinalMD'];
                $data['VIPfinal'] = $_SESSION['globalMC']['VIPfinal'];
                $this->load->view('match_phase_1_finalize',$data);
            }
            else{
                
            $pf = $VIPf[$indexM];
            $ps = $VIPs[$indexM];
            
            $pf = $this->doHeuristicMatch(true, $SLf,$pf);
            $ps = $this->doHeuristicMatch(false, $SLs,$ps);
            $this->generateProjectMetadata($pf);
            $this->generateProjectMetadata($ps);
            $VIPf[$indexM]=$pf;
            $VIPs[$indexM]=$ps;
            $indexM++;
            //should load view to new match page 
            $_SESSION['globalMC']['VIPfinal'] = $VIPfinal;
            $_SESSION['globalMC']['VIPf'] = $VIPf;
            $_SESSION['globalMC']['VIPs'] = $VIPs;
            $_SESSION['globalMC']['SLcombo'] = $SLcombo;
            $_SESSION['globalMC']['indexM'] = $indexM;
            
            $data['VIPfinal'] = $VIPfinal;
            $data['VIPf'] = $VIPf;
            $data['VIPs'] = $VIPs;
            $data['SLcombo'] = $SLcombo;
            $data['indexM'] = $indexM;
            
            $this->load->view('match_phase_1_manual_progress',$data);
            }
    }

    //VIP matching V4
    public function doMatchPhase1Auto($PL, $SL){
            $VIPf = $this->cloneProjects($PL);//VIP project to have friendly result
            $VIPs = $this->cloneProjects($PL);//VIP project to have scientific result
            //unset($PL); //destroy
            
            $SLf = $this->cloneStudents($SL);
            $SLs = $this->cloneStudents($SL);
            
            foreach ($VIPf as $p) {
                $p = $this->doHeuristicMatch(true, $SLf,$p);//match p based on current student list
                $SLf = $this->reduceStudents($SLf, $p);//reduce students in student list based on p's team
            }
            
            foreach ($VIPs as $p) {
                $p = $this->doHeuristicMatch(false, $SLs,$p);//match p based on current student list
                $SLs = $this->reduceStudents($SLs, $p);//reduce students in student list based on p's team
            }
            
            $VIPfMD = $this->generateMatchMetaData($VIPf);//vip metadata
            $VIPsMD = $this->generateMatchMetaData($VIPs);
            
            //should load view to new match page 
            $_SESSION['globalMC']['VIPf'] = $VIPf;
            $_SESSION['globalMC']['VIPs'] = $VIPs;
            $_SESSION['globalMC']['VIPfMD'] = $VIPfMD;
            $_SESSION['globalMC']['VIPsMD'] = $VIPsMD;
            
            $_SESSION['globalMC']['SL'] = $SL;
            $_SESSION['globalMC']['SLrf'] = $SLf;
            $_SESSION['globalMC']['SLrc'] = $SLs;
            $this->load->view('match_phase_1_auto');
        
    }
    //help move things to match phase 1 confirmation/finalization
    public function matchPhase1HelperAuto() {
            
        if($_POST['VIPchoice'] == 'friendly'){//depending on uer choice which is the true vip
            if(!isset($_SESSION['globalMC']['VIPman'])){
                $_SESSION['globalMC']['VIPfinal'] = $_SESSION['globalMC']['VIPf'];
                $_SESSION['globalMC']['VIPfinalMD'] = $_SESSION['globalMC']['VIPfMD'];
            }
            else{
                $_SESSION['globalMC']['VIPfinal'] = array_merge($_SESSION['globalMC']['VIPman'],$_SESSION['globalMC']['VIPf']);
                $_SESSION['globalMC']['VIPfinalMD'] = $this->generateMatchMetaData($_SESSION['globalMC']['VIPfinal']);
            }
            $_SESSION['globalMC']['SLcombo'] = $_SESSION['globalMC']['SLrf'];
        }
        else{
            if(!isset($_SESSION['globalMC']['VIPman'])){
                $_SESSION['globalMC']['VIPfinal'] = $_SESSION['globalMC']['VIPs'];
                $_SESSION['globalMC']['VIPfinalMD'] = $_SESSION['globalMC']['VIPsMD'];
            }
            else{
                $_SESSION['globalMC']['VIPfinal'] = array_merge($_SESSION['globalMC']['VIPman'],$_SESSION['globalMC']['VIPs']);
                $_SESSION['globalMC']['VIPfinalMD'] = $this->generateMatchMetaData($_SESSION['globalMC']['VIPfinal']);
            }
            $_SESSION['globalMC']['SLcombo'] = $_SESSION['globalMC']['SLrc'];            
        }
        
        $data['VIPfinalMD'] = $_SESSION['globalMC']['VIPfinalMD'];
        $data['VIPfinal'] = $_SESSION['globalMC']['VIPfinal'];
        $this->load->view('match_phase_1_finalize',$data);
    }
    //removes students in p from SL
    public function reduceStudents($SL, $p) {
        foreach ($p->desiredStudents as $ds) {
            foreach ($SL as $k => $s) {
                if($s->id == $ds->id){
                    unset($SL[$k]);//could be improved if I make sure $SL listed by ID
                }
            }
        }
        return $SL;
    }
    //Match SL students to project p intesively using only students who wants p (if friendly = true)
    public function doHeuristicMatch($friendly,$SL, $p) {
        $trueSL = array();
        if($friendly){//match on only students that want p
            foreach ($SL as $s) {
                if(array_key_exists($p->id, $s->scoreList) && $s->scoreList[$p->id] > 0){
                    array_push($trueSL, clone $s);//if student wants P put in true list
                }
            }
        }
        else{
            $trueSL = $this->cloneStudents($SL);//match on all students
        }
        
        foreach ($trueSL as $s) {//get each students contribution number for comparator
            $s->amountContributing = count(array_intersect($s->skills, $p->skills));
        }
        
        $team = $this->backTracking($trueSL, $p->skills,$p->max, true);//construct team
        //add team to p
        foreach ($team as $t) {
            $p->addStudent($t);
        }/*
        var_dump($p->name);
        var_dump($p->max);
        var_dump($team);
        var_dump($p->desiredStudents);*/
        return $p;
    }
    //backtrack algorithmn with Depth first search
    //student list, remaining skills to fulfill, $current position(i.e. x positions left to fill)
    public function backTracking($SL,$remain,$pos, $reset) {
        static $max = 0;
        static $checked = array();//hash to check for already considered relevancy set
        static $bestTeam = array();//best team (to compare against
        static $currTeam = array();//current team
        static $functionBreak = 0; //if set to 1 BREAK EVERYTHING we found a winner
        static $oSkills = array();//original skills project p needs

        if($reset){
            $max = 0;
            $checked = array();//hash to check for already considered relevancy set
            $bestTeam = array();//best team (to compare against
            $currTeam = array();//current team
            $functionBreak = 0; //if set to 1 BREAK EVERYTHING we found a winner
            $oSkills = array();//original skills project p needs
        }

        if($max==0){
            $max = $pos; //global (within scope) max
            $bestTeam = array();//precaution
            $oSkills = $remain;
        }
        
        $qualifier = ceil(count($remain)/$pos);//if student s does not have this amount of relevant skills they and any s afterwards don't qualify for consideration
        
        foreach ($SL as $s) {
            $s->relevant = array_intersect($s->skills, $remain);            
        }
        usort($SL, 'compare_students');//sort students based on most likely to be best
        $nextSL = $this->cloneStudents($SL);
        $checkIfAddition = 0;//check if added in a round
        
        foreach($SL as $k => $s){
            if(count($bestTeam) != $max){//fill first best team to start with note for first team dupes don't matter
                
                array_push($bestTeam, $s);
                array_push($currTeam, $s);
                
                $checkIfAddition=1;
                //set id
                $checked[$this->hashSkills($s->relevant)] = 1;
                //recurse to one less postion and count $s less skills
                if($pos != 1){
                    unset($nextSL[$k]); //remove for next position consideration 
                    $this->backTracking($nextSL, array_diff($remain, $s->relevant), $pos - 1, false);
                }
                elseif($this->sucessfulGroup($s, $remain)&& count($bestTeam) == $max){//in case first match is right match CHECK HERE
                        $functionBreak = 1;
                }
                else{
                    continue;//if pos == 1 then see if next possible teams better
                }
            }
            elseif($this->pruneUnqualified($qualifier,$s)){
                break;//stop searchign this studentlist, nothing good will come
            }
            elseif(count($currTeam) != $max && !$this->pruneDupe($s, $checked)){//if curr team not full fill with s assuming s not duplicate
                if($this->inList($s,$currTeam)){
                    continue;//remove? I'm confused why do I need this? pretty sure dupes impossible? guess not
                }
                array_push($currTeam, $s);
                $checkIfAddition=1;
                $checked[$this->hashSkills($s->relevant)] = 1;
                
                if(count($currTeam) == $max){ //if now max
                    if($this->sucessfulGroup($s, $remain)){//in case match is right match auto best
                            $functionBreak = 1;$bestTeam = $currTeam;
                    }
                    elseif($this->betterMatch($currTeam,$bestTeam,$oSkills)){
                        $bestTeam = $currTeam;//reconsider the best
                        break;//last person added exit this consideration
                    }
                    else{
                        break;//last person exit this consideration
                    }
                }
                if($functionBreak != 1){//just in case
                    unset($nextSL[$k]); //remove for next position consideration 
                    $this->backTracking($nextSL, array_diff($remain, $s->relevant), $pos - 1, false);    
                }
                
            }
            
            if($functionBreak == 1){//break all method stacks
            /*
                $max = 0;//reset the statics? is this necessary?
                $checked = array();
                $currTeam = array();
                $functionBreak = 0; */
                break;
            }
        }
        
        array_pop($currTeam);//if going back up one pop last added
        unset($SL);unset($nextSL);//free memory
        return $bestTeam;
    }
    //see if current team c better than best team b
    public function betterMatch($c, $b, $oSkills) {
        $cFulfill = 0;//total team fulfillment
        $bFulfill = 0;
        $cTotalR = 0;//total skills going to fulfillment
        $bTotalR = 0;
        $cAmount = 0;//total skills even useless ones
        $bAmount = 0;
        $cTemp = array();//hold array to check fulfillment
        $bTemp = array();
        
        foreach ($c as $s) {
            $cTemp = array_unique(array_merge($s->skills, $cTemp));
            $cTotalR += count(array_intersect($s->skills, $oSkills));
            $cAmount += count($s->skills);
        }
        foreach ($b as $s) {
            $bTemp = array_unique(array_merge($s->skills, $bTemp));
            $bTotalR += count(array_intersect($s->skills, $oSkills));
            $bAmount += count($s->skills);
        }
        $cFulfill = count(array_intersect($oSkills,$cTemp));
        $bFulfill = count(array_intersect($oSkills,$bTemp));
        
        if($cFulfill > $bFulfill){
            return true;//if current team more fulfillment
        }
        elseif ($cTotalR > $bTotalR && $cFulfill == $bFulfill) {
            return true;//if prior check equivement and new team has more skill for project
        }
        elseif($cAmount < $bAmount && $cTotalR == $bTotalR && $cFulfill == $bFulfill){
            return true;//if prior 2 checks equivalent and new team has less wasteful skills
        }
        return false;
        
    }
    //if true means the team has been determined to be the best
    public function sucessfulGroup($s,$remain) {
        if(count(array_diff($s->skills, $remain))==0){
            return true;
        }
        else{
            return false;
        }
    }
    //pruning heuristic which does not continue search in a logically duplicate relevancy set
    public function pruneDupe($s, $checked) {
        $id = $this->hashSkills($s->relevant);
        
        if(array_key_exists($id, $checked)){
            return true;//yes was consider
        }
        else{
            return false;//no was not considered
        }
        
    }
    //pruning heuristic which does not continue search in an unqualified remaining list of students
    //i.e. this student and any after can not possibly improve project
    public function pruneUnqualified($qualifier,$s){
        if(count($s->relevant) < $qualifier){
            return true;//student cannot possibly improve project
        }
        else{
            return false;//possible improver
        }
    }


    //makes skill list a hashable id works because skills are sorted alphabetically
    public function hashSkills($skills) {
        $hashID = " ";
        foreach ($skills as $s) {
            $hashID = $hashID.$s;
        }
        return $hashID;
    }
    
    //student-centric matching
    public function doMatchPhase2() {
        $PLf = $this->cloneProjects($_SESSION['globalMC']['PL2']);//student free for all remainder projects
        $PLc = $this->cloneProjects($_SESSION['globalMC']['PL2']);//compromise remainder projects
        $SL = $_SESSION['globalMC']['SLcombo'];
        
        $PLf = $this->doNRMP($PLf,$this->cloneStudents($SL), false);//do free for all
        $PLc = $this->doNRMP($PLc,$this->cloneStudents($SL), true);//do compromise
        $PLcMD = $this->generateMatchMetaData($PLc);
        $PLfMD = $this->generateMatchMetaData($PLf);
        
        $_SESSION['globalMC']['PLf'] = $PLf;
        $_SESSION['globalMC']['PLc'] = $PLc;
        $_SESSION['globalMC']['PLfMD'] = $PLfMD;
        $_SESSION['globalMC']['PLcMD'] = $PLcMD;
        
       //var_dump($SL);
       //var_dump($data['PL2']);
        
        $this->load->view('match_phase_2');
    }
    //generates metadata for a project list (aka match)
    public function generateMatchMetaData($PL){
        $PLMD = new ProjectListMetaData(0, 0, 0, 0);
        if(empty($PL)){
            return $PLMD;
        }
        foreach ($PL as $p) {//do this for easier time viewing       
            $p->generateSkillMetaData();
            $PLMD->avgInterest += $p->calculateAvgInterest();
            $PLMD->avgAvgFulfillment+= $p->calculateAvgFulfillment();
            $PLMD->avgTotalSkill+= $p->calculateTotalFulfillment();
            $PLMD->totalOverflow+= $p->calculateTotalOverflow();
        }
        if(count($PL) > 0){
            $PLMD->avgInterest = round($PLMD->avgInterest/count($PL));
            $PLMD->avgAvgFulfillment = round($PLMD->avgAvgFulfillment/count($PL));
            $PLMD->avgTotalSkill = round($PLMD->avgTotalSkill/count($PL));
        }
        return $PLMD;
    }
    //minor function for one by one metadata generation
    public function generateProjectMetadata($p) {
        $p->generateSkillMetaData();
        $p->calculateAvgInterest();
        $p->calculateAvgFulfillment();
        $p->calculateTotalFulfillment();
        $p->calculateTotalOverflow();
    }

    //match via national residency matching program (NRMP alg)
    //worst case is O(students * projects) more or less
    //if compromise true criteria is based on student interest and skill contribution
    //else only on student interest
    public function doNRMP($PL,$SL, $compromise) {
        
        foreach ($SL as $value) {
            $matching[$value->id] = $value;
        }
        
        $unmatched = array();
        
        while(!empty($matching)){//while more to match
            foreach($matching as $s){ //for each matching

                for($i = $s->traversal; $i < count($s->iProjList); $i++){//traverse student project list
                    $ps = $s->iProjList[$i];     
                    $s->traversal = $i+ 1;//save increment before anything else
                    
                    $matching[$s->id]->traversal = $i +1;
                    if(!array_key_exists($ps->id, $PL)){//if project considered not in PL at this point try next
                        continue 2;
                    }
                    
                    $p = $PL[$ps->id];
                    if($p->filled()){//if filled
                        $sw =  $p->getWorst($compromise);
                               
                        if($p -> betterFit($s,$sw,$compromise)){//check if s better than sw given compromise then replace                                                    
                            unset($matching[$s->id]);//unset better fit
                            $matching[$sw->id] = $sw;//reset worst fit
                            $p->removeStudent($sw);
                            $p->addStudent($s);                            
                            
                            continue 2;//continue to next student needing matching
                        }
                    }
                    else{
                        unset($matching[$s->id]);//unset matched student
                        $p->addStudent($s); //add since there's space
                        continue 2;//continue to next student needing matching
                    }
                }
                unset($matching[$s->id]);//if everything traversed s could not be matched
                array_push($unmatched, $s);//push to unmatched
            }
        }
        $word = $compromise."unmatched";
        $_SESSION['globalMC'][$word] = $unmatched;
        return $PL;
    }
    //for a list of students have their relevant skills be equal to
    //the skills they have that is also on skill bank
    public function getRelaventSkillData($SL,$sb){
        foreach($SL as $s){
            $s->relevant = array_intersect($sb, $s->skills);
        }
        return $SL;
    }
    //go from match phase 2 to final show and tell confirmation
    public function matchFinalizeHelper() {
        
        if($_POST['Otherchoice']=='friendly'){//set final result
            $_SESSION['globalMC']['OtherP'] = $_SESSION['globalMC']['PLf'];
            $_SESSION['globalMC']['OtherMD'] = $_SESSION['globalMC']['PLfMD'];
        }
        else{
            $_SESSION['globalMC']['OtherP'] = $_SESSION['globalMC']['PLc'];
            $_SESSION['globalMC']['OtherMD'] = $_SESSION['globalMC']['PLfMD'];
            $_SESSION['globalMC']['unmatched'] = $_SESSION['globalMC']['1unmatched'];
        }
        $data['VIPfinal']=$_SESSION['globalMC']['VIPfinal'];
        $data['VIPfinalMD']=$_SESSION['globalMC']['VIPfinalMD'];
        $data['OtherP']=$_SESSION['globalMC']['OtherP'];
        $data['OtherMD']=$_SESSION['globalMC']['OtherMD'];
        $data['unmatched']=$_SESSION['globalMC']['unmatched'];
        
        
        $this->load->view('match_finalize',$data);
    }
    /*
    public function backTracking($SL,$sb,$pos){
            $check = ceil(floatval(array_count_values($sb))/floatval($pos));
            static $checked = array();
            static $bestTeam = array();
            static $globalPos = null;
            static $currentTeam = array();
            static $fulfilled = 0;
            static $originalReq = array();
            if($globalPos == null){
                $globalPos = $pos;$originalReq = $sb;
            }
            $SL = $this->getRelaventSkillData($SL,$sb);
            usort($SL, 'compare_students');
            
            foreach($SL as $s){
                if(isset($bestTeam) && array_count_values($bestTeam) != $$globalPos){
                    if($this->pruneSkillAmount($check, array_count_values($s->relevant))){
                        break;
                    }
                    elseif($this->pruneSkillCopy($s->relevant,$checked)){
                        continue;
                    }
                }
                $id = " ";
                foreach($s->relevant as $sk){
                    $id .= $sk;
                }
                
                $checked[$id] = 1;//id will now exist in checked skill combo array
                
                if($pos != 1){
                    array_push($currentTeam, $s);
                    unset($SL[$s->id]);
                    $this->backTracking($SL,  array_diff($sb, $s->relevant),$pos-1);
                }
                else{
                    $bestTeam = $this->findBestTeam($bestTeam,$currentTeam,$fulfilled);
                    if($fulfilled == array_count_values($originalReq)){
                        break;
                    }
                }
                
            }
            array_pop($currentTeam);
            return $bestTeam;
            
    }
    
    public function findBestTeam($currentBest,$currentTeam,$or){
        $cbnum = 0;
        $ctnum = 0;
        $a = array();
        $b = array();
        foreach($currentBest as $cb){
            array_unique(array_merge($a,  $cb->relevant));
        }
        foreach($currentTeam as $ct){
            array_unique(array_merge($a,  $ct->relevant));
        }
        $an = array_count_values($a);
        $bn = array_count_values($b);
        if($an< $bn){
            $or = $bn;
            return $currentTeam;
        }
        return $bestTeam;
    }
    
    public function pruneSkillCopy($rel,$checked) {
        $id = " ";
        foreach($rel as $skill){
            $id .= $skill;
        }
        if(array_key_exists($id, $checked)){
            return true;
        }
        return false;
    }
    public function pruneSkillAmount($check,$skillAmount) {
        if($check > $skillAmount){
            return true;
        }
        return false;
    }*/
    /*//v3 outdated
    public function doMatch() {

//        set_time_limit(0);
//        error_reporting(E_ALL);
//        ob_implicit_flush(TRUE);
//        ob_end_flush();
        // LOAD PRE-PREPARED STUDENTS from preprocess i guess?
          $PL = $_SESSION['PL'];
          $SL = $_SESSION['SL'];

//        $p1 = new Project('1', 'Proj 1', array('1' => 'sk1', '2' => 'sk2', '3' => 'sk3'), '1', '3');
//        $p2 = new Project('2', 'Proj 2', array('1' => 'sk1', '2' => 'sk2'), '3', '2');
//        $p3 = new Project('3', 'Proj 3', array('2' => 'sk2', '4' => 'sk4'), '2', '3');
//
//        $PL = array($p1, $p2, $p3);

        $pskillSet = array();//array of array of skills
        foreach ($PL as $p) {
            foreach ($p->skills as $s) {
                array_push($pskillSet, $s);
            }
        }
        
        usort($PL, 'compare_ranks');//sort project list via ranking

        $numProjects = count($PL);//how many projects
        $previousRank = null;//rank of project before p 
        foreach ($PL as $p) {//truly rank all projects relative to one another
            if (intval($p->score) == $previousRank) {//why intval
                $numProjects++;
                $previousRank = intval($p->score);
                $p->score = $numProjects;
            } else {
                $previousRank = intval($p->score);
                $p->score = $numProjects;
            }
            if ($numProjects > 1) {
                $numProjects--;
            }
        }

        //print_r($pskillSet);
        $pskillCount = array_count_values_ignore_case($pskillSet);//array[skill name] = amount of skills in it

//        $rL1 = array('1' => '2', '2' => '1', '3' => '1');
//        $rL2 = array('1' => '3', '2' => '6', '3' => '6');
//        $rL3 = array('1' => '1', '2' => '4', '3' => '7');
//        $rL4 = array('1' => '1', '3' => '1');
//        $rL5 = array('1' => '2', '2' => '1');
//
//        $s1 = new Student('1', 'Chris', array('1' => 'sk1', '2' => 'sk2', '3' => 'sk3'), $rL1);
//        $s2 = new Student('2', 'Alicia', array('1' => 'sk1', '2' => 'sk2', '4' => 'sk4'), $rL2);
//        $s3 = new Student('3', 'Michael', array('1' => 'sk1', '3' => 'sk3'), $rL3);
//        $s4 = new Student('4', 'Steven', array('2' => 'sk2', '4' => 'sk4'), $rL4);
//        $s5 = new Student('5', 'Lorenzo', array('3' => 'sk3', '4' => 'sk4'), $rL5);
//
//        $SL = array($s1, $s2, $s3, $s4, $s5);

        foreach ($SL as $s) {//truly rank student rankings
            $minimum = 2;
            $previousRank = null;
            $x = 0;
            foreach ($s->scoreList as $project => $rank) {
                if (intval($rank) == $previousRank) {
                    if ($minimum > 1) {
                        $minimum++;
                    }
                    $s->scoreList[$project] = $minimum;
                } else {
                    $s->scoreList[$project] = $minimum;
                }
                if ($minimum > 1) {
                    $minimum--;
                }
                $previousRank = intval($rank);
                $x++;
            }
            // echo '<br>';
        }

        $sskillSet = array();
        foreach ($SL as $s) {//make list of all student skills plus amount
            foreach ($s->skills as $sk) {
                array_push($sskillSet, $sk);
            }
        }

        // print_r($sskillSet);
        $sskillCount = array_count_values_ignore_case($sskillSet);//array of skills #

        foreach ($PL as $p) {
            foreach ($SL as $s) {
                if ($p->checkStudent($s)) {//if students has at least one skill for p
                    $p->addDesiredStudent($s);//add student
                }
            }
        }

        $LTL = array();//list of team list?
        
        //What happens: for each project!, get teamlist!, made all possible arrangements of students for project,
        //for each arranement, made a team out of that, addedum it to TL
        //then push TL for LTL for projects
        //End result LTL is  = array where ith element is array for a project where jth element is possibteam for that project 
        foreach ($PL as $p) {
            $TL = array();//team list
            $arrangement = subsets_up_to_n($p->desiredStudents, $p->max);// get all arrangements?
            foreach ($arrangement as $a) {
                $team = new Team($p->id, $p->name, $p->score, $p->skills);
                $team->addStudents($a);//add arrangement for s
                array_push($TL, $team);
            }
            array_push($LTL, $TL);
            unset($TL);
        }

        $comb = combinations($LTL, count($SL));//?
        $data['doMatch'] = true;
        $data['comb'] = $comb;
        $this->load->view('match_results_page', $data);
    }*/
    //clone old project list to new project list
    public function cloneProjects($opl) {
        $npl = array(); //new project list
        
        foreach ($opl as $key => $value) {
            $npl[$key] = clone $value;
        }
        return $npl;
    }
        //clone old student list to new student list
    public function cloneStudents($osl) {
        $nsl = array(); //new project list
        
        foreach ($osl as $key => $value) {
            $nsl[$key] = clone $value;
        }
        return $nsl;
    }
    //find if student in student array
    public function inList($s, $SL) {
        foreach ($SL as $v) {
            if($v->id == $s->id){
                return true;
            }
        }        
        return false;
    }
}
