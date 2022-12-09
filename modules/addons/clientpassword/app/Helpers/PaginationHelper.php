<?php

namespace LMTech\ClientPassword\Helpers;

use WHMCS\Database\Capsule;
use LMTech\ClientPassword\Config\Config;

/**
 * WHMCS Client Password Changer
 *
 * Allows admins to change a users password manually without the need to send an email
 * to the client and reset it that way.
 *
 * @package    WHMCS
 * @author     Lee Mahoney <lee@leemahoney.dev>
 * @copyright  Copyright (c) Lee Mahoney 2022
 * @license    MIT License
 * @version    1.0.5
 * @link       https://leemahoney.dev
 */

if (!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}

class PaginationHelper {

    private $pageName;
    private $limit;
    private $model;

    private $where;
    private $whereInArray;
    private $whereOrArray;

    private $recordCount;
    private $pages;

    private $page;

    private $offset;

    private $sort;

    public function __construct($pageName = 'p', $where = [], $limit = 10, $model, $whereInArray = [], $whereOrArray = [], $sort = ['DESC', 'created_at']) {

        

        $this->pageName     = $pageName;
        $this->where        = $where;
        $this->limit        = $limit;
        $this->model        = $model;

        $this->whereInArray = $whereInArray;
        $this->whereOrArray = $whereOrArray;

        $this->recordCount  = $this->getRecordCount();
        $this->pages        = $this->recordCount / $this->limit;

        $this->page         = (int) (AdminPageHelper::getAttribute($this->pageName) != null) ? AdminPageHelper::getAttribute($this->pageName) : 1;

        $this->offset       = ($this->page - 1) * $this->limit;
        $this->sort         = $sort;

    }

    public function data() {

        $result = $this->model::offset($this->offset)->limit($this->limit);

        if (!empty($this->whereOrArray)) {

            $result->where($this->whereOrArray[0][0], $this->whereOrArray[0][1], $this->whereOrArray[0][2]);

            foreach ($this->whereOrArray as $array) {
                $result->orWhere($array[0], $array[1], $array[2]);
            }

        }

        if (!empty($this->where)) {
            $result->where($this->where);
        }

        if (!empty($this->whereInArray)) {
            $result->whereIn($this->whereInArray[0], $this->whereInArray[1]);
        }
       
        if ($this->sort[0] == 'ASC') {
            return $result->get()->sortBy($this->sort[1]);
        } else if ($this->sort[0] == 'DESC') {
            return $result->get()->sortByDesc($this->sort[1]);
        } else {
            return $result->get();
        }

    }

    public function links() {

        $html = '';

        if ($this->recordCount < ($this->limit + 1)) {
            $html .= '<div class="clearfix">';
            $html .= '<div class="text-sm-left float-left pull-left">Showing <b>' . $this->recordCount . '</b> out of <b>' . $this->recordCount . '</b> records</div>';
            $html .= '</div>'; 
        }

        if ($this->recordCount > $this->limit) {
   
            $html .= '
                <div class="clearfix">
                    <div class="text-sm-left float-left pull-left">Showing <b>' . $this->offset . '</b> to <b>' . ($this->page * $this->limit) . '</b> out of <b>' . $this->recordCount . '</b> records</div>
                    <ul style="margin: 0px 0px" class="pagination float-right pull-right">
            ';

            if($this->page == 1) {
                $html .= '<li class="page-item disabled"><a href="#" class="page-link">&laquo;</a></li>';
            } else {
                $html .= '<li class="page-item"><a href="' . $this->parseURL($this->pageName, 1) . '" class="page-link">&laquo;</a></li>';
            }
            
            if ($this->page < 2) {
                $html .= '<li class="page-item disabled"><a href="#" class="page-link">Previous</a></li>';
            } else {
                $html .= '<li class="page-item"><a href="' . $this->parseURL($this->pageName, ($this->page - 1)) . '" class="page-link">Previous</a></li>';
            }

            if ($this->page - 2 > 0) {
                $html .= '<li class="page-item"><a href="' . $this->parseURL($this->pageName, ($this->page - 2)) . '" class="page-link">' . ($this->page - 2) . '</a></li>';
            }

            if ($this->page - 1 > 0) {
                $html .= '<li class="page-item"><a href="' . $this->parseURL($this->pageName, ($this->page - 1)) . '" class="page-link">' . ($this->page - 1) . '</a></li>';
            }

            $html .= '<li class="page-item active"><a href="' . $this->parseURL($this->pageName, $this->page) . '" class="page-link">' . $this->page . '</a></li>';

            if (($this->page + 1) < ($this->pages + 1)) {
                $html .= '<li class="page-item"><a href="' . $this->parseURL($this->pageName, ($this->page + 1)) . '" class="page-link">' . ($this->page + 1) . '</a></li>';
            }

            if (($this->page + 2) < ($this->pages + 1)) {
                $html .= '<li class="page-item"><a href="' . $this->parseURL($this->pageName, ($this->page + 2)) . '" class="page-link">' . ($this->page + 2) . '</a></li>';
            }

            if (($this->page + 1) < ($this->pages + 1)) {
                $html .= '<li class="page-item"><a href="' . $this->parseURL($this->pageName, ($this->page + 1)) . '" class="page-link">Next</a></li>';
            } else {
                $html .= '<li class="page-item disabled"><a href="#" class="page-link">Next</a></li>';
            }

            if($this->page == round($this->pages)) {
                $html .= '<li class="page-item disabled"><a href="#" class="page-link">&raquo;</a></li>';
            } else {
                $html .= '<li class="page-item"><a href="' . $this->parseURL($this->pageName, round($this->pages)) . '" class="page-link">&raquo;</a></li>';
            }

            $html .= '
                    </ul>
                </div>    
            ';

        }

        return $html;


    }

    public function recordCount() {
        return $this->recordCount;
    }

    private function parseURL($parameter, $value) { 
        
        $params     = []; 
        $output     = '?';
        $firstRun   = true; 
        
        foreach($_GET as $key => $val) { 
            
            if($key != $parameter) { 
                
                if(!$firstRun) { 
                    $output .= '&'; 
                } else { 
                    $firstRun = false; 
                }

                $output .= $key . '=' . urlencode($val);

             }

        } 
    
        if(!$firstRun) {
            $output .= '&'; 
        }
            
        $output .= $parameter . '=' . urlencode($value); 

        return htmlentities($output); 
    
    }

    private function getRecordCount() {

        $result = $this->model::offset($this->offset)->limit($this->limit);

        if (!empty($this->whereOrArray)) {

            $result->where($this->whereOrArray[0][0], $this->whereOrArray[0][1], $this->whereOrArray[0][2]);

            foreach ($this->whereOrArray as $array) {
                $result->orWhere($array[0], $array[1], $array[2]);
            }

        }

        if (!empty($this->where)) {
            $result->where($this->where);
        }

        if (!empty($this->whereInArray)) {
            $result->whereIn($this->whereInArray[0], $this->whereInArray[1]);
        }
       
        if ($this->sort[0] == 'ASC') {
            return $result->count()->sortBy($this->sort[1]);
        } else if ($this->sort[0] == 'DESC') {
            return $result->count()->sortByDesc($this->sort[1]);
        } else {
            return $result->count();
        }
        
    }

}