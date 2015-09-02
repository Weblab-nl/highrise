<?php

/**
 * Wrapper around a collection of highrise emails for a company
 *
 * @author ThePerfectWedding.nl - Thomas Marinissen
 */
class Tpw_Controller_Helper_Highrise_CompanyEmails {

    /**
     * The Highrise Company instance
     * 
     * @var \Highrise
     */
    private $company;

    /**
     * The company emails as \SimpleXMLElement object
     * 
     * @var SimpleXMLElement
     */
    private $emails;

    /**
     * Constructor
     * 
     * @param \Company             The Highrise company
     */
    public function __construct(\Tpw_Controller_Helper_Highrise_Company $company) {
        // set the class variable
        $this->company = $company;

        // consutrct the base url
        $baseUrl = 'companies/' . $company->id() . '/emails.xml';

        // set the offset
        $offset = 0;
        
        // add all the emails for the company
        while (true) {
            // load the emails for the current iteration
            $emails = $this->company->highrise()->call($baseUrl . '?n=' . $offset);
            
            // if there are emails, add them
            if (!is_null($emails)) {
                $this->addEmails($emails);
            }

            // if all the emails where collected, break the while loop
            if (is_null($emails) || $emails->count() < 25) {
                break;
            }

            // continue to the next page
            $offset += 25;
        }
    }

    /**
     * Add a email
     * 
     * @param  SimpleXMLElement             The SimpleXMLElement containing the email information
     * @return \CompanyEmails               The Instance of this, to make chaining possible
     */
    public function addEmail(SimpleXMLElement $email) {
        // add the email
        $this->emails[] = new \Tpw_Controller_Helper_Highrise_Email($this->company->highrise(), $email);

        // done, return the instance of this, to make chaining possible
        return $this;
    }

    /**
     * Add emails
     * 
     * @param  SimpleXMLElement             The SimpleXMLElement containing the emails
     * @return \CompanyEmails               The Instance of this, to make chaining possible
     */
    public function addEmails(SimpleXMLElement $emails) {
        // iterate over all the emails and add a new email for every xml email
        foreach ($emails->children() as $email) {
            $this->addEmail($email);
        }

        // done, return the instance of this, to make chaining possible
        return $this;
    }
    
    /**
     * Get the emails
     * 
     * @return SimpleXMLElement[]           The highrise notes
     */
    public function emails() {
        return $this->emails;
    }
}
