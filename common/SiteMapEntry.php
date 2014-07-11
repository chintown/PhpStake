<?php

class SiteMapEntry {
    const ALWAYS = 'always';
    const HOURLY = 'hourly';
    const DAILY = 'daily';
    const WEEKLY = 'weekly';
    const MONTHLY = 'monthly';
    const YEARLY = 'yearly';
    const NEVER = 'never';

    const TOP = 1.00;
    const NORMAL = 0.5;
    const BOTTOM = 0.1;
    const PRIORITY_SCALE = 0.1;

    public $location;
    public $changeFrequency;
    public $priorityRatio;

    function __construct($location) {
        $this->location = $location;
        $this->changeFrequency = self::DAILY;
        $this->priorityRatio = self::NORMAL ;
    }

    /**
     * @param string $changeFrequency
     */
    public function setChangeFrequency($changeFrequency)
    {
        $this->changeFrequency = $changeFrequency;
    }

    /**
     * @param float $priorityRatio
     */
    public function setPriorityRatio($priorityRatio)
    {
        $this->priorityRatio = $priorityRatio;
    }
}