<?php
class PaginationObject {
    public $numItemPerPage = 10;
    public $numPagerPerPage = 10;
    public $numItem;
    public $numPager;
    public $currentPage; // starts from 1
    public $leftMostPage;
    public $rightMostPage;


    public function setNumItemPerPage($numItemsPerPage) {
        $this->numItemPerPage = $numItemsPerPage;
    }
    public function setNumPagerPerPage($numPagersPerPage) {
        $this->numPagerPerPage = $numPagersPerPage;
    }
    public function setNumItem($numItems) {
        $this->numItem = $numItems;
        $this->calculateNumPagers();
    }
    public function setCurrentPage($currentPage) {
        $this->currentPage = $currentPage;
        $this->calculateLeftMostPage();
        $this->calculateRightMostPage();
    }


    public function getLeftMostPage() {
        return $this->leftMostPage;
    }
    public function getCurrentPage() {
        return $this->currentPage;
    }
    public function getRightMostPage() {
        return $this->rightMostPage;
    }
    public function getNumPager() {
        return $this->numPager;
    }
    public function getNumPagerPerPage() {
        return $this->numPagerPerPage;
    }

    public function export() {
        return $this->exportAs();
    }
    public function exportAs($nameOfLeftMostPage='low', $nameOfCurrentPage='current', $nameOfRightMostPage='high', $nameOfNumPager='max', $nameOfNumPagerPerPage='num_per_page') {
        return array(
            $nameOfLeftMostPage=> $this->leftMostPage,
            $nameOfCurrentPage=> $this->currentPage,
            $nameOfRightMostPage=> $this->rightMostPage,
            $nameOfNumPager=> $this->numPager,
            $nameOfNumPagerPerPage=> $this->numPagerPerPage
        );
    }


    private function calculateNumPagers() {
        if (!isset($this->numItem)) {
            throw new Exception("please set numItem before calculateNumPagers");
        } else if (!isset($this->numItemPerPage)) {
            throw new Exception("please set numItemsPerPage before calculateNumPagers");
        }
        $this->numPager = intval(($this->numItem - 1) / $this->numItemPerPage) + 1; // start from 1
    }
    private function calculateLeftMostPage() {
        if (!isset($this->numPagerPerPage)) {
            throw new Exception("please set numPagerPerPage before calculateLeftMostPage");
        } else if (!isset($this->currentPage)) {
            throw new Exception("please set currentPage before calculateLeftMostPage");
        }
        $this->leftMostPage = ($this->numPagerPerPage * (intval(($this->currentPage - 1) / $this->numPagerPerPage))) + 1;
    }
    private function calculateRightMostPage() {
        if (!isset($this->leftMostPage)) {
            throw new Exception("please set leftMostPage before calculateRightMostPage");
        } else if (!isset($this->numPagerPerPage)) {
            throw new Exception("please set numPagerPerPage before calculateRightMostPage");
        } else if (!isset($this->numPager)) {
            throw new Exception("please set numPager before calculateRightMostPage");
        }
        $this->rightMostPage = (($this->leftMostPage + $this->numPagerPerPage - 1) > $this->numPager)
                                    ? $this->numPager
                                    : ($this->leftMostPage + $this->numPagerPerPage - 1);
    }
}