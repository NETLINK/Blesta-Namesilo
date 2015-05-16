<?php
/**
 * Namesilo Module
 *
 * @package blesta
 * @subpackage blesta.components.modules.namesilo
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @link http://www.blesta.com/ Blesta
 * @copyright Copyright (c) 2015, NETLINK IT SERVICES
 * @link http://www.netlink.ie/ NETLINK
 */
class Namesilo extends Module {
	
	/**
	 * @var string The version of this module
	 */
	private static $version = "1.0.0-alpha";
	/**
	 * @var array The authors of this module
	 */
	private static $authors = array(
		array(
			'name'=> "Phillips Data, Inc.",
			'url'=>"http://www.blesta.com"
		),
		array(
			'name' => "NETLINK IT SERVICES",
			'url' => "http://www.netlink.ie/"
		),
	);
	
	private static $debug_to = "root@localhost";
	
	private static $codes;

	/**
	 * Initializes the module
	 */
	public function __construct() {
		// Load components required by this module
		Loader::loadComponents($this, array("Input"));
		
		// Load the language required by this module
		Language::loadLang("namesilo", null, dirname(__FILE__) . DS . "language" . DS);
		
		Configure::load("namesilo", dirname(__FILE__) . DS . "config" . DS);
		
		self::$codes = Configure::get( 'Namesilo.status.codes' );
	}

	/**
	 * Returns the name of this module
	 *
	 * @return string The common name of this module
	 */
	public function getName() {
		return Language::_("Namesilo.name", true);
	}
	
	/**
	 * Returns the version of this module
	 *
	 * @return string The current version of this module
	 */
	public function getVersion() {
		return self::$version;
	}

	/**
	 * Returns the name and URL for the authors of this module
	 *
	 * @return array A numerically indexed array that contains an array with key/value pairs for 'name' and 'url', representing the name and URL of the authors of this module
	 */
	public function getAuthors() {
		return self::$authors;
	}
	
	/**
	 * Returns the value used to identify a particular service
	 *
	 * @param stdClass $service A stdClass object representing the service
	 * @return string A value used to identify this service amongst other similar services
	 */
	public function getServiceName($service) {
		foreach ($service->fields as $field) {
			if ($field->key == "DomainName")
				return $field->value;
		}
		return null;
	}
	
	/**
	 * Returns a noun used to refer to a module row (e.g. "Server", "VPS", "Reseller Account", etc.)
	 *
	 * @return string The noun used to refer to a module row
	 */
	public function moduleRowName() {
		return Language::_("Namesilo.module_row", true);
	}
	
	/**
	 * Returns a noun used to refer to a module row in plural form (e.g. "Servers", "VPSs", "Reseller Accounts", etc.)
	 *
	 * @return string The noun used to refer to a module row in plural form
	 */
	public function moduleRowNamePlural() {
		return Language::_("Namesilo.module_row_plural", true);
	}
	
	/**
	 * Returns a noun used to refer to a module group (e.g. "Server Group", "Cloud", etc.)
	 *
	 * @return string The noun used to refer to a module group
	 */
	public function moduleGroupName() {
		return null;
	}
	
	/**
	 * Returns the key used to identify the primary field from the set of module row meta fields.
	 * This value can be any of the module row meta fields.
	 *
	 * @return string The key used to identify the primary field from the set of module row meta fields
	 */
	public function moduleRowMetaKey() {
		return "user";
	}
	
	/**
	 * Returns the value used to identify a particular package service which has
	 * not yet been made into a service. This may be used to uniquely identify
	 * an uncreated services of the same package (i.e. in an order form checkout)
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param array $vars An array of user supplied info to satisfy the request
	 * @return string The value used to identify this package service
	 * @see Module::getServiceName()
	 */
	public function getPackageServiceName($packages, array $vars=null) {
		if (isset($vars['DomainName']))
			return $vars['DomainName'];
		return null;
	}
	
	/**
	 * Attempts to validate service info. This is the top-level error checking method. Sets Input errors on failure.
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param array $vars An array of user supplied info to satisfy the request
	 * @return boolean True if the service validates, false otherwise. Sets Input errors when false.
	 */
	public function validateService($package, array $vars=null) {
		return true;
	}
	
