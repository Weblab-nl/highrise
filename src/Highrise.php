<?php
// add the namespace
namespace Weblab;

/**
 * Class to access the Highrise Api
 *
 * @author Weblab.nl - Thomas Marinissen
 */
class Highrise {

    /**
     * The Highrise identification token
     *
     * @var string
     */
    private $token;

    /**
     * The base url to highrise
     *
     * @var string
     */
    private $baseUrl = 'https://%s.highrisehq.com/';

    /**
     * The subdomain to query
     *
     * @var string
     */
    private $subdomain;

    /**
     * The total time (there is a maximum of 10 calls every 5 seconds)
     *
     * @var float
     */
    private $totalTime = 0;

    /**
     * The total number of calls the last 5 seconds
     *
     * @var int
     */
    private $totalCalls = 0;

    /**
     * Constructor
     *
     * @param   string                  The key needed to connect to Highrise
     * @param   string                  The highrise subdomain to query
     */
    public function __construct($token, $subdomain) {
        // set the access token
        $this->token = $token;

        // set the subdomain
        $this->subdomain = $subdomain;
    }

    /**
     * Make a call on the highrise api
     * 
     * @param  string                           The url to fetch
     * @return SimpleXMLElement|null            The response from the api, null if something went wrong
     */
    public function call($path) {
        // make sure it is possible to call the highrise api
        $this->readyForCall();

        // create the entire path
        $url = sprintf($this->baseUrl, $this->subdomain) . $path;

        // initiate the curl instance
        $curl = curl_init();

        // set the curl options
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: text/xml', 'Content-type: text/xml'));
        curl_setopt($curl, CURLOPT_USERPWD, $this->token . ':x');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        // set the url
        curl_setopt($curl, CURLOPT_URL, $url);

        // execute the curl request
        $response = curl_exec($curl);

        // get the response code
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // if the response code is an error code, return null
        if ($responseCode != 200 && $responseCode != 201) {
            return null;
        }

        // done, return the response as xml object
        return simplexml_load_string($response);
    }


    /**
     * Get the company information from highrise
     * 
     * @param  int                          The Highrise company identifier
     * @return \Company|null                The company from highrise, null whenever there is no company
     */
    public function company($id) {
        // get the company information from highrise
        return \Tpw_Controller_Helper_Highrise_Company::load($this, $id);
    }

    /**
     * Get the highrise users
     *
     * @return Tpw_Controller_Helper_Highrise_Users                 The highrise users
     */
    public function users() {
        return new Tpw_Controller_Helper_Highrise_Users($this);
    }

    /**
     * Return true whenever it is possible to call the highrise api (10 calls
     * every 5 seconds are allowed)
     * 
     * @return boolean
     */
    private function readyForCall() {
        // get the total time
        $totalTime = $this->totalTime();
        
        // get whether it is possible to make a call (not possible if less than
        // 5 seconds have past and the number of calls above 10 already
        $canNotCall = ($totalTime < 10 && $this->totalCalls > 500);
        
        // if it is not possible to call, wait till we can call again
        if ($canNotCall) {
            sleep(10 - $totalTime);
        }

        // if the total time is 0 or the total time is over 5 seconds, or if it
        // was not possible to call before, reset the timer
        if ($totalTime == 0 || $totalTime > 10 || $canNotCall) {
            // reset the time
            $this->resetTimer();
        }
        
        // add 1 to the total number of calls made
        $this->totalCalls++;
        
        // done, return
        return true;
    }
    
    /**
     * Get the total run time since the start of the timer
     * 
     * @return float            The total time
     */
    private function totalTime() {
        // if the timer is not running, just return 0
        if ($this->totalTime == 0) {
            return $this->totalTime;
        }
        
        // the timer is running, return the total time
        return microtime(true) - $this->totalTime;
    }
    
    /**
     * Reset the timer and total number of calls made
     */
    private function resetTimer() {
        $this->totalTime = microtime(true);
        $this->totalCalls = 0;
    }
}
