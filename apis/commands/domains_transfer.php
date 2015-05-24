<?php
/**
 * Namesilo Domain Transfer Management
 *
 * @copyright Copyright (c) 2013, Phillips Data, Inc.
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @package namesilo.commands
 */
class NamesiloDomainsTransfer {
	
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
	 * Transfers a domain to Namesilo.
	 *
	 * @param array $vars An array of input params including:
	 * 	- domain Domain name to transfer
	 * 	- years Number of years to renew after a successful transfer
	 * 	- EPPCode The EPPCode is required for transferring .com, .net, .de, .org, .biz, .info, .mobi, .cn , .co, .ca and .us domains only.
	 * 	- PromotionCode Promotional (coupon) code for transfer
	 * 	- AddFreeWhoisguard Adds free Whoisguard for the domain
	 * 	- WGEnable Promotional (coupon) code for transfer
	 * @return NamesiloResponse
	 *
	 * https://www.namesilo.com/api_reference.php#transferDomain
	 */
	public function create( array $vars ) {
		if ( isset( $vars['auth'] ) && substr( $vars['auth'], 0, 7 ) != "base64:" )
			$vars['auth'] = "base64:" . base64_encode( $vars['auth'] );
		return $this->api->submit( "transferDomain", $vars );
	}
	
	/**
	 * Requests the EPP Code for the given domain. The code is not returned,
	 * but is instead emailed to the registered domain contact (under whois).
	 * 
	 * @param array $vars An array of input params including:
	 *	- DomainName Domain name to get EPP code for
	 *	- Reason (optional) Should be one of: price, support, technical, others
	 *	- Description (optional) More information regarding the reason if available. Max length: 200
	 *	- Contact (optional) If customer can be contacted regarding this. Value should be: true/ false
	 * @return NamesiloResponses
	 */
	public function getEpp(array $vars) {
		return $this->api->submit("retrieveAuthCode", $vars);
	}
	
	/**
	 * Gets the status of a particular transfer.
	 *
	 * @param array $vars An array of input params including:
	 * 	- TransferID The unique Transfer ID which you get after placing a transfer request
	 * @return NamesiloResponse
	 */
	public function getStatus( array $vars ) {
		return $this->api->submit( "checkTransferStatus", $vars );
	}
	
	/**
	 * Updates the status of a particular transfer. Allows you to re-submit the
	 * transfer after releasing the registry lock.
	 *
	 * @param array $vars An array of input params including:
	 * 	- TransferID The unique TransferID
	 * 	- Resubmit The value 'true' resubmits the transfer
	 * @return NamesiloResponse
	 */
	public function updateStatus(array $vars) {
		return $this->api->submit("namesilo.domains.transfer.updateStatus", $vars);
	}
	
	/**
	 * Gets the list of domain transfers.
	 *
	 * @param array $vars An array of input params including:
	 * 	- ListType Possible values are ALL,INPROGRESS,CANCELLED,COMPLETED 
	 * 	- SearchTerm The keyword should be a domainname 
	 * 	- Page Page to return 
	 * 	- PageSize Number of transfers to be listed in a page. Minimum value is 10 and maximum value is 100.
	 * 	- SortBy Possible values are DOMAINNAME,DOMAINNAME_DESC,TRANSFERDATE,TRANSFERDATE_DESC,STATUSDATE,STATUSDATE_DESC.
	 * @return NamesiloResponse
	 */
	public function getList(array $vars) {
		return $this->api->submit("namesilo.domains.transfer.getList", $vars);
	}
}
?>