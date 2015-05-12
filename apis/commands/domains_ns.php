<?php
/**
 * Namesilo Nameserver Management
 *
 * @copyright Copyright (c) 2013, Phillips Data, Inc.
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @package namesilo.commands
 */
class NamesiloDomainsNs {
	
	/**
	 * @var NamesiloApi
	 */
	private $api;
		
	/**
	 * Sets the API to use for communication
	 *
	 * @param NamesiloApi $api The API to use for communication
	 */
	public function __construct(NamesiloApi $api) {
		$this->api = $api;
	}
	
	/**
	 * Creates a new nameserver.
	 *
	 * @param array $vars An array of input params including:
	 * 	- SLD SLD of domain
	 * 	- TLD TLD of domain
	 * 	- Nameserver Nameserver to create
	 * 	- IP Nameserver IP address
	 * @return NamesiloResponse
	 */
	public function create(array $vars) {
		return $this->api->submit("namesilo.domains.ns.create", $vars);
	}

	/**
	 * Deletes a nameserver associated with the requested domain.
	 *
	 * @param array $vars An array of input params including:
	 * 	- SLD SLD of domain
	 * 	- TLD TLD of domain
	 * 	- Nameserver Nameserver for deletion
	 * @return NamesiloResponse
	 */	
	public function delete(array $vars) {
		return $this->api->submit("namesilo.domains.ns.delete", $vars);
	}
	
	/**
	 * Retrieves information about a registered nameserver.
	 *
	 * @param array $vars An array of input params including:
	 * 	- SLD SLD of domain
	 * 	- TLD TLD of domain
	 * 	- Nameserver Nameserver
	 * @return NamesiloResponse
	 */
	public function getInfo(array $vars) {
		return $this->api->submit("namesilo.domains.ns.getInfo", $vars);
	}
	
	/**
	 * Updates the IP address of a registered nameserver.
	 *
	 * @param array $vars An array of input params including:
	 * 	- SLD SLD of domain
	 * 	- TLD TLD of domain
	 * 	- Nameserver Nameserver Name
	 * 	- OldIP Existing IP address
	 * 	- IP New IP address
	 * @return NamesiloResponse
	 */
	public function update(array $vars) {
		return $this->api->submit("namesilo.domains.ns.update", $vars);
	}
}
?>