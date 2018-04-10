<?php
/**
 * Namesilo Module
 *
 * @package blesta
 * @subpackage blesta.components.modules.namesilo
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @link http://www.blesta.com/ Blesta
 * @copyright Copyright (c) 2015-2018, NETLINK IT SERVICES
 * @link http://www.netlink.ie/ NETLINK
 */
class Namesilo extends Module {
	
	private static $debug_to = "root@localhost";
	
	// Namesilo response codes (array)
	private static $codes;
	
	// Pending statutes (array)
	private static $pending = array( 'in_review', 'pending' );
	
	private static $defaultModuleView;
	
	private static $api;
	
	/**
	 * Initializes the module
	 */
	public function __construct() {
		# Load config.json
		$this->loadConfig( __DIR__ . DS . "config.json" );
		# Load components required by this module
		Loader::loadComponents( $this, array ( "Input" ) );
		# Load the language required by this module
		Language::loadLang( "namesilo", null, __DIR__ . DS . "language" . DS );
		# Load configuration
		Configure::load( "namesilo", __DIR__ . DS . "config" . DS );
		# Get Namesilo response codes
		self::$codes = Configure::get( 'Namesilo.status.codes' );
		# Set default module view
		self::$defaultModuleView = "components" . DS . "modules" . DS . "namesilo" . DS;
	}

	/**
	 * Returns the name of this module
	 *
	 * @return string The common name of this module
	 */
	public function getName() {
		return Language::_( "Namesilo.name", true );
	}
	
	/**
	 * Returns the value used to identify a particular service
	 *
	 * @param stdClass $service A stdClass object representing the service
	 * @return string A value used to identify this service amongst other similar services
	 */
	public function getServiceName( $service ) {
		foreach ( $service->fields as $field ) {
			if ( $field->key == "domain" ) {
				return $field->value;
			}
		}
		return null;
	}
	