	/**
	 * Adds the service to the remote server. Sets Input errors on failure,
	 * preventing the service from being added.
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param array $vars An array of user supplied info to satisfy the request
	 * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
	 * @param stdClass $parent_service A stdClass object representing the parent service of the service being added (if the current service is an addon service and parent service has already been provisioned)
	 * @param string $status The status of the service being added. These include:
	 * 	- active
	 * 	- canceled
	 * 	- pending
	 * 	- suspended
	 * @return array A numerically indexed array of meta fields to be stored for this service containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function addService($package, array $vars=null, $parent_package=null, $parent_service=null, $status="pending") {
		
		$row = $this->getModuleRow($package->module_row);
		$api = $this->getApi($row->meta->user, $row->meta->key, $row->meta->sandbox == "true");
		
		#
		# TODO: Handle validation checks
		#
		
		$tld = null;
		$input_fields = array();
		
		if ($package->meta->type == "domain") {
			if (array_key_exists("EPPCode", $vars))
				$input_fields = array_merge(Configure::get("Namesilo.transfer_fields"), array('Years' => true));
			else {
				if (isset($vars['DomainName']))
					$tld = $this->getTld($vars['DomainName']);
				
				$whois_fields = Configure::get("Namesilo.whois_fields");
				$input_fields = array_merge(Configure::get("Namesilo.domain_fields"), $whois_fields, (array)Configure::get("Namesilo.domain_fields" . $tld), array('Years' => true, 'Nameservers' => true));
			}
		}
		
		if (isset($vars['use_module']) && $vars['use_module'] == "true") {
			if ($package->meta->type == "domain") {

				$vars['Years'] = 1;
				
				foreach ($package->pricing as $pricing) {
					if ($pricing->id == $vars['pricing_id']) {
						$vars['Years'] = $pricing->term;
						break;
					}
				}
				
				// Handle transfer
				if (isset($vars['transfer']) || isset($vars['EPPCode'])) {
					$fields = array_intersect_key($vars, $input_fields);
					
					$transfer = new NamesiloDomainsTransfer($api);
					$response = $transfer->create($fields);
					$this->processResponse($api, $response);
					
					if ($this->Input->errors())
						return;
					
					return array(array('key' => "DomainName", 'value' => $fields['DomainName'], 'encrypted' => 0));
				}
				// Handle registration
				else {
					
					// Set all whois info from client ($vars['client_id'])
					if (!isset($this->Clients))
						Loader::loadModels($this, array("Clients"));
                    if (!isset($this->Contacts))
                        Loader::loadModels($this, array("Contacts"));
						
					$client = $this->Clients->get($vars['client_id']);
                    if ($client)
                        $contact_numbers = $this->Contacts->getNumbers($client->contact_id);
					
					foreach ($whois_fields as $key => $value) {
						if (strpos($key, "FirstName") !== false)
							$vars[$key] = $client->first_name;
						elseif (strpos($key, "LastName") !== false)
							$vars[$key] = $client->last_name;
						elseif (strpos($key, "Address1") !== false)
							$vars[$key] = $client->address1;
						elseif (strpos($key, "Address2") !== false)
							$vars[$key] = $client->address2;
						elseif (strpos($key, "City") !== false)
							$vars[$key] = $client->city;
						elseif (strpos($key, "StateProvince") !== false)
							$vars[$key] = $client->state;
						elseif (strpos($key, "PostalCode") !== false)
							$vars[$key] = $client->zip;
						elseif (strpos($key, "Country") !== false)
							$vars[$key] = $client->country;
						elseif (strpos($key, "Phone") !== false)
							$vars[$key] = $this->formatPhone(isset($contact_numbers[0]) ? $contact_numbers[0]->number : null, $client->country);
						elseif (strpos($key, "EmailAddress") !== false)
							$vars[$key] = $client->email;
					}
					
					// Set custom nameservers as CSV
					$nameservers = "";
					for ($i=1; $i<=5; $i++) {
						if (isset($vars["ns" . $i]) && $vars["ns" . $i] != "")
							$nameservers .= (empty($nameservers) ? "" : ",")  . $vars["ns" . $i];
					}
					
					if (!empty($nameservers))
						$vars['Nameservers'] = $nameservers;
					
					if ($tld = ".asia")
						$vars['ASIACCLocality'] = $client->country;
					
					$fields = array_intersect_key($vars, $input_fields);
					
					$domains = new NamesiloDomains($api);
					$response = $domains->create($fields);
					$this->processResponse($api, $response);
					
					if ($this->Input->errors())
						return;
					
					return array(array('key' => "DomainName", 'value' => $vars['DomainName'], 'encrypted' => 0));
				}
			}
			else {
				
				#
				# TODO: we'll see...
				#
			}
		}
		
		$meta = array();
		$fields = array_intersect_key($vars, $input_fields);
		foreach ($fields as $key => $value) {
			$meta[] = array(
				'key' => $key,
				'value' => $value,
				'encrypted' => 0
			);
		}

		return $meta;
	}
	
	/**
	 * Edits the service on the remote server. Sets Input errors on failure,
	 * preventing the service from being edited.
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $vars An array of user supplied info to satisfy the request
	 * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
	 * @param stdClass $parent_service A stdClass object representing the parent service of the service being edited (if the current service is an addon service)
	 * @return array A numerically indexed array of meta fields to be stored for this service containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function editService($package, $service, array $vars=array(), $parent_package=null, $parent_service=null) {
		return null; // All this handled by admin/client tabs instead
	}
	
	/**
	 * Cancels the service on the remote server. Sets Input errors on failure,
	 * preventing the service from being canceled.
	 */
	public function cancelService($package, $service, $parent_package=null, $parent_service=null) {
		return null; // Nothing to do
	}
	
	/**
	 * Suspends the service on the remote server. Sets Input errors on failure,
	 * preventing the service from being suspended.
	 */
	public function suspendService($package, $service, $parent_package=null, $parent_service=null) {
		return null; // Nothing to do
	}
	
	/**
	 * Unsuspends the service on the remote server. Sets Input errors on failure,
	 * preventing the service from being unsuspended.
	 */
	public function unsuspendService($package, $service, $parent_package=null, $parent_service=null) {
		return null; // Nothing to do
	}
	
