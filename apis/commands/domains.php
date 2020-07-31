<?php
/**
 * Namesilo Domain Management
 *
 * @copyright Copyright (c) 2013, Phillips Data, Inc.
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @package namesilo.commands
 */
class NamesiloDomains
{
    /**
     * @var NamesiloApi
     */
    private $api;

    /**
     * Sets the API to use for communication
     *
     * @param NamesiloApi $api The API to use for communication
     */
    public function __construct(NamesiloApi $api)
    {
        $this->api = $api;
    }

    /**
     * Returns a list of domains for the particular user.
     *
     * @param array $vars An array of input params including:
     *
     *  - portfolio (optional) portfolio to fetch domains for
     * @return NamesiloResponse
     */
    public function getList(array $vars)
    {
        return $this->api->submit('listDomains', $vars);
    }

    /**
     * Returns a list of tlds
     *
     * @return NamesiloResponse
     */
    public function getTldList()
    {
        return $this->api->submit('namesilo.domains.getTldList');
    }

    /**
     * Registers a new domain name.
     *
     * https://www.namesilo.com/api_reference.php#registerDomain
     */
    public function create(array $vars)
    {
        return $this->api->submit('registerDomain', array_merge($vars, ['auto_renew' => 0]));
    }

    /**
     * Get essential information on a particular domain, including the expiration date, creation date, status,
     * locked status and nameservers.
     *
     * https://www.namesilo.com/api_reference.php#getDomainInfo
     */
    public function getDomainInfo(array $vars)
    {
        return $this->api->submit('getDomainInfo', $vars);
    }

    /**
     * Gets contact information for the requested domain.
     *
     * @param array $vars An array of input params including:
     *
     *  - DomainName Domain to get contacts
     * @return NamesiloResponse
     */
    public function getContacts(array $vars)
    {
        return $this->api->submit('contactList', $vars);

        $response = self::getDomainInfo($vars);

        if (parent::$codes[$response->status()][1] != 'fail') {
            $contact_ids = $response->response()->contact_ids;

            $contacts = $temp = [];
            foreach ($contact_ids as $type => $id) {
                if (!isset($temp[$id])) {
                    $response = $this->api->submit('contactList', ['contact_id' => $id]);
                    if (parent::$codes[$response->status()][1] != 'fail') {
                        $temp[$id] = $response->response()->contact;
                        $contacts[$type] = $temp[$id];
                    }
                } else {
                    $contacts[$type] = $temp[$id];
                }
            }
            return $contacts;
        }
        return false;
    }

    /**
     * Adds a contact
     *
     * @param array $vars An array of contact information
     * @return NamesiloResponse
     */
    public function addContacts(array $vars)
    {
        return $this->api->submit('contactAdd', $vars);
    }

    /**
     * Sets contact information for the requested domain.
     *
     * @return NamesiloResponse
     */
    public function setContacts(array $vars)
    {
        return $this->api->submit('contactDomainAssociate', $vars);
    }

    /**
     * Delete a contact profile in your account. Please remember
     * that the only contact profiles that can be deleted are those
     * that are not the account default and are not associated with
     * any active domains or order profiles.
     *
     * https://www.namesilo.com/api_reference.php#contactDelete
     */
    public function deleteContacts(array $vars)
    {
        return $this->api->submit('contactDelete', $vars);
    }

    /**
     * Checks the availability of a domain name.
     *
     * https://www.namesilo.com/api_reference.php#checkRegisterAvailability
     */
    public function check(array $vars)
    {
        return $this->api->submit('checkRegisterAvailability', $vars);
    }

    /**
     * Reactivates an expired domain.
     *
     * @param array $vars An array of input params including:
     *
     *  - DomainName DomainName to reactivate
     * @return NamesiloResponse
     */
    public function reactivate(array $vars)
    {
        return $this->api->submit('namesilo.domains.reactivate', $vars);
    }

    /**
     * Renews a domain.
     *
     * @param array $vars An array of input params including:
     *
     *  - domain DomainName to renew
     *  - years Number of years to renew
     *  - coupon Promotional (coupon) code for renewing the domain
     * @return NamesiloResponse
     *
     * https://www.namesilo.com/api_reference.php#renewDomain
     */
    public function renew(array $vars)
    {
        return $this->api->submit('renewDomain', $vars);
    }

    /**
     * Gets the RegistrarLock status for the requested domain.
     *
     * @param array $vars An array of input params including:
     *  - domain Domain name to get status
     *
     * @return NamesiloResponse
     */
    public function getRegistrarLock(array $vars)
    {
        return $this->api->submit('getDomainInfo', $vars);
    }

    /**
     * Sets the RegistrarLock status for a domain.
     *
     * @param array $vars An array of input params including:
     *
     *  - domain Domain name to set status
     *  - LockAction Possible values are LOCK and UNLOCK
     * @return NamesiloResponse
     */
    public function setRegistrarLock($lock_action, array $vars)
    {
        return $this->api->submit('domain' . $lock_action, $vars);
    }

    /**
     * Sets the Auto Renew for a domain.
     *
     * @param array $vars An array of input params including:
     *
     *  - domain Domain name to set status
     *  - autorenew (boolean) true to enable auto renewal, (boolean) false to disable
     * @return NamesiloResponse
     */
    public function setAutoRenewal($domain, $autorenew = false)
    {
        if (!$autorenew) {
            $action = 'remove';
        } else {
            $action = 'add';
        }
        return $this->api->submit($action . 'AutoRenewal', ['domain' => $domain]);
    }

    /**
     * Adds privacy
     *
     * @param array $vars An array of data
     *
     *  - domain
     * @return NamesiloResponse
     */
    public function addPrivacy(array $vars)
    {
        return $this->api->submit('addPrivacy', $vars);
    }

    /**
     * Removes privacy
     *
     * @param array $vars An array of data
     *
     *  - domain
     * @return NamesiloResponse
     */
    public function removePrivacy(array $vars)
    {
        return $this->api->submit('removePrivacy', $vars);
    }

    /**
     * @param array $vars An array of data
     * @return NamesiloResponse
     */
    public function portfolioList()
    {
        return $this->api->submit('portfolioList');
    }

    /**
     * @return NamesiloResponse
     */
    public function getPrices()
    {
        return $this->api->submit('getPrices');
    }

    /**
     * @return NamesiloResponse
     */
    public function registrantVerificationStatus()
    {
        return $this->api->submit('registrantVerificationStatus');
    }

    /**
     * Performs email verification
     *
     * @param array $vars An array of data
     *
     *  - email string email address to verify
     * @return NamesiloResponse
     */
    public function emailVerification(array $vars)
    {
        return $this->api->submit('emailVerification', $vars);
    }
}
