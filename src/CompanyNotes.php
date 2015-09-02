<?php

/**
 * Wrapper around a collection of highrise notes for a company
 *
 * @author ThePerfectWedding.nl - Thomas Marinissen
 */
class Tpw_Controller_Helper_Highrise_CompanyNotes {

    /**
     * The Highrise Company instance
     * 
     * @var \Highrise
     */
    private $company;

    /**
     * The company notes as \SimpleXMLElement object
     * 
     * @var SimpleXMLElement[]
     */
    private $notes;

    /**
     * Constructor
     * 
     * @param \Company             The Highrise company
     */
    public function __construct(\Tpw_Controller_Helper_Highrise_Company $company) {
        // set the class variable
        $this->company = $company;

        // consutrct the base url
        $baseUrl = 'companies/' . $company->id() . '/notes.xml';

        // set the offset
        $offset = 0;
        
        // add all the notes for the company
        while (true) {
            // load the notes for the current iteration
            $notes = $this->company->highrise()->call($baseUrl . '?n=' . $offset);
            
            // if there are notes, add them
            if (!is_null($notes)) {
                $this->addNotes($notes);
            }

            // if all the notes where collected, break the while loop
            if (is_null($notes) || $notes->count() < 25) {
                break;
            }

            // continue to the next page
            $offset += 25;
        }
    }

    /**
     * Add a note
     * 
     * @param  SimpleXMLElement             The SimpleXMLElement containing the note information
     * @return \CompanyNotes                The Instance of this, to make chaining possible
     */
    public function addNote(SimpleXMLElement $note) {
        // add the note
        $this->notes[] = new \Tpw_Controller_Helper_Highrise_Note($this->company->highrise(), $note);

        // done, return the instance of this, to make chaining possible
        return $this;
    }

    /**
     * Add notes
     * 
     * @param  SimpleXMLElement             The SimpleXMLElement containing the notes
     * @return \CompanyNotes                The Instance of this, to make chaining possible
     */
    public function addNotes(SimpleXMLElement $notes) {
        // iterate over all the notes and add a new note for every xml note
        foreach ($notes->children() as $note) {
            $this->addNote($note);
        }

        // done, return the instance of this, to make chaining possible
        return $this;
    }
    
    /**
     * Get the notes
     * 
     * @return SimpleXMLElement[]           The highrise notes
     */
    public function notes() {
        return $this->notes;
    }
}
