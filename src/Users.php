<?php

/**
 * Wrapper around a collection of highrise users for a company
 *
 * @author ThePerfectWedding.nl - Thomas Marinissen
 */
class Tpw_Controller_Helper_Highrise_Users {

    /**
     * The Highrise  instance
     * 
     * @var \Highrise
     */
    private $highrise;

    /**
     * The users as \SimpleXMLElement object
     * 
     * @var SimpleXMLElement[]
     */
    private $users;

    /**
     * Constructor
     * 
     * @param \Highrise             The Highrise api instance
     */
    public function __construct(\Tpw_Controller_Helper_Highrise_Highrise $highrise) {
        // set the class variable
        $this->highrise = $highrise;

        // consutrct the base url
        $baseUrl = 'users.xml';

        // set the offset
        $offset = 0;
        
        // add all the users
        while (true) {
            // load the users for the current iteration
            $users = $this->highrise->call($baseUrl . '?n=' . $offset);

            // if there are users, add them
            if (!is_null($users)) {
                $this->addUsers($users);
            }

            // if all the users where collected, break the while loop
            if (!is_null($users) || $users->count() < 25) {
                break;
            }

            // continue to the next page
            $offset += 25;
        }
    }

    /**
     * Add a user
     * 
     * @param  SimpleXMLElement                                         The SimpleXMLElement containing the user information
     * @return \Tpw_Controller_Helper_Highrise_CompanyUsers             The Instance of this, to make chaining possible
     */
    public function addUser(SimpleXMLElement $user) {
        // add the user
        $this->users[] = new \Tpw_Controller_Helper_Highrise_User($this->highrise, $user);

        // done, return the instance of this, to make chaining possible
        return $this;
    }

    /**
     * Add users
     * 
     * @param  SimpleXMLElement                                         The SimpleXMLElement containing the users
     * @return \Tpw_Controller_Helper_Highrise_CompanyUsers             The Instance of this, to make chaining possible
     */
    public function addUsers(SimpleXMLElement $users) {
        // iterate over all the users and add a new user for every xml user
        foreach ($users->children() as $user) {
            $this->addUser($user);
        }

        // done, return the instance of this, to make chaining possible
        return $this;
    }
    
    /**
     * Get the users
     * 
     * @return SimpleXMLElement[]           The highrise users
     */
    public function users() {
        return $this->users;
    }
}
