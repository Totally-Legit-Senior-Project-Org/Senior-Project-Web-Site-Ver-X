<?php

class SPW_Project_Summary_View_Model extends CI_Model {

    //a SPW_Project_Model object
    public $project;
    public $statusName;
    //a SPW_Term_Model object
    //public $term;
    //an array of SPW_Skill_Model objects
    public $lSkills;
    //an array of SPW_User_Summary_View_Model
    // public $lMentorSummaries;
    //a SPW_User_Summary object
    public $proposedBySummary;
    //an array of SPW_User_Summary_View_Model
    public $lTeamMemberSummaries;
    public $justList;
    public $displayJoin;
    public $displayLeave;

    public function __construct() {
        parent::__construct();
    }

    public function getShortDescription() {
        return substr($this->project->description, 0, min(200, strlen($this->project->description)));
    }

    public function getDescription() {
        return $this->project->description;
    }

    public function getlSkillNames() {
        $resultArray = array();

        foreach ($this->lSkills as $iSkill) {
            $resultArray[] = $iSkill->name;
        }

        $resultStr = join(', ', $resultArray);
        return $resultStr;
    }

    /* this function fills a list of projects with their data */

    public function prepareProjectsDataToShow($user_id, $lProjectIds, $belongProjectIdsList, $pastProjects) { {
            $tempUser = new SPW_User_Model();
            $tempProject = new SPW_Project_Model();
            $tempTerm = SPW_Term_Model::getInstance();
            //$tempStatus = new SPW_Project_Status_Model();

            $length = count($lProjectIds);

            $lProjects = array();

            for ($i = 0; $i < $length; $i++) {
                $project_summ_vm = new SPW_Project_Summary_View_Model();

                $project_id = $lProjectIds[$i];

                $project = $tempProject->getProjectInfo($project_id);

                if (isset($project)) {
                    $project_summ_vm->project = $project;

                    $project_summ_vm->justList = true;

                    //$term = $tempProject->getProjectTermInfo($project_id);

                    $tempStatus = $project->status;

                    $project_summ_vm->statusName = $tempStatus;

                    $lSkills = $tempProject->getProjectListOfSkills($project_id);
                    if (isset($lSkills) && count($lSkills) > 0) {
                        $project_summ_vm->lSkills = $lSkills;
                    }

                    $tempMentor = $project->mentor;
                    $project_summ_vm->lMentor = $tempMentor;

                    $proposedBySumm = $tempProject->getProposedByOfProject($project_id);

                    if (isset($proposedBySumm)) {
                        $project_summ_vm->proposedBySummary = $proposedBySumm;
                    }


                    $lStudentsSumm = $tempProject->getStudentsListForProject($project_id);


                    if (isset($lStudentsSumm) && count($lStudentsSumm) > 0) {
                        $project_summ_vm->lTeamMemberSummaries = $lStudentsSumm;
                    }

//                if (!($project_summ_vm->justList))
//                {
                    if (!$pastProjects) {

                        if (($tempTerm->currentDateUnderDeadline())) {
                            if ($tempUser->isUserAStudent($user_id)) {

                                $currentProject = $tempUser->get_project($user_id);

                                if ($currentProject == $project_id) {
                                    $project_summ_vm->displayLeave = TRUE;
                                    $project_summ_vm->displayJoin = FALSE;
                                } else {
                                    $project_summ_vm->displayLeave = FALSE;
                                    $project_summ_vm->displayJoin = TRUE;
                                }
                            }
                        } else {
                            $project_summ_vm->displayLeave = FALSE;
                            $project_summ_vm->displayJoin = FALSE;
                        }
                    }
                } else {
                    throw new Exception('spw_project table error in db...');
                }

                $lProjects[] = $project_summ_vm;
            }
            return $lProjects;
        }
    }

    public function isProjectInList($belongProjIdsList, $project_Id) {
        $length = count($belongProjIdsList);

        for ($i = 0; $i < $length; $i++) {
            if ($belongProjIdsList[$i] == $project_Id)
                return true;
        }

        return false;
    }

}

?>