	/**
	 * Returns a noun used to refer to a module row (e.g. "Server", "VPS", "Reseller Account", etc.)
	 *
	 * @return string The noun used to refer to a module row
	 */
	public function moduleRowName() {
		return Language::_( "Namesilo.module_row", true );
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
		if (isset($vars['domain']))
			return $vars['domain'];
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
        $rules = array();

        // transfers (epp code)
	    if(isset($vars['transfer']) && $vars['transfer']) {
            $rule = [
                'auth' => [
                    'empty' => array(
                        'rule' => array("isEmpty"),
                        'negate' => true,
                        'message' => Language::_("Namesilo.!error.epp.empty", true),
                        'post_format' => "trim"
                    )
                ],
            ];
            $rules = array_merge($rules,$rule);
        }

        // .us fields
        if(isset($vars['usnc']) || isset($vars['usap'])){
            $rule = [
                'usnc' => [
                    'empty' => array(
                        'rule' => array("isEmpty"),
                        'negate' => true,
                        'message' => Language::_("Namesilo.!error.US.RegistrantNexus.empty", true),
                        'post_format' => "trim",
                        'final' => true
                    ),
                    'valid' => [
                        'rule' => ['array_key_exists', Configure::get("Namesilo.domain_fields.us")['usnc']['options']],
                        'message' => Language::_("Namesilo.!error.US.RegistrantNexus.invalid", true)
                    ]
                ],
                'usap' => [
                    'empty' => array(
                        'rule' => array("isEmpty"),
                        'negate' => true,
                        'message' => Language::_("Namesilo.!error.US.RegistrantPurpose.empty", true),
                        'post_format' => "trim",
                        'final' => true
                    ),
                    'valid' => [
                        'rule' => ['array_key_exists', Configure::get("Namesilo.domain_fields.us")['usap']['options']],
                        'message' => Language::_("Namesilo.!error.US.RegistrantPurpose.invalid", true)
                    ]
                ],
            ];
            $rules = array_merge($rules,$rule);
        }

        // .ca fields
        if(isset($vars['calf']) || isset($vars['cawd']) || isset($vars['caln'])){
            $rule = [
                'calf' => [
                    'empty' => array(
                        'rule' => array("isEmpty"),
                        'negate' => true,
                        'message' => Language::_("Namesilo.!error.CA.CIRALegalType.empty", true),
                        'post_format' => "trim",
                        'final' => true
                    ),
                    'valid' => [
                        'rule' => ['array_key_exists', Configure::get("Namesilo.domain_fields.ca")['calf']['options']],
                        'message' => Language::_("Namesilo.!error.CA.CIRALegalType.invalid", true)
                    ],
                    'other' => [
                        'rule' => ['matches', '/^OTHER$/'],
                        'negate' => true,
                        'message' => Language::_("Namesilo.!error.CA.CIRALegalType.other", true)
                    ]
                ],
                'cawd' => [
                    'empty' => array(
                        'rule' => array("isEmpty"),
                        'negate' => true,
                        'message' => Language::_("Namesilo.!error.CA.CIRAWhoisDisplay.empty", true),
                        'post_format' => "trim",
                        'final' => true
                    ),
                    'valid' => [
                        'rule' => ['array_key_exists', Configure::get("Namesilo.domain_fields.ca")['cawd']['options']],
                        'message' => Language::_("Namesilo.!error.CA.CIRAWhoisDisplay.invalid", true)
                        ]
                ],
                'caln' => [
                    'empty' => array(
                        'rule' => array("isEmpty"),
                        'negate' => true,
                        'message' => Language::_("Namesilo.!error.CA.CIRALanguage.empty", true),
                        'post_format' => "trim",
                        'final' => true
                    ),
                    'valid' => [
                        'rule' => ['array_key_exists', Configure::get("Namesilo.domain_fields.ca")['caln']['options']],
                        'message' => Language::_("Namesilo.!error.CA.CIRALanguage.invalid", true)
                        ]
                ],
            ];
            $rules = array_merge($rules,$rule);
        }

        if(isset($rules) && count($rules) > 0){
            $this->Input->setRules($rules);
            return $this->Input->validates($vars);
        }
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
	public function addService( $package, array $vars = null, $parent_package = null, $parent_service = null, $status = "pending" ) {
		
		$row = $this->getModuleRow( $package->module_row );
		$api = $this->getApi( $row->meta->user, $row->meta->key, $row->meta->sandbox == "true" );
		
		#
		# TODO: Handle validation checks
		# TODO: Fix nameservers
		#

        if ( isset( $vars['domain'] ) ) {
            $tld = $this->getTld($vars['domain'],$row);
        }

        $input_fields = array_merge(
            Configure::get( "Namesilo.domain_fields" ),
            (array) Configure::get("Namesilo.domain_fields" . $tld ),
            (array) Configure::get("Namesilo.nameserver_fields"),
            (array) Configure::get("Namesilo.transfer_fields"),
            array( 'years' => true, 'transfer' => isset($vars['transfer']) ? $vars['transfer'] : 0 )
        );

        // .ca domains can't have traditional whois privacy
        if($tld == '.ca')
            unset($input_fields['private']);

		
		if ( isset( $vars['use_module'] ) && $vars['use_module'] == "true" ) {
			
			if ( $package->meta->type == "domain" ) {

				$vars['years'] = 1;
				
				foreach ( $package->pricing as $pricing ) {
					if ( $pricing->id == $vars['pricing_id'] ) {
						$vars['years'] = $pricing->term;
						break;
					}
				}

                $whois_fields = Configure::get("Namesilo.whois_fields");

                // Set all whois info from client ($vars['client_id'])
                if ( !isset( $this->Clients ) ) {
                    Loader::loadModels( $this, array( "Clients" ) );
                }
                if ( !isset( $this->Contacts ) ) {
                    Loader::loadModels( $this, array( "Contacts" ) );
                }

                $client = $this->Clients->get( $vars['client_id'] );

                if ( $client )
                    $contact_numbers = $this->Contacts->getNumbers( $client->contact_id );

                foreach ( $whois_fields as $key => $value ) {
                    $input_fields[$value['rp']] = true;
                    if ( strpos( $key, "phone" ) !== false ) {
                        $vars[$value['rp']] = $this->formatPhone( isset( $contact_numbers[0] ) ? $contact_numbers[0]->number : null, $client->country );
                    }
                    else {
                        $vars[$value['rp']] = ( isset( $value['lp'] ) && !empty( $value['lp'] ) ) ? $client->{$value['lp']} : 'NA';
                    }
                }

                $fields = array_intersect_key( $vars, $input_fields );

                if(!empty($row->meta->portfolio))
                    $fields['portfolio'] = $row->meta->portfolio;
                if(!empty($row->meta->payment_id))
                    $fields['payment_id'] = $row->meta->payment_id;

                // for .ca domains we need to create a special contact to use
                if($tld == '.ca'){
                    $domains = new NamesiloDomains( $api );
                    $response = $domains->addContacts($vars);
                    $this->processResponse($api,$response);
                    if ($this->Input->errors())
                        return;
                    $fields['contact_id'] = $response->response()->contact_id;

                    $this->debug($fields);
                }

				// Handle transfer
				if ( isset($vars['auth']) && $vars['auth'] ) {

					$transfer = new NamesiloDomainsTransfer( $api );

					$response = $transfer->create( $fields );
					$this->processResponse( $api, $response );
					
					if ($this->Input->errors()) {
					    if(isset($vars['contact_id']))
					        $domains->deleteContacts(array('contact_id'=>$vars['contact_id']));
                        return;
                    }
				}else{
				    // Handle registration
					$domains = new NamesiloDomains( $api );

					//$this->debug( $fields );
					//$this->Input->setErrors( array( 'errors' => array( 'Test' ) ) );
					//return;

					$response = $domains->create( $fields );
					$this->processResponse( $api, $response );
					
					if ($this->Input->errors()) {
                        if(isset($vars['contact_id']))
                            $domains->deleteContacts(array('contact_id'=>$vars['contact_id']));
                        return;
                    }
				}
			}
		}

        $meta = [];
        $fields = array_intersect_key($vars, $input_fields);
        foreach ($fields as $key => $value) {
            $meta[] = [
                'key' => $key,
                'value' => $value,
                'encrypted' => 0
            ];
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
	public function editService( $package, $service, array $vars = array(), $parent_package = null, $parent_service = null ) {
		$renew = isset( $vars["renew"] ) ? (int) $vars["renew"] : 0;
		if ( $renew > 0 && $vars["use_module"] == 'true' ) {
			$this->renewService( $package, $service, $parent_package, $parent_service, $renew );
			unset( $vars['renew'] );
		}
		return null; // All this handled by admin/client tabs instead
	}
	
	/**
	 * Cancels the service on the remote server. Sets Input errors on failure,
	 * preventing the service from being canceled.
	 */
	public function cancelService( $package, $service, $parent_package = null, $parent_service = null ) {
		
		$row = $this->getModuleRow( $package->module_row );
		$api = $this->getApi( $row->meta->user, $row->meta->key, $row->meta->sandbox == "true" );

		if ( $package->meta->type == "domain" ) {
			
			$fields = $this->serviceFieldsToObject( $service->fields );
			
			$domains = new NamesiloDomains( $api );
			$response = $domains->setAutoRenewal( $fields->{"domain"}, false );
			$this->processResponse( $api, $response );
			
			if ($this->Input->errors())
				return;
			
		}
		return;
	}
	
	/**
	 * Suspends the service on the remote server. Sets Input errors on failure,
	 * preventing the service from being suspended.
	 */
	public function suspendService( $package, $service, $parent_package = null, $parent_service = null ) {
		
		$row = $this->getModuleRow( $package->module_row );
		$api = $this->getApi( $row->meta->user, $row->meta->key, $row->meta->sandbox == "true" );

		if ( $package->meta->type == "domain" ) {
			
			$fields = $this->serviceFieldsToObject( $service->fields );
			
			# Make sure auto renew is off
			$domains = new NamesiloDomains( $api );
			$response = $domains->setAutoRenewal( $fields->{"domain"}, false );
			$this->processResponse( $api, $response );
			
			if ( $this->Input->errors() ) {
				return;
			}
			
		}
		return;
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
	public function renewService( $package, $service, $parent_package = NULL, $parent_service = NULL, $years = NULL ) {
		
		$row = $this->getModuleRow( $package->module_row );
		$api = $this->getApi( $row->meta->user, $row->meta->key, $row->meta->sandbox == "true" );

		// Renew domain renewDomain?version=1&type=xml&key=12345&domain=namesilo.com&years=2
		if ( $package->meta->type == "domain" ) {
			
			$fields = $this->serviceFieldsToObject( $service->fields );

			$vars = array(
				"domain" => $fields->{"domain"},
				"years" => 1
			);
			
			if ( !$years )
			{
				foreach ( $package->pricing as $pricing ) {
					if ( $pricing->id == $service->pricing_id ) {
						$vars['years'] = $pricing->term;
						break;
					}
				}
			}
			else {
				$vars["years"] = $years;
			}
			
			$domains = new NamesiloDomains( $api );
			$response = $domains->renew( $vars );
			$this->processResponse( $api, $response );

			if ($this->Input->errors())
				return;
		}

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
	public function manageModule( $module, array &$vars ) {
		// Load the view into this object, so helpers can be automatically added to the view
		$this->view = new View( "manage", "default" );
		$this->view->base_uri = $this->base_uri;
		$this->view->setDefaultView( self::$defaultModuleView );
		
		#
		#
		# TODO: add tab to check status of all transfers: check if possible with Namesilo... ref: NamesiloDomainsTransfer->getList()
		#
		#

        foreach($module->rows as $row){
            if(isset($row->meta->key)){
                $link_buttons[] = array('name'=>Language::_("Namesilo.manage.sync_renew_dates",true),'attributes'=>array('href'=>array('href'=>$this->base_uri . "settings/company/modules/addrow/" . $module->id."?action=sync_renew_dates")));
                $link_buttons[] = array('name'=>Language::_("Namesilo.manage.audit_domains", true), 'attributes'=>array('href'=>$this->base_uri . "settings/company/modules/addrow/" . $module->id."?action=audit_domains"));
                break;
            }
        }

        $this->view->set("module", $module);
        $this->view->set("link_buttons",$link_buttons);

		// Load the helpers required for this view
		Loader::loadHelpers( $this, array ( "Form", "Html", "Widget" ) );

		$this->view->set( "module", $module );
		
		return $this->view->fetch();
	}
	
	/**
	 * Returns the rendered view of the add module row page
	 *
	 * @param array $vars An array of post data submitted to or on the add module row page (used to repopulate fields after an error)
	 * @return string HTML content containing information to display when viewing the add module row page
	 */
	public function manageAddRow(array &$vars) {
		$action = isset($_GET['action']) ? $_GET['action'] : null;

		if($action == 'audit_domains') {
		    $vars = [];
            $this->view = new View("audit_domains", "default");
            $this->view->base_uri = $this->base_uri;
            $this->view->setDefaultView( self::$defaultModuleView );

            // Load the helpers required for this view
            Loader::loadHelpers($this, array ("Form", "Html", "Widget"));
            Loader::loadModels($this,array("Services","Record","ModuleManager"));


            $module_row = $this->getNamesiloRow();

            $api = $this->getApi($module_row->meta->user, $module_row->meta->key, $module_row->meta->sandbox == "true", null, true);
            $domains = new NamesiloDomains($api);

            if($module_row->meta->portfolio)
                $vars['portfolio'] = $module_row->meta->portfolio;

            $domain_list = $domains->getList($vars)->response()->domains->domain;

            $vars['domains'] = [];
            foreach($domain_list as $domain){
                $record = $this->Record->select("*")->from("services")
                    ->leftJoin("service_fields","services.id","=","service_fields.service_id", false)
                    ->where("services.status","IN",array("active","suspended"))
                    ->where("service_fields.value","=",$domain)
                    ->where("services.module_row_id","=",$module_row->id)
                    ->where("service_fields.key","=",'domain')
                    ->numResults();
                if(!$record){
                    $vars['domains'][] = $domain;
                }
            }

            $this->view->set( "vars", (object)$vars );

            return $this->view->fetch();
        }elseif($action == 'sync_renew_dates'){
            if(isset($_POST['sync_services']))
                $post['sync_services'] = $_POST['sync_services'];

		    $this->view = new View("sync_renew_dates", "default");
            $this->view->base_uri = $this->base_uri;
            $this->view->setDefaultView( self::$defaultModuleView );

            // Load the helpers required for this view
            Loader::loadHelpers($this, array ("Form", "Html", "Widget"));
            Loader::loadModels($this,array("Services","Clients","Record","ModuleManager","ClientGroups"));

            $module_row = $this->getNamesiloRow();

            $api = $this->getApi($module_row->meta->user, $module_row->meta->key, $module_row->meta->sandbox == "true", null, true);
            $domains = new NamesiloDomains($api);

            $services = $this->Record->select(array("services.id","services.client_id"))
                ->from("services")
                ->where('module_row_id','=',$module_row->id)
                ->where('status','=','active')
                ->fetchAll();

            $vars['changes'] = array();

            foreach($services as $service_id){
                $vars['changes'][] = $this->getRenewInfo($service_id->id,$domains);
            }

            $this->view->set( "vars", $vars );
            return $this->view->fetch();
        }else{
            // Load the view into this object, so helpers can be automatically added to the view
            $this->view = new View("add_row", "default");
            $this->view->base_uri = $this->base_uri;
            $this->view->setDefaultView( self::$defaultModuleView );

            // Load the helpers required for this view
            Loader::loadHelpers( $this, array ( "Form", "Html", "Widget" ) );

            // Set unspecified checkboxes
            if (!empty($vars)) {
                if (empty($vars['sandbox']))
                    $vars['sandbox'] = "false";
            }

            $this->view->set("vars", (object)$vars);
            return $this->view->fetch();
        }
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
		$this->view->setDefaultView( self::$defaultModuleView );
		
		// Load the helpers required for this view
		Loader::loadHelpers( $this, array ( "Form", "Html", "Widget" ) );
		
		if (empty($vars))
			$vars = $module_row->meta;
		else {
			// Set unspecified checkboxes
			if (empty($vars['sandbox']))
				$vars['sandbox'] = "false";
		}
		
		$this->view->set( "vars", (object)$vars );
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
        if(isset($_POST['sync_services'])){
            Loader::loadModels($this,array("ModuleManager","Services","Clients","ClientGroups"));

            $module_row = $this->getNamesiloRow();

            foreach($_POST['sync_services'] as $service_id) {
                $api = $this->getApi($module_row->meta->user, $module_row->meta->key, $module_row->meta->sandbox == "true", null, true);
                $domains = new NamesiloDomains($api);

                $info = $this->getRenewInfo($service_id, $domains);
                $this->Services->edit($service_id, array('date_renews' => $info['date_after']), true);
            }
            $url = explode("?",$_SERVER['REQUEST_URI']);
            header('Location:' . $url[0].'?action=sync_renew_dates&msg=success');
            exit();
        }
	    $meta_fields = array("user", "key", "sandbox", "portfolio", "payment_id", "namesilo_module");
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

        // Fetch all packages available for the given server or server group
        $module_row = null;
        if (isset($vars->module_group) && $vars->module_group == '') {
            if (isset($vars->module_row) && $vars->module_row > 0) {
                $module_row = $this->getModuleRow($vars->module_row);
            } else {
                $rows = $this->getModuleRows();
                if (isset($rows[0])) {
                    $module_row = $rows[0];
                }
                unset($rows);
            }
        } else {
            // Fetch the 1st server from the list of servers in the selected group
            $rows = $this->getModuleRows($vars->module_group);
            if (isset($rows[0])) {
                $module_row = $rows[0];
            }
            unset($rows);
        }

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
		
		$tlds = $this->getTlds($module_row);
		sort( $tlds );
		foreach ( $tlds as $tld ) {
			$tld_label = $fields->label( $tld, "tld_" . $tld );
			$tld_options->attach( $fields->fieldCheckbox( "meta[tlds][]", $tld, ( isset( $vars->meta['tlds'] ) && in_array( $tld, $vars->meta['tlds'] ) ), array( 'id' => "tld_" . $tld ), $tld_label ) );
		}
		$fields->setField( $tld_options );

		// Set nameservers
		for ( $i=1; $i<=5; $i++ ) {
			$type = $fields->label( Language::_( "Namesilo.package_fields.ns" . $i, true ), "namesilo_ns" . $i );
			$type->attach( $fields->fieldText( "meta[ns][]",
				$this->Html->ifSet( $vars->meta['ns'][$i-1] ), array ( 'id' => "namesilo_ns" . $i ) ) );
			$fields->setField( $type );
		}

		$fields->setHtml("
			<script type=\"text/javascript\">
				$(document).ready(function() {
					toggleTldOptions($('#namesilo_type').val());
				
					// Re-fetch module options
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
		return array( 'service' => array ( 'domain' ) );
	}

	/**
	 * Returns all fields to display to an admin attempting to add a service with the module
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @param $vars stdClass A stdClass object representing a set of post fields
	 * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
	 */
	public function getAdminAddFields( $package, $vars = null ) {

		Loader::loadHelpers( $this, array( "Form", "Html" ) );

		if ( $package->meta->type == "domain" ) {

			// Set default name servers
			if ( !isset( $vars->ns1 ) && isset( $package->meta->ns ) ) {
				$i = 1;
				foreach ( $package->meta->ns as $ns ) {
					$vars->{"ns" . $i++} = $ns;
				}
			}

			// Handle transfer request
			if ( isset( $vars->transfer ) || isset( $vars->auth ) ) {
				return $this->arrayToModuleFields( Configure::get( "Namesilo.transfer_fields" ), null, $vars );
			}
			// Handle domain registration
			else {

				#
				# TODO: Select TLD, then display additional fields
				#

				$fields = Configure::get( "Namesilo.transfer_fields" );

				$fields["transfer"] = array(
					'label' => Language::_( "Namesilo.domain.DomainAction", true ),
					'type' => "radio",
					'value' => "1",
					'options' => array(
						'1' => "Register",
						'2' => "Transfer",
					),
				);

				$fields["auth"] = array(
					"label" => Language::_( "Namesilo.transfer.EPPCode", true ),
					"type" => "text",
				);

				$module_fields = $this->arrayToModuleFields( array_merge( $fields, Configure::get( "Namesilo.nameserver_fields" ) ), null, $vars );

				// $module_fields = $this->arrayToModuleFields(array_merge(Configure::get("Namesilo.domain_fields"), Configure::get("Namesilo.nameserver_fields")), null, $vars);

				$module_fields->setHtml("
					<script type=\"text/javascript\">
						$(document).ready(function() {
							$('#transfer_id_0').prop('checked', true);
							$('#auth_id').closest('li').hide();
							// Set whether to show or hide the ACL option
							$('#auth').closest('li').hide();
							if ($('input[name=\"transfer\"]:checked').val() == '2')
								$('#auth_id').closest('li').show();
								
							$('input[name=\"transfer\"]').change(function() {
								if ($(this).val() == '2')
									$('#auth_id').closest('li').show();
								else
									$('#auth_id').closest('li').hide();
							});
						});
					</script>");

                // Build the domain fields
                $fields = $this->buildDomainModuleFields( $vars );
                if ( $fields )
                    $module_fields = $fields;
			}
		}

		return ( isset( $module_fields ) ? $module_fields : new ModuleFields() );
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
			$vars->domain = $vars->domain;

		if ($package->meta->type == "domain") {

			// Set default name servers
			if (!isset($vars->ns) && isset($package->meta->ns)) {
				$i=1;
				foreach ($package->meta->ns as $ns) {
					$vars->{"ns" . $i++} = $ns;
				}
			}

            if ( isset( $vars->domain ) ) {
                $tld = $this->getTld($vars->domain);
            }

			// Handle transfer request
			if (isset($vars->transfer) || isset($vars->auth)) {
				$fields = array_merge(
				    Configure::get("Namesilo.transfer_fields"),
                    (array) Configure::get("Namesilo.domain_fields" . $tld)
                );

                // .ca domains can't have traditional whois privacy
                if($tld == '.ca')
                    unset($fields['private']);

				// We should already have the domain name don't make editable
				$fields['domain']['type'] = "hidden";
				$fields['domain']['label'] = null;
				// we already know we're doing a transfer, don't make it editable
				$fields['transfer']['type'] = "hidden";
				$fields['transfer']['label'] = null;

				$module_fields = $this->arrayToModuleFields($fields, null, $vars);

                return $module_fields;
			}
			// Handle domain registration
			else {
				$fields = array_merge(
				    Configure::get("Namesilo.nameserver_fields"),
                    Configure::get("Namesilo.domain_fields"),
                    (array) Configure::get("Namesilo.domain_fields" . $tld)
                );

                // .ca domains can't have traditional whois privacy
                if($tld == '.ca')
                    unset($fields['private']);

				// We should already have the domain name don't make editable
				$fields['domain']['type'] = "hidden";
				$fields['domain']['label'] = null;

				$module_fields = $this->arrayToModuleFields( $fields, null, $vars );
			}
		}

        // Determine whether this is an AJAX request
        return ( isset ( $module_fields ) ? $module_fields : new ModuleFields() );
	}

    /**
     * Builds and returns the module fields for domain registration
     *
     * @param stdClass $vars An stdClass object representing the input vars
     * @param $client True if rendering the client view, or false for the admin (optional, default false)
     * return mixed The module fields for this service, or false if none could be created
     */
    private function buildDomainModuleFields($vars, $client = false) {
        if (isset($vars->domain)) {
            $tld = $this->getTld($vars->domain);

            $extension_fields = Configure::get("Namesilo.domain_fields" . $tld);
            if ($extension_fields) {
                // Set the fields
                if ($client)
                    $fields = array_merge(Configure::get("Namesilo.domain_fields"), $extension_fields);
                else
                    $fields = array_merge(Configure::get("Namesilo.domain_fields"), $extension_fields);

                if(!isset($vars->transfer))
                    $fields = array_merge($fields,Configure::get("Namesilo.nameserver_fields"));
                else
                    $fields = array_merge($fields,Configure::get("Namesilo.transfer_fields"));

                if ($client) {
                    // We should already have the domain name don't make editable
                    $fields['domain']['type'] = "hidden";
                    $fields['domain']['label'] = null;
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
                    elseif($field['type'] == "checkbox"){
                        $type = $module_fields->fieldCheckbox($key, (isset($field['options']) ? $field['options']: 1));
                        $label = $module_fields->label((isset($field['label']) ? $field['label'] : ""), $key);
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
	public function getAdminEditFields( $package, $vars = NULL ) {

		Loader::loadHelpers( $this, array( "Html" ) );

		$fields = new ModuleFields();

		// Create domain label
		//$domain = $fields->label( Language::_( "Cpanel.service_field.domain", true ), "cpanel_domain" );
		$domain = $fields->label( Language::_( "Namesilo.manage.manual_renewal", true ), "renew" );
		// Create domain field and attach to domain label
		$domain->attach( $fields->fieldSelect( "renew", array( 0, "1 year", "2 years", "3 years", "4 years", "5 years" ), $this->Html->ifSet( $vars->renew ), array( 'id'=>"renew" ) ) );
		// Set the label as a field
		$fields->setField( $domain );

		return $fields;
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
	public function getAdminTabs( $package ) {
		if ( $package->meta->type == "domain" ) {
			return array(
				'tabWhois' => Language::_( "Namesilo.tab_whois.title", true ),
				'tabNameservers' => Language::_( "Namesilo.tab_nameservers.title", true ),
                'tabHosts' => Language::_( "Namesilo.tab_hosts.title", true ),
				'tabSettings' => Language::_( "Namesilo.tab_settings.title", true ),
				'tabAdminActions' => Language::_( "Namesilo.tab_adminactions.title", true ),
			);
		}
	}

	/**
	 * Returns all tabs to display to a client when managing a service whose
	 * package uses this module
	 *
	 * @param stdClass $package A stdClass object representing the selected package
	 * @return array An array of tabs in the format of method => title. Example: array('methodName' => "Title", 'methodName2' => "Title2")
	 */
	public function getClientTabs( $package ) {
		if ( $package->meta->type == "domain" ) {
			return array(
				'tabClientWhois' => Language::_( "Namesilo.tab_whois.title", true ),
				'tabClientNameservers' => Language::_( "Namesilo.tab_nameservers.title", true ),
                'tabClientHosts' => Language::_( "Namesilo.tab_hosts.title", true ),
				'tabClientSettings' => Language::_( "Namesilo.tab_settings.title", true ),
			);
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
     * Admin Hosts tab
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $get Any GET parameters
     * @param array $post Any POST parameters
     * @param array $files Any FILES parameters
     * @return string The string representing the contents of this tab
     */
    public function tabHosts($package, $service, array $get=null, array $post=null, array $files=null) {
        return $this->manageHosts("tab_hosts", $package, $service, $get, $post, $files);
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
     * Admin Hosts tab
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $get Any GET parameters
     * @param array $post Any POST parameters
     * @param array $files Any FILES parameters
     * @return string The string representing the contents of this tab
     */
    public function tabClientHosts($package, $service, array $get=null, array $post=null, array $files=null) {
        return $this->manageHosts("tab_client_hosts", $package, $service, $get, $post, $files);
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
	public function tabClientSettings( $package, $service, array $get = null, array $post = null, array $files = null ) {
		if ( !isset( $this->Clients ) ) {
			Loader::loadModels( $this, array( "Clients" ) );
		}
		foreach ( $this->Clients->getCustomFieldValues( $service->{'client_id'} ) as $key => $value ) {
			if ( $value->{'name'} == "Disable Domain Transfers"
				&& $value->{'value'} == "Yes" )
			{
				//$this->view = new View( "whois_disabled", "client/NETLINK" );
				//$this->view->setDefaultView( "app" . DS );
				$this->view = new View( "whois_disabled", "default" );
				$this->view->setDefaultView( self::$defaultModuleView );
				return $this->view->fetch();
			}
		}
		return $this->manageSettings("tab_client_settings", $package, $service, $get, $post, $files);
	}

	/**
	 * Admin Actions tab
	 *
	 * @param stdClass $package A stdClass object representing the current package
	 * @param stdClass $service A stdClass object representing the current service
	 * @param array $get Any GET parameters
	 * @param array $post Any POST parameters
	 * @param array $files Any FILES parameters
	 * @return string The string representing the contents of this tab
	 */
	public function tabAdminActions( $package, $service, array $get = null, array $post = null, array $files = null ) {

		$vars = new stdClass();

		Loader::load( __DIR__ . DS . "includes" . DS . "communication.php" );

		$communication = new Communication( $service );

		$vars->options = $communication->getNotices();

		if (!empty($post)){
            $fields = $this->serviceFieldsToObject( $service->fields );
            $row = $this->getModuleRow($package->module_row);
            $api = $this->getApi($row->meta->user, $row->meta->key, $row->meta->sandbox == "true");
            $domains = new NamesiloDomains($api);

		    if(!empty($post['notice'])) {
                $communication->send($post);
            }
            if(isset($post['action']) && $post['action'] == 'sync_date'){
                Loader::loadModels($this, ['Services']);

		        $domain_info = $domains->getDomainInfo(array('domain'=>$fields->domain));
		        $this->processResponse($api,$domain_info);

		        if(!$this->Input->errors()) {
                    $domain_info = $domain_info->response();
                    $expires = $domain_info->expires;
                    $edit_vars['date_renews'] = date('Y-m-d h:i:s', strtotime($expires));
                    $this->Services->edit($service->id, $edit_vars, $bypass_module = true);
                }
            }
		}

		$this->view = new View( 'tab_admin_actions', "default" );

		Loader::loadHelpers( $this, array ( "Form", "Html" ) );

		$this->view->set( "vars", $vars );
		$this->view->setDefaultView( self::$defaultModuleView );

		return $this->view->fetch();
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
	private function manageWhois( $view, $package, $service, array $get = null, array $post = null, array $files = null ) {

		$vars = new stdClass();

		if ( in_array( $service->status, self::$pending ) ) {
			$this->view = new View( 'pending', "default" );
			$this->view->setDefaultView( self::$defaultModuleView );
			return $this->view->fetch();
		}
		else if ( $view == "tab_client_whois" && $service->status == "suspended" ) {
			$this->view = new View( 'suspended', "default" );
			$this->view->setDefaultView( self::$defaultModuleView );
			return $this->view->fetch();
		}

		// if the domain is pending transfer display a notice of such
        $checkDomainStatus = $this->checkDomainStatus($service,$package);
		if(isset($checkDomainStatus)) {
            return $checkDomainStatus;
        }


		$this->view = new View( $view, "default" );
		// Load the helpers required for this view
		Loader::loadHelpers( $this, array ( "Form", "Html" ) );

		$row = $this->getModuleRow( $package->module_row );
		$api = $this->getApi( $row->meta->user, $row->meta->key, $row->meta->sandbox == "true" );
		$domains = new NamesiloDomains( $api );

		$sections = array( 'registrant', 'admin', 'tech', 'billing' );

		$vars = new stdClass();

		$whois_fields = Configure::get( "Namesilo.whois_fields" );
		$fields = $this->serviceFieldsToObject( $service->fields );

		$domainInfo = $domains->getDomainInfo( array( 'domain' => $fields->domain ) );
		if ( self::$codes[$domainInfo->status()][1] == "fail" ) {
			$this->processResponse( $api, $domainInfo );
			return false;
		}

		$contact_ids = $domainInfo->response( true )['contact_ids'];

		if ( !empty( $post ) ) {
            $new_ids = $delete_ids = array();

            $params = array("domain" => $fields->domain);

            foreach ($post as $key => $value) {
                $response = $domains->addContacts($value);
                $this->processResponse($api, $response);
                if (self::$codes[$response->status()][1] == "success") {
                    $new_ids[$key] = $params[$key] = $response->response()->contact_id;
                    $delete_ids[] = $contact_ids[$key];
                }
            }

            $response = $domains->setContacts($params);
            if (self::$codes[$response->status()][1] == "success") {
                // Delete old contact IDs and set new ones
                foreach ($delete_ids as $id) $domains->deleteContacts(array('contact_id' => $id));
                $contact_ids = array_replace($contact_ids, $new_ids);
            }

            //$vars = (object)$post;
		}

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
			//*/

			// Format fields
			foreach ( $contacts as $section => $element ) {
				foreach ( $element as $name => $value ) {
					// Value must be a string
					if ( !is_scalar( $value ) )
						$value = "";
					if ( isset( $whois_fields[$name]['rp'] ) )
						$vars->{$section . '[' . $whois_fields[$name]['rp'] . ']'} = $value;
				}
			}
		}

		$all_fields = array();
		foreach ( $whois_fields as $field => $value ) {
			$key = $value['rp'];
			$all_fields["administrative[{$key}]"] = $value;
			$all_fields["technical[{$key}]"] = $value;
			$all_fields["registrant[{$key}]"] = $value;
			$all_fields["billing[{$key}]"] = $value;
		}

		$this->view->set( "vars", $vars );
		$this->view->set( "fields", $this->arrayToModuleFields( $all_fields, null, $vars )->getFields());
		$this->view->set( "sections", $sections );
		$this->view->setDefaultView( self::$defaultModuleView );
		return $this->view->fetch();
	}

	/*
	private function whoisContacts( array $vars ) {

		$response = $domains->getDomainInfo( array( 'domain' => $fields->domain ) );

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
	*/

	/**
	 * Handle updating nameserver information
	 *
	 */
    private function manageNameservers( $view, $package, $service, array $get = null, array $post = null, array $files = null ) {

        $vars = new stdClass();

        if ( in_array( $service->status, self::$pending ) ) {
            $this->view = new View( 'pending', "default" );
        }
        else if ( $view == "tab_client_nameservers" && $service->status == "suspended" ) {
            $this->view = new View( 'suspended', "default" );
        }
        else {

            // if the domain is pending transfer display a notice of such
            $checkDomainStatus = $this->checkDomainStatus($service,$package);
            if(isset($checkDomainStatus)) {
                return $checkDomainStatus;
            }

            $this->view = new View( $view, "default" );
            // Load the helpers required for this view
            Loader::loadHelpers( $this, array ( "Form", "Html" ) );

            $row = $this->getModuleRow( $package->module_row );
            $api = $this->getApi( $row->meta->user, $row->meta->key, $row->meta->sandbox == "true" );
            $dns = new NamesiloDomainsDns( $api );

            $fields = $this->serviceFieldsToObject( $service->fields );

            $tld = $this->getTld( $fields->domain,$row );
            $sld = substr( $fields->domain, 0, -strlen( $tld ) );

            if ( ! empty ( $post ) ) {
                $args = array(); $i = 1;
                foreach( $post['ns'] as $ns ) {
                    $args["ns{$i}"] = $ns;
                    $i++;
                }

                $args['domain'] = $fields->domain;

                $response = $dns->setCustom( $args );
                $this->processResponse( $api, $response );

                $vars = (object)$post;
            }
            else {
                $response = $dns->getList( array( 'domain' => $fields->domain ) )->response();

                if ( isset ( $response->nameservers ) ) {
                    $vars->ns = array();
                    foreach ( $response->nameservers->nameserver as $ns ) {
                        $vars->ns[] = $ns;
                    }
                }
            }

        }

        $this->view->set( "vars", $vars );
        $this->view->setDefaultView( self::$defaultModuleView );
        return $this->view->fetch();
    }

	/**
	 * since the api only returns XML sometimes the return array/object changes based on the xml.  lets get it consistent for hosts
	 */
    private function getRegisteredHosts($package,$service){
        $fields = $this->serviceFieldsToObject($service->fields);

        $row = $this->getModuleRow( $package->module_row );
        $api = $this->getApi( $row->meta->user, $row->meta->key, $row->meta->sandbox == "true" );
        $ns = new NamesiloDomainsNs( $api );

        $response = $ns->getInfo( array( 'domain' => $fields->domain ) )->response();
        $host_obj = new stdClass();
        $hosts = [];

        // lets get our data in a consistent format
        if(isset($response->hosts->host) && isset($response->hosts->ip)){
            if(!is_array($response->hosts->ip)) {
                $ips[] = $response->hosts->ip;
            }else{
                $ips = $response->hosts->ip;
            }
            $host_obj->host = $response->hosts->host;
            $host_obj->ip = $ips;
            $hosts[0] = $host_obj;
            return $hosts;
        }

        if(isset($response->hosts)) {
            foreach ($response->hosts as $host) {
                if (!is_array($host->ip)) {
                    $ips[] = $host->ip;
                } else {
                    $ips = $host->ip;
                }
                $host_obj->host = $host->host;
                $host_obj->ip = $ips;
                $hosts[] = $host_obj;
                $host_obj = new stdClass();
                $ips = null;
            }
        }

        return $hosts;
    }


    /**
     * Handle updating host information
     *
     */
    private function manageHosts( $view, $package, $service, array $get = null, array $post = null, array $files = null ) {
        $vars = new stdClass();
        if ( in_array( $service->status, self::$pending ) ) {
            $this->view = new View( 'pending', "default" );
        }elseif( $view == "tab_client_hosts" && $service->status == "suspended" ) {
            $this->view = new View('suspended', "default");
        }else{
            // if the domain is pending transfer display a notice of such
            $checkDomainStatus = $this->checkDomainStatus($service,$package);
            if(isset($checkDomainStatus)) {
                return $checkDomainStatus;
            }

            $this->view = new View( $view, "default" );
            $this->view->base_uri = $this->base_uri;
            // Load the helpers required for this view
            Loader::loadHelpers( $this, array ( "Form", "Html" ) );

            $row = $this->getModuleRow( $package->module_row );
            $api = $this->getApi( $row->meta->user, $row->meta->key, $row->meta->sandbox == "true" );
            $ns = new NamesiloDomainsNs( $api );

            $fields = $this->serviceFieldsToObject( $service->fields );
            $this->view->set('domain', $fields->domain);

            if (!empty($post)) {
                foreach($post['hosts'] as $host=>$ips){
                    $ips_arr = [];
                    foreach ($ips as $key => $ip) {
                        if ($ip)
                            $ips_arr["ip" . ($key + 1)] = $ip;
                    }

                    // if all of the ips are blanked, lets remove the host
                    if(!$ips_arr) {
                        $response = $ns->delete(array('domain' => $fields->domain, 'current_host' => $host));
                        $this->processResponse($api, $response);
                    }else{
                        $args = array_merge(array('domain' => $fields->domain, 'current_host' => $host, 'new_host' => $host), $ips_arr);
                        $response = $ns->update($args);
                        $this->processResponse($api, $response);
                    }
                }

                if(!empty($post['new_host']) && !empty($post['new_host_ip'])){
                    $response = $ns->create(array('domain' => $fields->domain, 'new_host' => $post['new_host'], 'ip1' => $post['new_host_ip']));
                    $this->processResponse($api, $response);
                }

                $vars = (object)$post;
            }

            $vars->hosts = $this->getRegisteredHosts($package,$service);
            $this->view->set("vars", $vars);
            $this->view->set('client_id', $service->client_id);
            $this->view->set('service_id', $service->id);
        }

        $this->view->setDefaultView(self::$defaultModuleView);
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

		$vars = new stdClass();

		if ( in_array( $service->status, self::$pending ) ) {
			$this->view = new View( 'pending', "default" );
		}
		else if ( $view == "tab_client_settings" && $service->status == "suspended" ) {
			$this->view = new View( 'suspended', "default" );
		}
		else {

            // if the domain is pending transfer display a notice of such
            $checkDomainStatus = $this->checkDomainStatus($service,$package);
            if(isset($checkDomainStatus)) {
                return $checkDomainStatus;
            }

			$this->view = new View($view, "default");
			// Load the helpers required for this view
			Loader::loadHelpers($this, array("Form", "Html"));

			$row = $this->getModuleRow($package->module_row);
			$api = $this->getApi($row->meta->user, $row->meta->key, $row->meta->sandbox == "true");
			$domains = new NamesiloDomains($api);
			$transfer = new NamesiloDomainsTransfer($api);

			$fields = $this->serviceFieldsToObject($service->fields);

			if ( !empty( $post ) ) {
                if(isset($post['resend_verification_email'])) {
                    $response = $domains->emailVerification(array('email' => $post['resend_verification_email']));
                    $this->processResponse($api, $response);
                }else {
                    if (isset($post['registrar_lock'])) {
                        $LockAction = $post['registrar_lock'] == "Yes" ? "Lock" : "Unlock";
                        $response = $domains->setRegistrarLock($LockAction, array('domain' => $fields->domain));
                        $this->processResponse($api, $response);
                    }

                    if (isset($post['request_epp'])) {
                        $response = $transfer->getEpp(array('domain' => $fields->domain));
                        $this->processResponse($api, $response);
                    }

                    if (isset($post['whois_privacy_before']) || isset($post['whois_privacy'])) {
                        if ($post['whois_privacy_before'] == 'No' && $post['whois_privacy'] == 'Yes') {
                            $response = $domains->addPrivacy(array('domain' => $fields->domain));
                            $this->processResponse($api, $response);
                        } elseif ($post['whois_privacy_before'] == 'Yes' && !isset($post['whois_privacy'])) {
                            $response = $domains->removePrivacy(array('domain' => $fields->domain));
                            $this->processResponse($api, $response);
                        }
                    }

                    $vars = (object)$post;
                }
			}else{
				$response = $domains->getRegistrarLock( array ( 'domain' => $fields->domain ) )->response();
				if ( isset ( $response->locked ) ) {
					$vars->registrar_lock = $response->locked;
				}

                $info = $domains->getDomainInfo(array('domain'=>$fields->domain));
				$info_response = $info->response();
				if(isset($info_response->private)) {
                    $vars->whois_privacy = $info_response->private;
                }
			}

			if(!isset($info))
                $info = $domains->getDomainInfo(array('domain'=>$fields->domain));
            $registrant_id = $info->response(true)['contact_ids']['registrant'];
            $registrant_info = $domains->getContacts(array('contact_id'=>$registrant_id));
            $registrant_email = $registrant_info->response()->contact->email;

            $registrant_verification = $domains->registrantVerificationStatus()->response();
            if(!is_array($registrant_verification->email))
                $registrant_verification->email = array($registrant_verification->email);
            foreach($registrant_verification->email as $registrant){
                if($registrant->email_address == $registrant_email) {
                    $vars->registrant_verification_info = $registrant;
                }
            }
		}

		$this->view->set( "vars", $vars );
		$this->view->setDefaultView( self::$defaultModuleView );
		return $this->view->fetch();
	}

	/**
	 * Performs a whois lookup on the given domain
	 *
	 * @param string $domain The domain to lookup
	 * @return boolean true if available, false otherwise
	 */
	public function checkAvailability( $domain ) {

		$row = $this->getModuleRow();
		$api = $this->getApi( $row->meta->user, $row->meta->key, $row->meta->sandbox == "true" );

		$domains = new NamesiloDomains($api);
		$result = $domains->check( array ( 'domains' => $domain ) );

		if ( self::$codes[$result->status()][1] == "fail" ) {
			return false;
		}

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
			),
            'portfolio' => array(
                'valid' => array(
                    'rule' => array(array($this, "validatePortfolio"), $vars['key'], $vars['user'], isset($vars['sandbox']) ? $vars['sandbox'] : "false"),
                    'message' => Language::_("Namesilo.!error.portfolio.valid_portfolio", true)
                )
            ),
            'payment_id' => array(
                'valid' => array(
                    'rule' => array('matches', '/^[\s\d]*$/'),
                    'message' => Language::_("Namesilo.!error.payment_id.valid_format", true)
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
		//$status = $domains->check( array( 'domains' => "example.com" ) )->status();
		$response = $domains->check( array( 'domains' => "example.com" ) );
		$this->processResponse( $api, $response );
		return true;
	}

	public function validatePortfolio($portfolio, $key, $user, $sandbox){
        $api = $this->getApi($user, $key, $sandbox == "true");
        $domains = new NamesiloDomains($api);
        $response = $domains->portfolioList();
        $this->processResponse( $api, $response );
        $response = $response->response();

        if(isset($response->portfolios->name)){
            if(!in_array($portfolio,$response->portfolios->name) && $portfolio){
                return false;
            }
        }
        return true;
    }

	/**
	 * Initializes the NamesiloApi and returns an instance of that object
	 *
	 * @param string $user The user to connect as
	 * @param string $key The key to use when connecting
	 * @param boolean $sandbox Whether or not to process in sandbox mode (for testing)
	 * @param string $username The username to execute an API command using
     * @param bool $batch use API batch mode
	 * @return NamesiloApi The NamesiloApi instance
	 */
	public function getApi( $user = null, $key = null, $sandbox = true, $username = null, $batch = false ) {

		Loader::load( __DIR__ . DS . "apis" . DS . "namesilo_api.php" );

		if ( empty( $user ) || empty( $key ) ) {
			$row = $this->getModuleRow();
			$user = $row->meta->user;
			$key = $row->meta->key;
			$sandbox = $row->meta->sandbox;
		}

		return new NamesiloApi( $user, $key, $sandbox, $username, $batch );
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
	private function logRequest( NamesiloApi $api, NamesiloResponse $response ) {
		$last_request = $api->lastRequest();
		$url = substr( $last_request['url'], 0, strpos( $last_request['url'], '?' ) );
		$this->log( $url, serialize( $last_request['args'] ), "input", true );
		$this->log( $url, $response->raw(), "output", self::$codes[$response->status()][1] == "success" );
	}

	/**
	 * Returns the TLD of the given domain
	 *
	 * @param string $domain The domain to return the TLD from
     * @param stdClass module row object
	 * @return string The TLD of the domain
	 */
	private function getTld($domain,$row=null) {
        if($row == null)
	        $row = $this->getNamesiloRow();

		$tlds = $this->getTlds($row);
		$domain = strtolower($domain);

		foreach ($tlds as $tld) {
			if (substr($domain, -strlen($tld)) == $tld)
				return $tld;
		}
		return strstr($domain, ".");
	}

	private function getTlds($row) {

		$tlds = Cache::fetchCache( 'tld_cache', 'Namesilo' . DS );

		if ( $tlds !== false ) {
			return unserialize( base64_decode( $tlds ) );
		}

		$result = $this->getApi($row->meta->user, $row->meta->key, $row->meta->sandbox == "true")->submit( 'getPrices' );

		$tlds = array();
		foreach( $result->response() as $tld => $v ) {
			if ( !is_object( $v ) ) {
				continue;
			}
			$tlds[] = '.' . $tld;
		}
		
		if ( count( $tlds ) > 0 ) {
			
			if ( Configure::get( 'Caching.on' ) && is_writable( CACHEDIR ) ) {
				try {
					Cache::writeCache(
						'tld_cache',
						base64_encode( serialize( $tlds ) ),
						strtotime( Configure::get( 'Blesta.cache_length' ) ) - time(),
						'Namesilo' . DS
					);
				} catch ( Exception $e ) {
					// Couldn't cache
					error_log( $e );
				}
			}
		}
		return $tlds;
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

	private function checkDomainStatus($service, $package){
        $fields = $this->serviceFieldsToObject( $service->fields );
        $row = $this->getModuleRow( $package->module_row );
        $api = $this->getApi( $row->meta->user, $row->meta->key, $row->meta->sandbox == "true" );
        $domains = new NamesiloDomains($api);
        $domain_info = $domains->getDomainInfo(array('domain'=>$fields->domain))->response();
        if(isset($domain_info->code) && $domain_info->code != 300){
            $transfer = new NamesiloDomainsTransfer($api);
            $transfer_info = $transfer->getStatus(array('domain'=>$fields->domain))->response();
            if(isset($transfer_info->code) && $transfer_info->code == 300){
                $this->view = new View('transferstatus', "default");
                $this->view->setDefaultView( self::$defaultModuleView );
                $this->view->set('transferstatus', $transfer_info);
                Loader::loadHelpers( $this, array ( "Form", "Html" ) );
                return $this->view->fetch();
            }
        }
    }

    private function getRenewInfo($service_id,$api_object){
	    $vars = array();

	    $service = $this->Services->get($service_id);
	    $api_response = $api_object->getDomainInfo(array(
	        'domain'=>$service->name
        ))->response();

        if($api_response->code != 300){
            $vars = array(
                'domain' => $service->name,
                'error' => array(
                    'code' => $api_response->code,
                    'detail' => $api_response->detail
                )
            );
            return $vars;
        }elseif(strtotime($api_response->expires) < 946706400){
            $vars = array(
                'domain' => $service->name,
                'error' => array(
                    'code' => $api_response->code,
                    'detail' => $api_response->expires . "expires date from the API cannot possibly be valid"
                )
            );
            return $vars;
        }

        $date_renews = new DateTime($service->date_renews);
        $expires = new DateTime($api_response->expires);

        $client = $this->Clients->get($service->client_id);
        $suspend_days = $this->ClientGroups->getSetting($client->client_group_id, 'suspend_services_days_after_due')->value;

        // take into account suspension threshold and a 3 day buffer
        $target_date_obj = $expires->modify("- " . (3 + $suspend_days) . " days");
        $target_date = $target_date_obj->format('Y-m-d H:i:s');

        if($date_renews->diff($target_date_obj)->format('%a') > 0){
            $vars = array(
                'service_id' => $service_id,
                'domain' => $service->name,
                'date_before' => $date_renews->format('Y-m-d H:i:s'),
                'date_after' => $target_date,
                'error' => false
            );
        }

        return $vars;
    }

    private function getNamesiloRow(){
        Loader::loadModels($this,array("ModuleManager"));

        $modules = $this->ModuleManager->getInstalled();
        foreach ($modules as $module) {
            $module_data = $this->ModuleManager->get($module->id);
            foreach ($module_data->rows as $module_row) {
                if(isset($module_row->meta->namesilo_module)) {
                    return $module_row;
                }
            }
        }
    }
	
	public function debug( $data ) {
		mail( self::$debug_to, "Namesilo Module " /*. self::$version*/ . " Debug", var_export( $data, true ), "From: blesta@localhost\n\n" );
	}
	
}
