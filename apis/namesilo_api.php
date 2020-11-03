<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'namesilo_response.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'commands' . DIRECTORY_SEPARATOR . 'domains.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'commands' . DIRECTORY_SEPARATOR . 'domains_dns.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'commands' . DIRECTORY_SEPARATOR . 'domains_ns.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'commands' . DIRECTORY_SEPARATOR . 'domains_transfer.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'commands' . DIRECTORY_SEPARATOR . 'users_address.php';

/**
 * Namesilo API processor
 *
 * Documentation on the Namesilo API: http://www.namesilo.com/api_reference.php
 *
 * @copyright Copyright (c) 2013, Phillips Data, Inc.
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @package namesilo
 */
class NamesiloApi
{
    const SANDBOX_URL = 'http://sandbox.namesilo.com/api';
    const LIVE_URL = 'https://www.namesilo.com/api';

    /**
     * @var string API version
     */
    private $api_version = 1;
    /**
     * @var string The format of the API response
     */
    private $format = 'xml';
    /**
     * @var string The user to connect as
     */
    private $user;
    /**
     * @var string The username to execute an API command using
     */
    private $username;
    /**
     * @var string The key to use when connecting
     */
    private $key;
    /**
     * @var bool Whether or not to process in sandbox mode (for testing)
     */
    private $sandbox;
    /**
     * @var array An array representing the last request made
     */
    private $last_request = ['url' => null, 'args' => null];
    /**
     * @var bool use batch api url if true
     */
    private $batch;
    /**
     * @var int http return code
     */
    public $httpcode;

    /**
     * Sets the connection details
     *
     * @param string $user The user to connect as
     * @param string $key The key to use when connecting
     * @param bool $sandbox Whether or not to process in sandbox mode (for testing)
     * @param string $username The username to execute an API command using
     * @param bool $batch Set true to pass commands to batch API URL.
     *      See https://www.namesilo.com/Support/API-Automated-Batch-Processing
     */
    public function __construct($user, $key, $sandbox = true, $username = null, $batch = false)
    {
        $this->user = $user;
        $this->key = $key;

        $this->sandbox = filter_var($sandbox, FILTER_VALIDATE_BOOLEAN);

        if (!$username) {
            $username = $user;
        }

        $this->username = $username;

        $this->batch = $batch;
    }

    /**
     * Submits a request to the API
     *
     * @param string $command The command to submit
     * @param array $args An array of key/value pair arguments to submit to the given API command
     * @return NamesiloResponse The response object
     */
    public function submit($command, array $args = [])
    {
        $url = self::LIVE_URL;
        if ($this->sandbox) {
            $url = self::SANDBOX_URL;
        }

        if ($this->batch) {
            $url = $url . 'batch';
        }

        $url .= '/' . $command . '?key=' . $this->key;

        $args['version'] = $this->api_version;
        $args['type'] = $this->format;

        $this->last_request = [
            'url' => $url,
            'args' => $args
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        $this->httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return new NamesiloResponse($response);
    }

    /**
     * Returns the details of the last request made
     *
     * @return array An array containg:
     *
     *  - url The URL of the last request
     *  - args The paramters passed to the URL
     */
    public function lastRequest()
    {
        return $this->last_request;
    }
}
