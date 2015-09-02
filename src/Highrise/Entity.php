<?php
// add the namespace
namespace Weblab\Highrise;

/**
 * Abstract base class for a Highrise entity, adding functionality to perform
 * CRUD operations ont the Highrise entity.
 *
 * @author Weblab.nl - Thomas Marinissen
 */
abstract class Entity {

    /**
     * The Highrise API instance
     *
     * @var \Weblab\Highrise
     */
    protected $highriseApi;

    /**
     * The entity identifier
     *
     * @var null|int
     */
    protected $id = null;

    /**
     * The entity information
     *
     * @var \SimpleXMLElement
     */
    protected $entity;

    /**
     * Constructor
     *
     * @param   \Weblab\Highrise                            The highrise api instance
     * @param   string|null                                 The entity identifier
     */
    public function __construct(\Weblab\Highrise $highrise, $id = null) {
        // get access to the highrise API
        $this->highriseApi = $highrise;

        // store the identifier
        $this->id = $id;

        // set the entity base
        $entity = new \SimpleXMLElement('<' . static::ENTITY_NAME . '/>');

        // if a entity id was given, get the entity
        if (!is_null($id)) {
            // get the entity from the highrise api
            $entity = $this->api()->call(static::NAME . '/' . $id . '.xml');
        }

        // if there is no valid entity, throw an exception
        if (is_null($entity)) {
            throw new \Exception('Not possible to request the ' . static::NAME);
        }

        // set the entity
        $this->entity = $entity;
    }

    /**
     * Magic getter method
     *
     * @param   string                              The name of the getter
     * @return  mixed                               The value to get
     */
    public function __get($name) {
        // if there is no value in the entity for the given name, return null
        if (!isset($this->entity->{$name})) {
            return null;
        }

        // return the value for the given name
        return $this->entity->{$name};
    }

    /**
     * Set the entity body
     *
     * @param   \SimpleXMLElement                       The entity to set as body of this
     * @return  \Weblab\Highrise\Entity                 The instance of this, to make chaining possible
     */
    public function setEntity(\SimpleXMLElement $entity) {
        // set the entity
        $this->entity = $entity;

        // done, return the instance of this, to make chaining possible
        return $this;
    }

    /**
     * Static method to get the highrise entity
     *
     * @param   \Weblab\Highrise                           The highrise api instance
     * @param   int|null                                   The highrise entity identifier
     * @return  \Weblab\Highrise\Entity                    The fetched entity from highrise
     *
     * @throws \Exception
     */
    public static function get(\Weblab\Highrise $highrise, $id = null) {
        // get the name of the called class
        $className = get_called_class();

        // try getting the entity for the given id from the Highrise API
        try {
            $entity = new $className($highrise, $id);
        } catch (\Exception $e) {
            return null;
        }

        // done, evertying is all right, return the highrise entity
        return $entity;
    }

    /**
     * Get the instance of the highrise api
     *
     * @return \Weblab\Highrise                The instance of the Highrise api
     */
    public function api() {
        return $this->highriseApi;
    }

    /**
     * Get the id
     *
     * @return int                  The entity identifier
     */
    public function id() {
        return $this->id;
    }

    /**
     * get all entities for a set url
     *
     * @param   string                              The url to fetch
     * @return  \Weblab\Highrise\Entity[]           A collection of highrise entities
     */
    protected function allForUrl($class, $url) {
        // set the offset
        $offset = 0;

        // variable for storing all the entities
        $entities = array();

        // get all the sub entities
        while (true) {
            // load the entities for the current iteration
            $entitiesHighrise = $this->api()->call($url . '?n=' . $offset);

            // if there are no entities, break out
            if (is_null($entitiesHighrise)) {
                break;
            }

            // get all the the children of the collection and create an Entity for every highrise xml element
            foreach ($entitiesHighrise->children() as $highriseEntity) {
                // create a new entity object to store the data of the current iteration
                $entity = $class::get($this->api());

                // if it was not possible to create an entity, continue
                if (is_null($entity)) {
                    continue;
                }

                // add the entity to the array
                $entities[] = $entity
                    ->setEntity($highriseEntity);
            }

            // if all the entities where collected, break the while loop
            if ($entitiesHighrise->count() < 25) {
                break;
            }

            // continue to the next page
            $offset += 25;
        }

        // done, return all the entities
        return $entities;
    }

    /**
     * Return the entity as XML
     *
     * @return string
     */
    public function __toString() {
        return $this->entity->asXML();
    }

}
