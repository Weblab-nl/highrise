<?php
// add the namespace
namespace Weblab\Highrise;

/**
 * Wrapper around a highrise company
 *
 * @author Weblab.nl - Thomas Marinissen
 */
class Company extends \Weblab\Highrise\Entity {

    /**
     * The entity name
     */
    const NAME = 'companies';

    /**
     * The name of a single entity
     */
    const ENTITY_NAME = 'company';

    /**
     * Load the company notes
     *
     * @return \Weblab\Highrise\Note[]              The company notes
     */
    public function notes() {
        // get the company entity id
        $id = $this->id();

        // if there is no entity id, return out
        if (is_null($id)) {
            return array();
        }

        // construct the url to get all the tasks
        $url = 'companies/' . $id . '/' . \Weblab\Highrise\Note::NAME .'.xml';

        // done, return the tasks
        return $this->allForUrl('\Weblab\Highrise\Note', $url);
    }

    /**
     * Load the company emails
     *
     * @return \Weblab\Highrise\Email[]              The company emails
     */
    public function emails() {
        // get the company entity id
        $id = $this->id();

        // if there is no entity id, return out
        if (is_null($id)) {
            return array();
        }

        // construct the url to get all the tasks
        $url = 'companies/' . $id . '/' . \Weblab\Highrise\Email::NAME .'.xml';

        // done, return the tasks
        return $this->allForUrl('\Weblab\Highrise\Email', $url);
    }

    /**
     * Load the company tasks
     *
     * @return \Weblab\Highrise\Task[]              The company tasks
     */
    public function tasks() {
        // get the company entity id
        $id = $this->id();

        // if there is no entity id, return out
        if (is_null($id)) {
            return array();
        }

        // construct the url to get all the tasks
        $url = 'companies/' . $id . '/' . \Weblab\Highrise\Task::NAME .'.xml';

        // done, return the tasks
        return $this->allForUrl('\Weblab\Highrise\Task', $url);
    }
}