	/**
	 * Allows the module to perform an action when the service is ready to renew.
	 * Sets Input errors on failure, preventing the service from renewing.
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
	 * @param stdClass $parent_service A stdClass object representing the parent service of the service being renewed (if the current service is an addon service)
	 * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function renewService($package, $service, $parent_package=null, $parent_service=null) {
		return null;
	}
	
	/**
	 * Updates the package for the service on the remote server. Sets Input
	 * errors on failure, preventing the service's package from being changed.
	 */
	public function changeServicePackage($package_from, $package_to, $service, $parent_package=null, $parent_service=null) {
		return null; // Nothing to do
	}

	/**
	 * Validates input data when attempting to add a package, returns the meta
	 * data to save when adding a package. Performs any action required to add
	 * the package on the remote server. Sets Input errors on failure,
	 * preventing the package from being added.
	 *
	 * @param array An array of key/value pairs used to add the package
	 * @return array A numerically indexed array of meta fields to be stored for this package containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function addPackage(array $vars=null) {
		
		$meta = array();
		if (isset($vars['meta']) && is_array($vars['meta'])) {
			// Return all package meta fields
			foreach ($vars['meta'] as $key => $value) {
				$meta[] = array(
					'key' => $key,
					'value' => $value,
					'encrypted' => 0
				);
			}
		}
		
		return $meta;
	}
	
	/**
	 * Validates input data when attempting to edit a package, returns the meta
	 * data to save when editing a package. Performs any action required to edit
	 * the package on the remote server. Sets Input errors on failure,
	 * preventing the package from being edited.
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param array An array of key/value pairs used to edit the package
	 * @return array A numerically indexed array of meta fields to be stored for this package containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 * @see Module::getModule()
	 * @see Module::getModuleRow()
	 */
	public function editPackage($package, array $vars=null) {
		
		$meta = array();
		if (isset($vars['meta']) && is_array($vars['meta'])) {
			// Return all package meta fields
			foreach ($vars['meta'] as $key => $value) {
				$meta[] = array(
					'key' => $key,
					'value' => $value,
					'encrypted' => 0
				);
			}
		}
		
		return $meta;	
	}
	
	/**
	 * Returns the rendered view of the manage module page
	 *
	 * @param mixed $module A stdClass object representing the module and its rows
	 * @param array $vars An array of post data submitted to or on the manage module page (used to repopulate fields after an error)
	 * @return string HTML content containing information to display when viewing the manager module page
	 */
	public function manageModule($module, array &$vars) {
		// Load the view into this object, so helpers can be automatically added to the view
		$this->view = new View("manage", "default");
		$this->view->base_uri = $this->base_uri;
		$this->view->setDefaultView("components" . DS . "modules" . DS . "namesilo" . DS);
		
		#
		#
		# TODO: add tab to check status of all transfers: check if possible with Namesilo... ref: NamesiloDomainsTransfer->getList()
		#
		#
		
		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html", "Widget"));

		$this->view->set("module", $module);
		
		return $this->view->fetch();
	}
	
	/**
	 * Returns the rendered view of the add module row page
	 *
	 * @param array $vars An array of post data submitted to or on the add module row page (used to repopulate fields after an error)
	 * @return string HTML content containing information to display when viewing the add module row page
	 */
	public function manageAddRow(array &$vars) {
		// Load the view into this object, so helpers can be automatically added to the view
		$this->view = new View("add_row", "default");
		$this->view->base_uri = $this->base_uri;
		$this->view->setDefaultView("components" . DS . "modules" . DS . "namesilo" . DS);
		
		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html", "Widget"));
		
		// Set unspecified checkboxes
		if (!empty($vars)) {
			if (empty($vars['sandbox']))
				$vars['sandbox'] = "false";
		}
		
		$this->view->set("vars", (object)$vars);
		return $this->view->fetch();	
	}

	/**
	 * Returns the rendered view of the edit module row page
	 *
	 * @param stdClass $module_row The stdClass representation of the existing module row
	 * @param array $vars An array of post data submitted to or on the edit module row page (used to repopulate fields after an error)
	 * @return string HTML content containing information to display when viewing the edit module row page
	 */	
	public function manageEditRow($module_row, array &$vars) {
		// Load the view into this object, so helpers can be automatically added to the view
		$this->view = new View("edit_row", "default");
		$this->view->base_uri = $this->base_uri;
		$this->view->setDefaultView("components" . DS . "modules" . DS . "namesilo" . DS);
		
		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html", "Widget"));
		
		if (empty($vars))
			$vars = $module_row->meta;
		else {
			// Set unspecified checkboxes
			if (empty($vars['sandbox']))
				$vars['sandbox'] = "false";
		}
		
		$this->view->set("vars", (object)$vars);
		return $this->view->fetch();
	}
	
