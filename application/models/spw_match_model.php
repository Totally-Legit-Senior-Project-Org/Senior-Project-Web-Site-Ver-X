<?php

class SPW_Match_Model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getRanks($user) {
        $this->db->select('project, rank');
        $this->db->where('user', $user);
        $query = $this->db->get('spw_rank_user');

        $rankInfo = array();
        foreach ($query->result() as $row) {
            $rankInfo[$row->project] = $row->rank;
        }

        return $rankInfo;
    }

    public function getMinimum() {
        $this->db->select('rank_min');
        $query = $this->db->get('spw_term');
        foreach ($query->result() as $row) {
            $min = $row->rank_min;
        }
        return $min;
    }

    public function setMinimum($minVal) {
        $this->db->set('rank_min', $minVal, FALSE);
        ;
        $this->db->update('spw_term');
    }

    public function insertRanks($rank) {
        $data = array('user' => $rank['user'],
            'rank' => $rank['rank']
        );

        foreach ($data['rank'] as $project => $rank) {
            $rankInfo = array(
                'user' => $data['user'],
                'project' => $project,
                'rank' => $rank
            );

            if ($this->rank_exists($rankInfo['user'], $rankInfo['project'])) {
                if ($rankInfo['rank'] < 0) {
                    $this->removeRank($rankInfo['user'], $rankInfo['project']);
                } else {
                    $this->db->set('rank', $rankInfo['rank'], FALSE);
                    $this->db->where(array('project' => $rankInfo['project'],
                        'user' => $rankInfo['user']));
                    $this->db->update('spw_rank_user');
                }
            } else {
                if ($rankInfo['rank'] > -1) {
                    $this->db->insert('spw_rank_user', $rankInfo);
                }
            }
        }
    }

    public function removeRank($user, $project) {
        $query = $this->db->delete('spw_rank_user', array('user' => $user,
            'project' => $project
        ));
    }

    public function rank_exists($user, $project) {
        $this->db->where(array('user' => $user,
            'project' => $project
        ));
        $query = $this->db->get('spw_rank_user');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getActiveHeadProfessor() {
        $this->db->select('id');
        $this->db->where(array('status' => 'ACTIVE',
            'role' => 'HEAD'
        ));
        $query = $this->db->get('spw_user');
        foreach ($query->result() as $row) {
            $id = $row->id;
        }
        return $id;
    }

    public function getAllActiveStudentIDs() {
        $ids = array();
        $this->db->select('id');
        $this->db->where(array('status' => 'ACTIVE',
            'role' => 'STUDENT'
        ));
        $query = $this->db->get('spw_user');
        foreach ($query->result() as $row) {
            array_push($ids, $row->id);
        }
        return $ids;
    }

    public function getAllApprovedProjectIDs() {
        $ids = array();
        $this->db->select('id');
        $this->db->where(array('status' => 'APPROVED'
        ));
        $query = $this->db->get('spw_project');
        foreach ($query->result() as $row) {
            array_push($ids, $row->id);
        }
        return $ids;
    }

    public function getSkillsForStudent($id) {
        $skills = array();
        $sql = "SELECT spw_skill.name, spw_skill.id "
                . "FROM `spw_skill`,`spw_skill_user` "
                . "WHERE spw_skill_user.user=$id AND (spw_skill.id = spw_skill_user.skill)";
        $query = $this->db->query($sql);
        foreach ($query->result() as $row) {
            $skills[$row->id] = trim(strtolower($row->name));
        }
        return $skills;
    }

    public function getRanksForStudent($id) {
        $ranks = array();
        $sql = "SELECT `project`, `rank` "
                . "FROM `spw_rank_user` "
                . "WHERE spw_rank_user.user=$id";
        $query = $this->db->query($sql);
        foreach ($query->result() as $row) {
            $ranks[$row->project] = $row->rank;
        }
        return $ranks;
    }

    public function getSkillsForProject($id) {
        $skills = array();
        $sql = "SELECT spw_skill.name, spw_skill.id "
                . "FROM `spw_skill`,`spw_skill_project` "
                . "WHERE spw_skill_project.project=$id AND (spw_skill.id = spw_skill_project.skill)";
        $query = $this->db->query($sql);
        foreach ($query->result() as $row) {
            if ($row->name != '') {
                $skills[$row->id] = trim(strtolower($row->name));
            }
        }
        return $skills;
    }

    public function getRanksForProjects() {
        $id = $this->getActiveHeadProfessor();
        $ranks = $this->getRanksForStudent($id);
        return $ranks;
    }

    public function getAllProjectsMaxStudents() {
        $ids = array();
        $this->db->select('id, max_students');
        $this->db->where(array('status' => 'APPROVED'
        ));
        $query = $this->db->get('spw_project');
        foreach ($query->result() as $row) {
            $ids[$row->id] = $row->max_students;
        }
        return $ids;
    }

    public function getStudentNames() {
        $names = array();
        $sql = "SELECT `id`,`first_name`,`last_name`  "
                . "FROM `spw_user` "
                . "WHERE (`role`='STUDENT') and (`status`='ACTIVE')";
        $query = $this->db->query($sql);
        foreach ($query->result() as $row) {
            $fn = trim(strtolower($row->first_name));
            $ln = trim(strtolower($row->last_name));
            $fn[0] = strtoupper($fn[0]);
            $ln[0] = strtoupper($ln[0]);
            $names[$row->id] = $fn . " " . $ln;
        }
        return $names;
    }

    public function getProjectNames() {
        $names = array();
        $sql = "SELECT `id`,`title`  "
                . "FROM `spw_project` "
                . "WHERE (`status`='APPROVED')";
        $query = $this->db->query($sql);
        foreach ($query->result() as $row) {
            $title = trim($row->title);
            $names[$row->id] = $title;
        }
        return $names;
    }
    
    public function addStudentToProject( $student_id,$project_id) {
        $data = array(
            'project' => $project_id
        );

        $this->db->where('id', $student_id);
        $this->db->update('spw_user', $data);
    }

}
