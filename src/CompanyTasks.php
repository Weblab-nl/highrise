<?php

/**
 * Wrapper around a collection of highrise tasks for a company
 *
 * @author ThePerfectWedding.nl - Thomas Marinissen
 */
class Tpw_Controller_Helper_Highrise_CompanyTasks {

    /**
     * The Highrise Company instance
     * 
     * @var \Highrise
     */
    private $company;

    /**
     * The company tasks as \SimpleXMLElement object
     * 
     * @var SimpleXMLElement[]
     */
    private $tasks;

    /**
     * Constructor
     * 
     * @param \Company             The Highrise company
     */
    public function __construct(\Tpw_Controller_Helper_Highrise_Company $company) {
        // set the class variable
        $this->company = $company;

        // consutrct the base url
        $baseUrl = 'companies/' . $company->id() . '/tasks.xml';

        // set the offset
        $offset = 0;
        
        // add all the tasks for the company
        while (true) {
            // load the tasks for the current iteration
            $tasks = $this->company->highrise()->call($baseUrl . '?n=' . $offset);
            
            // if there are tasks, add them
            if (!is_null($tasks)) {
                $this->addTasks($tasks);
            }

            // if all the tasks where collected, break the while loop
            if (is_null($tasks) || $tasks->count() < 25) {
                break;
            }

            // continue to the next page
            $offset += 25;
        }
    }

    /**
     * Add a task
     * 
     * @param  SimpleXMLElement             The SimpleXMLElement containing the task information
     * @return \CompanyTasks                The Instance of this, to make chaining possible
     */
    public function addTask(SimpleXMLElement $task) {
        // add the task
        $this->tasks[] = new \Tpw_Controller_Helper_Highrise_Task($this->company->highrise(), $task);

        // done, return the instance of this, to make chaining possible
        return $this;
    }

    /**
     * Add tasks
     * 
     * @param  SimpleXMLElement             The SimpleXMLElement containing the tasks
     * @return \CompanyTasks                The Instance of this, to make chaining possible
     */
    public function addTasks(SimpleXMLElement $tasks) {
        // iterate over all the tasks and add a new task for every xml task
        foreach ($tasks->children() as $task) {
            $this->addTask($task);
        }

        // done, return the instance of this, to make chaining possible
        return $this;
    }
    
    /**
     * Get the tasks
     * 
     * @return SimpleXMLElement[]           The highrise tasks
     */
    public function tasks() {
        return $this->tasks;
    }

}