	/**
	 * Adds the module row on the remote server. Sets Input errors on failure,
	 * preventing the row from being added.
	 *
	 * @param array $vars An array of module info to add
	 * @return array A numerically indexed array of meta fields for the module row containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 */
	public function addModuleRow(array &$vars) {
		$meta_fields = array("user", "key", "sandbox");
		$encrypted_fields = array("key");

		// Set unspecified checkboxes
		if (empty($vars['sandbox']))
			$vars['sandbox'] = "false";
		
		$this->Input->setRules($this->getRowRules($vars));
		
		// Validate module row
		if ($this->Input->validates($vars)) {

			// Build the meta data for this row
			$meta = array();
			foreach ($vars as $key => $value) {
				
				if (in_array($key, $meta_fields)) {
					$meta[] = array(
						'key' => $key,
						'value' => $value,
						'encrypted' => in_array($key, $encrypted_fields) ? 1 : 0
					);
				}
			}
			
			return $meta;
		}
	}
	
	/**
	 * Edits the module row on the remote server. Sets Input errors on failure,
	 * preventing the row from being updated.
	 *
	 * @param stdClass $module_row The stdClass representation of the existing module row
	 * @param array $vars An array of module info to update
	 * @return array A numerically indexed array of meta fields for the module row containing:
	 * 	- key The key for this meta field
	 * 	- value The value for this key
	 * 	- encrypted Whether or not this field should be encrypted (default 0, not encrypted)
	 */
	public function editModuleRow($module_row, array &$vars) {
		// Same as adding
		return $this->addModuleRow($vars);
	}
	
	/**
	 * Deletes the module row on the remote server. Sets Input errors on failure,
	 * preventing the row from being deleted.
	 *
	 * @param stdClass $module_row The stdClass representation of the existing module row
	 */
	public function deleteModuleRow($module_row) {
		
	}
	
