<?php
/**
 * Namesilo User Address Management
 *
 * @copyright Copyright (c) 2013, Phillips Data, Inc.
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @package namesilo.commands
 */
class NamesiloUsersAddress {
	
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
	 * Creates a new address for the user
	 *
	 * @param array $vars An array of input params including:
	 * 	- AddressName Address name to create
	 * 	- DefaultYN Possible values are 0 and 1.If the value of this parameter is set to 1, the address is set as default address for the user.
	 * 	- FirstName First name of the user
	 * 	- LastName Last name of the user
	 * 	- JobTitle Job designation of the user
	 * 	- Organization Organization of the user
	 * 	- Address1 StreetAddress1 of the user
	 * 	- Address2 StreetAddress2 of the user
	 * 	- City City of the user
	 * 	- StateProvince State/Province name of the user
	 * 	- StateProvinceChoice State/Province choice of the user
	 * 	- Zip Zip/Postal code of the user
	 * 	- Country Two letter country code of the user
	 * 	- Phone Phone number in the format +NNN.NNNNNNNNNN
	 * 	- Fax Fax number in the format +NNN.NNNNNNNNNN
	 * 	- PhoneExt PhoneExt of the user
	 * 	- EmailAddress Email address of the user
	 * @return NamesiloResponse
	 */
	public function create(array $vars) {
		return $this->api->submit("namesilo.users.address.create", $vars);
	}
	
	/**
	 * Creates a new address for the user
	 *
	 * @param array $vars An array of input params including:
	 * 	- AddressId The unique address ID to update
	 * 	- AddressName Address name to create
	 * 	- DefaultYN Possible values are 0 and 1.If the value of this parameter is set to 1, the address is set as default address for the user.
	 * 	- FirstName First name of the user
	 * 	- LastName Last name of the user
	 * 	- JobTitle Job designation of the user
	 * 	- Organization Organization of the user
	 * 	- Address1 StreetAddress1 of the user
	 * 	- Address2 StreetAddress2 of the user
	 * 	- City City of the user
	 * 	- StateProvince State/Province name of the user
	 * 	- StateProvinceChoice State/Province choice of the user
	 * 	- Zip Zip/Postal code of the user
	 * 	- Country Two letter country code of the user
	 * 	- Phone Phone number in the format +NNN.NNNNNNNNNN
	 * 	- Fax Fax number in the format +NNN.NNNNNNNNNN
	 * 	- PhoneExt PhoneExt of the user
	 * 	- EmailAddress Email address of the user 
	 * @return NamesiloResponse
	 */
	public function update(array $vars) {
		return $this->api->submit("namesilo.users.address.update", $vars);
	}
	
	/**
	 * Creates a new address for the user
	 *
	 * @param array $vars An array of input params including:
	 * 	- AddressId The unique AddressID to delete
	 * @return NamesiloResponse
	 */
	public function delete(array $vars) {
		return $this->api->submit("namesilo.users.address.delete", $vars);
	}
	
	/**
	 * Creates a new address for the user
	 *
	 * @return NamesiloResponse
	 */
	public function getList() {
		return $this->api->submit("namesilo.users.address.getList");
	}
	
	/**
	 * Creates a new address for the user
	 *
	 * @param array $vars An array of input params including:
	 * 	- AddressId The unique addressID
	 * @return NamesiloResponse
	 */
	public function getInfo(array $vars) {
		return $this->api->submit("namesilo.users.address.getInfo", $vars);
	}
	
	/**
	 * Creates a new address for the user
	 *
	 * @param array $vars An array of input params including:
	 * 	- AddressId The unique addressID to set default
	 * @return NamesiloResponse
	 */
	public function setDefault(array $vars) {
		return $this->api->submit("namesilo.users.address.setDefault", $vars);
	}
}
?>