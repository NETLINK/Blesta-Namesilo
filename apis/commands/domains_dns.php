<?php
/**
 * Namesilo DNS Management
 *
 * @copyright Copyright (c) 2013, Phillips Data, Inc.
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @package namesilo.commands
 */
class NamesiloDomainsDns
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
     * Sets domain to use our default DNS servers. Required for free services
     * like Host record management, URL forwarding, email forwarding, dynamic
     * dns and other value added services.
     *
     * @param array $vars An array of input params including:
     *
     *  - SLD SLD of the DomainName
     *  - TLD TLD of the DomainName
     * @return NamesiloResponse
     */
    public function setDefault(array $vars)
    {
        return $this->api->submit('namesilo.domains.dns.setDefault', $vars);
    }

    /**
     * Sets domain to use custom DNS servers. NOTE: Services like URL forwarding,
     * Email forwarding, Dynamic DNS will not work for domains using custom
     * nameservers.
     *
     * https://www.namesilo.com/api_reference.php#changeNameServers
     */
    public function setCustom(array $vars)
    {
        return $this->api->submit('changeNameServers', $vars);
    }

    /**
     * Gets a list of DNS servers associated with the requested domain.
     *
     * https://www.namesilo.com/api_reference.php#getDomainInfo
     */
    public function getList(array $vars)
    {
        return $this->api->submit('getDomainInfo', $vars);
    }

    /**
     * Retrieves DNS host record settings for the requested domain.
     *
     * https://www.namesilo.com/api_reference.php#dnsListRecords
     */
    public function getHosts(array $vars)
    {
        return $this->api->submit('dnsListRecords', $vars);
    }

    /**
     * Sets DNS host records settings for the requested domain.
     *
     * https://www.namesilo.com/api_reference.php#dnsAddRecord
     */
    public function setHosts(array $vars)
    {
        return $this->api->submit('dnsAddRecord', $vars);
    }

    /**
     * Gets email forwarding settings for the requested domain.
     *
     * https://www.namesilo.com/api_reference.php#listEmailForwards
     */
    public function getEmailForwarding(array $vars)
    {
        return $this->api->submit('listEmailForwards', $vars);
    }

    /**
     * Sets email forwarding for a domain name.
     *
     * https://www.namesilo.com/api_reference.php#configureEmailForward
     */
    public function setEmailForwarding(array $vars)
    {
        return $this->api->submit('configureEmailForward', $vars);
    }

    /**
     * Retrieves DS records
     *
     * https://www.namesilo.com/api_reference.php#dnsSecListRecords
     */
    public function dnsSecListRecords(array $vars)
    {
        return $this->api->submit('dnsSecListRecords', $vars);
    }

    /**
     * Add DS record
     *
     * https://www.namesilo.com/api_reference.php#dnsSecListRecords
     */
    public function dnsSecAddRecord(array $vars)
    {
        return $this->api->submit('dnsSecAddRecord', $vars);
    }

    /**
     * Delete DS record
     *
     * https://www.namesilo.com/api_reference.php#dnsSecListRecords
     */
    public function dnsSecDeleteRecord(array $vars)
    {
        return $this->api->submit('dnsSecDeleteRecord', $vars);
    }

    /**
     * Retrieves DNS records
     * https://www.namesilo.com/api_reference.php#dnsListRecords
     */
    public function dnsListRecords(array $vars)
    {
        return $this->api->submit('dnsListRecords', $vars);
    }

    /**
     * Add a DNS record
     * https://www.namesilo.com/api_reference.php#dnsAddRecord
     */
    public function dnsAddRecord(array $vars)
    {
        return $this->api->submit('dnsAddRecord', $vars);
    }

    /**
     * Update a DNS record
     * https://www.namesilo.com/api_reference.php#dnsUpdateRecord
     */
    public function dnsUpdateRecord(array $vars) {
        return $this->api->submit('dnsUpdateRecord', $vars);
    }

    /**
     * Delete a DNS record
     * https://www.namesilo.com/api_reference.php#dnsDeleteRecord
     */
    public function dnsDeleteRecord(array $vars)
    {
        return $this->api->submit('dnsDeleteRecord', $vars);
    }
}