	/**
	 * Returns all fields used when adding/editing a package, including any
	 * javascript to execute when the page is rendered with these fields.
	 *
	 * @param $vars stdClass A stdClass object representing a set of post fields
	 * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
	 */
	public function getPackageFields($vars=null) {
		Loader::loadHelpers($this, array("Html"));
		
		$fields = new ModuleFields();
		
		$types = array(
			'domain' => Language::_("Namesilo.package_fields.type_domain", true),
		);
		
		// Set type of package
		$type = $fields->label(Language::_("Namesilo.package_fields.type", true), "namesilo_type");
		$type->attach($fields->fieldSelect("meta[type]", $types,
			$this->Html->ifSet($vars->meta['type']), array('id'=>"namesilo_type")));
		$fields->setField($type);	
		
		// Set all TLD checkboxes
        $tld_options = $fields->label(Language::_("Namesilo.package_fields.tld_options", true));
		
		$tlds = Configure::get("Namesilo.tlds");
		sort($tlds);
		foreach ($tlds as $tld) {
			$tld_label = $fields->label($tld, "tld_" . $tld);
			$tld_options->attach($fields->fieldCheckbox("meta[tlds][]", $tld, (isset($vars->meta['tlds']) && in_array($tld, $vars->meta['tlds'])), array('id' => "tld_" . $tld), $tld_label));
		}
		$fields->setField($tld_options);
		
		// Set nameservers
		for ($i=1; $i<=5; $i++) {
			$type = $fields->label(Language::_("Namesilo.package_fields.ns" . $i, true), "namesilo_ns" . $i);
			$type->attach($fields->fieldText("meta[ns][]",
				$this->Html->ifSet($vars->meta['ns'][$i-1]), array('id'=>"namesilo_ns" . $i)));
			$fields->setField($type);
		}	
		
		$fields->setHtml("
			<script type=\"text/javascript\">
				$(document).ready(function() {
					toggleTldOptions($('#namesilo_type').val());
				
					// Re-fetch module options to pull cPanel packages and ACLs
					$('#namesilo_type').change(function() {
						toggleTldOptions($(this).val());
					});
					
					function toggleTldOptions(type) {
						if (type == 'ssl')
							$('.namesilo_tlds').hide();
						else
							$('.namesilo_tlds').show();
					}
				});
			</script>
		");
		
		return $fields;
	}
	
	/**
	 * Returns an array of key values for fields stored for a module, package,
	 * and service under this module, used to substitute those keys with their
	 * actual module, package, or service meta values in related emails.
	 *
	 * @return array A multi-dimensional array of key/value pairs where each key is one of 'module', 'package', or 'service' and each value is a numerically indexed array of key values that match meta fields under that category.
	 * @see Modules::addModuleRow()
	 * @see Modules::editModuleRow()
	 * @see Modules::addPackage()
	 * @see Modules::editPackage()
	 * @see Modules::addService()
	 * @see Modules::editService()
	 */
	public function getEmailTags() {
		return array('service' => array('DomainName'));
	}

	/**
	 * Returns all fields to display to an admin attempting to add a service with the module
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param $vars stdClass A stdClass object representing a set of post fields
	 * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
	 */
	public function getAdminAddFields($package, $vars=null) {
        Loader::loadHelpers($this, array("Form", "Html"));

		// Handle universal domain name
		if (isset($vars->domain))
			$vars->DomainName = $vars->domain;
			
		if ($package->meta->type == "domain") {
			
			// Set default name servers
			if (!isset($vars->ns1) && isset($package->meta->ns)) {
				$i=1;
				foreach ($package->meta->ns as $ns) {
					$vars->{"ns" . $i++} = $ns;
				}
			}
			
			// Handle transfer request
			if (isset($vars->transfer) || isset($vars->EPPCode)) {
				return $this->arrayToModuleFields(Configure::get("Namesilo.transfer_fields"), null, $vars);
			}
			// Handle domain registration
			else {
				
				#
				# TODO: Select TLD, then display additional fields
				#
				
				$module_fields = $this->arrayToModuleFields(array_merge(Configure::get("Namesilo.domain_fields"), Configure::get("Namesilo.nameserver_fields")), null, $vars);
				
                // Build the domain fields
                $fields = $this->buildDomainModuleFields($vars);
                if ($fields)
                    $module_fields = $fields;
			}
		}
        
        return (isset($module_fields) ? $module_fields : new ModuleFields());
	}
	
	/**
	 * Returns all fields to display to a client attempting to add a service with the module
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param $vars stdClass A stdClass object representing a set of post fields
	 * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
	 */	
	public function getClientAddFields($package, $vars=null) {
		
		// Handle universal domain name
		if (isset($vars->domain))
			$vars->DomainName = $vars->domain;
		
		if ($package->meta->type == "domain") {
			
			// Set default name servers
			if (!isset($vars->ns) && isset($package->meta->ns)) {
				$i=1;
				foreach ($package->meta->ns as $ns) {
					$vars->{"ns" . $i++} = $ns;
				}
			}
			
			// Handle transfer request
			if (isset($vars->transfer) || isset($vars->EPPCode)) {
				$fields = Configure::get("Namesilo.transfer_fields");
				
				// We should already have the domain name don't make editable
				$fields['DomainName']['type'] = "hidden";
				$fields['DomainName']['label'] = null;
				
				return $this->arrayToModuleFields($fields, null, $vars);
			}
			// Handle domain registration
			else {
				$fields = array_merge(Configure::get("Namesilo.nameserver_fields"), Configure::get("Namesilo.domain_fields"));
				
				// We should already have the domain name don't make editable
				$fields['DomainName']['type'] = "hidden";
				$fields['DomainName']['label'] = null;
				
				$module_fields = $this->arrayToModuleFields($fields, null, $vars);
				
                // Build the domain fields
                $domain_fields = $this->buildDomainModuleFields($vars, true);
                if ($domain_fields)
                    $module_fields = $domain_fields;
			}
		}

        // Determine whether this is an AJAX request
        return (isset($module_fields) ? $module_fields : new ModuleFields());
	}

    /**
     * Builds and returns the module fields for domain registration
     *
     * @param stdClass $vars An stdClass object representing the input vars
     * @param $client True if rendering the client view, or false for the admin (optional, default false)
     * return mixed The module fields for this service, or false if none could be created
     */
    private function buildDomainModuleFields($vars, $client = false) {
        if (isset($vars->DomainName)) {
            $tld = $this->getTld($vars->DomainName);

            $extension_fields = Configure::get("Namesilo.domain_fields" . $tld);
            if ($extension_fields) {
                // Set the fields
                if ($client)
                    $fields = array_merge(Configure::get("Namesilo.nameserver_fields"), Configure::get("Namesilo.domain_fields"), $extension_fields);
                else
                    $fields = array_merge(Configure::get("Namesilo.domain_fields"), Configure::get("Namesilo.nameserver_fields"), $extension_fields);

                if ($client) {
                    // We should already have the domain name don't make editable
                    $fields['DomainName']['type'] = "hidden";
                    $fields['DomainName']['label'] = null;
                }

                // Build the module fields
                $module_fields = new ModuleFields();

                // Allow AJAX requests
                $ajax = $module_fields->fieldHidden("allow_ajax", "true", array('id'=>"namesilo_allow_ajax"));
                $module_fields->setField($ajax);
                $please_select = array('' => Language::_("AppController.select.please", true));

                foreach ($fields as $key => $field) {
                    // Build the field
                    $label = $module_fields->label((isset($field['label']) ? $field['label'] : ""), $key);

                    $type = null;
                    if ($field['type'] == "text") {
                        $type = $module_fields->fieldText($key, (isset($vars->{$key}) ? $vars->{$key} : ""), array('id'=>$key));
                    }
                    elseif ($field['type'] == "select") {
                        $type = $module_fields->fieldSelect($key, (isset($field['options']) ? $please_select + $field['options'] : $please_select),
                                    (isset($vars->{$key}) ? $vars->{$key} : ""), array('id'=>$key));
                    }
                    elseif ($field['type'] == "hidden") {
                        $type = $module_fields->fieldHidden($key, (isset($vars->{$key}) ? $vars->{$key} : ""), array('id'=>$key));
                    }

                    // Include a tooltip if set
                    if (!empty($field['tooltip']))
                        $label->attach($module_fields->tooltip($field['tooltip']));

                    if ($type) {
                        $label->attach($type);
                        $module_fields->setField($label);
                    }
                }
            }
        }

        return (isset($module_fields) ? $module_fields : false);
    }

	/**
	 * Returns all fields to display to an admin attempting to edit a service with the module
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param $vars stdClass A stdClass object representing a set of post fields
	 * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
	 */	
	public function getAdminEditFields($package, $vars=null) {
		if ($package->meta->type == "domain") {
			return new ModuleFields();
		}
		else {
			return new ModuleFields();
		}
	}
	
	/**
	 * Fetches the HTML content to display when viewing the service info in the
	 * admin interface.
	 *
	 * @param stdClass $service A stdClass object representing the service
	 * @param stdClass $package A stdClass object representing the service's package
	 * @return string HTML content containing information to display when viewing the service info
	 */
	public function getAdminServiceInfo($service, $package) {
		return "";
	}
	
	/**
	 * Fetches the HTML content to display when viewing the service info in the
	 * client interface.
	 *
	 * @param stdClass $service A stdClass object representing the service
	 * @param stdClass $package A stdClass object representing the service's package
	 * @return string HTML content containing information to display when viewing the service info
	 */
	public function getClientServiceInfo($service, $package) {
		return "";
	}
	
	/**
	 * Returns all tabs to display to an admin when managing a service whose
	 * package uses this module
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @return array An array of tabs in the format of method => title. Example: array('methodName' => "Title", 'methodName2' => "Title2")
	 */
	public function getAdminTabs($package) {
		if ($package->meta->type == "domain") {
			return array(
				'tabWhois' => Language::_("Namesilo.tab_whois.title", true),
				'tabNameservers' => Language::_("Namesilo.tab_nameservers.title", true),
				'tabSettings' => Language::_("Namesilo.tab_settings.title", true)
			);
		}
		else {
			#
			# TODO: Activate (NamesiloSsl->active()) & uploads CSR, set field data, etc.
			#
		}
	}

	/**
	 * Returns all tabs to display to a client when managing a service whose
	 * package uses this module
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @return array An array of tabs in the format of method => title. Example: array('methodName' => "Title", 'methodName2' => "Title2")
	 */
	public function getClientTabs($package) {
		if ($package->meta->type == "domain") {
			return array(
				'tabClientWhois' => Language::_("Namesilo.tab_whois.title", true),
				'tabClientNameservers' => Language::_("Namesilo.tab_nameservers.title", true),
				'tabClientSettings' => Language::_("Namesilo.tab_settings.title", true)
			);
		}
		else {
			#
			# TODO: Activate (NamesiloSsl->active()) & uploads CSR, set field data, etc.
			#
		}
	}
	
	/**
	 * Admin Whois tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabWhois($package, $service, array $get=null, array $post=null, array $files=null) {
		return $this->manageWhois("tab_whois", $package, $service, $get, $post, $files);
	}
	
	/**
	 * Client Whois tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabClientWhois($package, $service, array $get=null, array $post=null, array $files=null) {
		return $this->manageWhois("tab_client_whois", $package, $service, $get, $post, $files);
	}
	
	/**
	 * Admin Nameservers tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabNameservers($package, $service, array $get=null, array $post=null, array $files=null) {
		return $this->manageNameservers("tab_nameservers", $package, $service, $get, $post, $files);
	}
	
	/**
	 * Admin Nameservers tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabClientNameservers($package, $service, array $get=null, array $post=null, array $files=null) {
		return $this->manageNameservers("tab_client_nameservers", $package, $service, $get, $post, $files);
	}
	
	/**
	 * Admin Settings tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabSettings($package, $service, array $get=null, array $post=null, array $files=null) {
		return $this->manageSettings("tab_settings", $package, $service, $get, $post, $files);
	}
	
	/**
	 * Client Settings tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabClientSettings($package, $service, array $get=null, array $post=null, array $files=null) {
		return $this->manageSettings("tab_client_settings", $package, $service, $get, $post, $files);
	}
	
	/**
	 * Handle updating whois information
	 *
	 * @param string $view The view to use
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	private function manageWhois($view, $package, $service, array $get=null, array $post=null, array $files=null) {
		$this->view = new View($view, "default");
		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html"));

		$row = $this->getModuleRow($package->module_row);
		$api = $this->getApi($row->meta->user, $row->meta->key, $row->meta->sandbox == "true");
		$domains = new NamesiloDomains($api);
		
		$sections = array( 'registrant', 'admin', 'tech', 'billing' );
		
		$vars = new stdClass();
		
		$whois_fields = Configure::get("Namesilo.whois_fields");
		$fields = $this->serviceFieldsToObject($service->fields);
		
		//self::debug( $whois_fields );
		
		if ( !empty( $post ) ) {
			
			//$post = array_merge( array( 'domain' => $fields->DomainName ), array_intersect_key( $post, $whois_fields ) );
			
			$params = array( "domain" => $fields->DomainName );
			
			foreach ( $post as $key => $value )
			{
				$response = $domains->addContacts( $value );
				$this->processResponse( $api, $response );
				if ( self::$codes[$response->status()][1] != "fail" ) {
					$params[$key] = $response->response()->contact_id;
				}
			}
			
			$response = $domains->setContacts( $params );
			
			//$vars = (object)$post;
		}
		//else {
			
			//$response = $domains->getContacts( array( 'domain' => $fields->DomainName ) );
			
			//if ( self::$codes[$response->status()][1] != "fail" ) {
			
			///*
			$response = $domains->getDomainInfo( array( 'domain' => $fields->DomainName ) );
		
			if ( self::$codes[$response->status()][1] != "fail" ) {
				$contact_ids = $response->response()->contact_ids;
				
				$contacts = $temp = array();
				foreach ( $contact_ids as $type => $id ) {
					if ( !isset( $temp[$id] ) ) {
						$response = $domains->getContacts( array( "contact_id" => $id ) );
						if ( self::$codes[$response->status()][1] != "fail" ) {
							$temp[$id] = $response->response()->contact;
							$contacts[$type] = $temp[$id];
						}
					}
					else {
						$contacts[$type] = $temp[$id];
					}
				}
				//*/
				
				// Format fields
				foreach ( $contacts as $section => $element ) {
					//self::debug( $element );
					foreach ( $element as $name => $value ) {
						// Value must be a string
						if ( !is_scalar( $value ) )
							$value = "";
						if ( isset( $whois_fields[$name]['key'] ) )
							$vars->{$section . '[' . $whois_fields[$name]['key'] . ']'} = $value;
					}
				}
			}
		//}
		
		$all_fields = array();
		foreach ( $whois_fields as $field => $value ) {
			$key = $value['key'];
			$all_fields["administrative[{$key}]"] = $value;
			$all_fields["technical[{$key}]"] = $value;
			$all_fields["registrant[{$key}]"] = $value;
			$all_fields["billing[{$key}]"] = $value;
		}
		
		//self::debug( $vars );
		
		$this->view->set("vars", $vars);
		$this->view->set("fields", $this->arrayToModuleFields($all_fields, null, $vars)->getFields());
		$this->view->set("sections", $sections );
		$this->view->setDefaultView("components" . DS . "modules" . DS . "namesilo" . DS);
		return $this->view->fetch();
	}
	
	private function whoisContacts( array $vars ) {
		
		$response = $domains->getDomainInfo( array( 'domain' => $fields->DomainName ) );
		
		if ( self::$codes[$response->status()][1] != "fail" ) {
			
			$contact_ids = $response->response()->contact_ids;
			
			$contacts = $temp = array();
			foreach ( $contact_ids as $type => $id ) {
				if ( !isset( $temp[$id] ) ) {
					$response = $domains->getContacts( array( "contact_id" => $id ) );
					if ( self::$codes[$response->status()][1] != "fail" ) {
						$temp[$id] = $response->response()->contact;
						$contacts[$type] = $temp[$id];
					}
				}
				else {
					$contacts[$type] = $temp[$id];
				}
			}
		}
	}
	
	/**
	 * Handle updating nameserver information
	 *
	 */
	private function manageNameservers($view, $package, $service, array $get=null, array $post=null, array $files=null) {
		$this->view = new View($view, "default");
		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html"));
		
		$vars = new stdClass();
		
		$row = $this->getModuleRow($package->module_row);
		$api = $this->getApi($row->meta->user, $row->meta->key, $row->meta->sandbox == "true");
		$dns = new NamesiloDomainsDns($api);
		
		$fields = $this->serviceFieldsToObject($service->fields);
		
		$tld = $this->getTld($fields->DomainName);
		$sld = substr($fields->DomainName, 0, -strlen($tld));
		
		if (!empty($post)) {
			$args = array(); $i = 1;
			foreach( $post['ns'] as $ns ) {
				$args["ns{$i}"] = $ns;
				$i++;
			}
			
			$args['domain'] = $fields->DomainName;
			
			$response = $dns->setCustom( $args );
			$this->processResponse($api, $response);
			
			$vars = (object)$post;
		}
		else {
			$response = $dns->getList( array( 'domain' => $fields->DomainName ) )->response();
			
			if (isset($response->nameservers)) {
				$vars->ns = array();
				foreach ($response->nameservers->nameserver as $ns) {
					$vars->ns[] = $ns;
				}
			}
		}
		
		$this->view->set("vars", $vars);
		$this->view->setDefaultView("components" . DS . "modules" . DS . "namesilo" . DS);
		return $this->view->fetch();
	}
	
	/**
	 * Handle updating settings
	 *
	 * @param string $view The view to use
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	private function manageSettings($view, $package, $service, array $get=null, array $post=null, array $files=null) {
		$this->view = new View($view, "default");
		// Load the helpers required for this view
		Loader::loadHelpers($this, array("Form", "Html"));
		
		$vars = new stdClass();
		
		$row = $this->getModuleRow($package->module_row);
		$api = $this->getApi($row->meta->user, $row->meta->key, $row->meta->sandbox == "true");
		$domains = new NamesiloDomains($api);
		$transfer = new NamesiloDomainsTransfer($api);
		
		$fields = $this->serviceFieldsToObject($service->fields);
		
		if ( !empty( $post ) ) {
			if ( isset( $post['registrar_lock'] ) ) {
				$LockAction = $post['registrar_lock'] == "Yes" ? "Lock" : "Unlock";
				$response = $domains->setRegistrarLock( $LockAction, array( 'domain' => $fields->DomainName ) );
				$this->processResponse( $api, $response );
			}
			
			if ( isset( $post['request_epp'] ) ) {
				$response = $transfer->getEpp( array( 'domain' => $fields->DomainName ) );
				$this->processResponse( $api, $response );
			}
			
			$vars = (object)$post;
		}
		else {
			$response = $domains->getRegistrarLock(array('domain' => $fields->DomainName))->response();
			if (isset($response->locked))
				$vars->registrar_lock = $response->locked;
		}
		
		$this->view->set("vars", $vars);
		$this->view->setDefaultView("components" . DS . "modules" . DS . "namesilo" . DS);
		return $this->view->fetch();
	}
	
	/**
	 * Performs a whois lookup on the given domain
	 *
	 * @param string $domain The domain to lookup
	 * @return boolean true if available, false otherwise
	 */
	public function checkAvailability($domain) {

		$row = $this->getModuleRow();
		$api = $this->getApi($row->meta->user, $row->meta->key, $row->meta->sandbox == "true");
		
		$domains = new NamesiloDomains($api);
		$result = $domains->check(array('domains' => $domain));
		
		if ( self::$codes[$result->status()][1] == "fail" )
			return false;
		
		$response = $result->response();
		
		$available = isset( $response->available->{'domain'} ) && $response->available->{'domain'} == $domain;
		return $available;
	}
	
	/**
	 * Builds and returns the rules required to add/edit a module row
	 *
	 * @param array $vars An array of key/value data pairs
	 * @return array An array of Input rules suitable for Input::setRules()
	 */
	private function getRowRules(&$vars) {
		return array(
			'user' => array(
				'valid' => array(
					'rule' => "isEmpty",
					'negate' => true,
					'message' => Language::_("Namesilo.!error.user.valid", true)
				)
			),
			'key' => array(
				'valid' => array(
					'last' => true,
					'rule' => "isEmpty",
					'negate' => true,
					'message' => Language::_("Namesilo.!error.key.valid", true)
				),
				'valid_connection' => array(
					'rule' => array(array($this, "validateConnection"), $vars['user'], isset($vars['sandbox']) ? $vars['sandbox'] : "false"),
					'message' => Language::_("Namesilo.!error.key.valid_connection", true)
				)
			)
		);
	}
	
	/**
	 * Validates that the given connection details are correct by attempting to check the availability of a domain
	 *
	 * @param string $key The API key
	 * @param string $user The API user
	 * @param string $sandbox "true" if this is a sandbox account, false otherwise
	 * @return boolean True if the connection details are valid, false otherwise
	 */
	public function validateConnection($key, $user, $sandbox) {
		$api = $this->getApi($user, $key, $sandbox == "true");
		$domains = new NamesiloDomains($api);
		$status = $domains->check(array('domains' => "example.com"))->status();
		return self::$codes[$status][1] == 'success';
	}
	
	/**
	 * Initializes the NamesiloApi and returns an instance of that object
	 *
	 * @param string $user The user to connect as
	 * @param string $key The key to use when connecting
	 * @param boolean $sandbox Whether or not to process in sandbox mode (for testing)
	 * @param string $username The username to execute an API command using
	 * @return NamesiloApi The NamesiloApi instance
	 */
	private function getApi($user, $key, $sandbox, $username = null) {
		Loader::load(dirname(__FILE__) . DS . "apis" . DS . "namesilo_api.php");
		
		return new NamesiloApi($user, $key, $sandbox, $username);
	}
	
	/**
	 * Process API response, setting an errors, and logging the request
	 *
	 * @param NamesiloApi $api The Namesilo API object
	 * @param NamesiloResponse $response The Namesilo API response object
	 */
	private function processResponse( NamesiloApi $api, NamesiloResponse $response ) {
		$this->logRequest( $api, $response );
		
		$status = $response->status();
		
		// Set errors, if any
		if ( self::$codes[$status][1] == "fail" ) {
			//$errors = isset( $response->errors()->Error ) ? $response->errors()->Error : array();
			$errors = $response->errors() ? $response->errors() : array();
			$this->Input->setErrors( array( 'errors' => (array)$errors ) );
		}
	}
	
	/**
	 * Logs the API request
	 *
	 * @param NamesiloApi $api The Namesilo API object
	 * @param NamesiloResponse $response The Namesilo API response object
	 */
	private function logRequest(NamesiloApi $api, NamesiloResponse $response) {		
		$last_request = $api->lastRequest();
		$url = substr( $last_request['url'], 0, strpos( $last_request['url'], '?' ) );
		$this->log( $url, serialize( $last_request['args'] ), "input", true );
		$this->log( $url, $response->raw(), "output", self::$codes[$response->status()][1] == "success" );
	}
	
	/**
	 * Returns the TLD of the given domain
	 *
	 * @param string $domain The domain to return the TLD from
	 * @return string The TLD of the domain
	 */
	private function getTld($domain) {
		$tlds = Configure::get("Namesilo.tlds");
		
		$domain = strtolower($domain);
		
		foreach ($tlds as $tld) {
			if (substr($domain, -strlen($tld)) == $tld)
				return $tld;
		}
		return strstr($domain, ".");
	}
	
	/**
	 * Formats a phone number into +NNN.NNNNNNNNNN
	 *
	 * @param string $number The phone number
	 * @param string $country The ISO 3166-1 alpha2 country code
	 * @return string The number in +NNN.NNNNNNNNNN
	 */
	private function formatPhone($number, $country) {
		if (!isset($this->Contacts))
			Loader::loadModels($this, array("Contacts"));
		
		return $this->Contacts->intlNumber($number, $country, ".");
	}
	
	private function debug( $data ) {
		mail( self::$debug_to, "Namesilo Module " . self::$version . " Debug", var_export( $data, true ), "From: blesta@localhost\n\n" );
	}
	
}
?>