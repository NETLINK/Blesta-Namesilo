<?php

// Transfer fields
Configure::set("Namesilo.transfer_fields", array(
	'domain' => array(
		'label' => Language::_("Namesilo.transfer.domain", true),
		'type' => "text"
	),
	'auth' => array(
		'label' => Language::_("Namesilo.transfer.EPPCode", true),
		'type' => "text"
	),
	'private' => array(
		'label' => Language::_("Namesilo.domain.WhoisPrivacy", true),
		'type' => "checkbox",
		'options' => array( '1' => "Yes" ),
	),
));

// Domain fields
Configure::set("Namesilo.domain_fields", array(
	'domain' => array(
		'label' => Language::_("Namesilo.domain.domain", true),
		'type' => "text"
	),
	'private' => array(
		"label" => Language::_("Namesilo.domain.WhoisPrivacy", true),
		"type" => "checkbox",
		"options" => array ( '1' => "Yes" ),
	),
));

// Nameserver fields
Configure::set("Namesilo.nameserver_fields", array(
	'ns1' => array(
		'label' => Language::_("Namesilo.nameserver.ns1", true),
		'type' => "text"
	),
	'ns2' => array(
		'label' => Language::_("Namesilo.nameserver.ns2", true),
		'type' => "text"
	),
	'ns3' => array(
		'label' => Language::_("Namesilo.nameserver.ns3", true),
		'type' => "text"
	),
	'ns4' => array(
		'label' => Language::_("Namesilo.nameserver.ns4", true),
		'type' => "text"
	),
	'ns5' => array(
		'label' => Language::_("Namesilo.nameserver.ns5", true),
		'type' => "text"
	)
));

// Whois fields
Configure::set("Namesilo.whois_fields", array(
	/*
	'nickname' => array(
		'label' => Language::_("Namesilo.whois.Nickname", true),
		'type' => "text",
		'key' => 'nn',
	),
	*/
	'first_name' => array(
		'label' => Language::_("Namesilo.whois.FirstName", true),
		'type' => "text",
		'rp' => 'fn',
		'lp' => 'first_name',
	),
	'last_name' => array(
		'label' => Language::_("Namesilo.whois.LastName", true),
		'type' => "text",
		'rp' => 'ln',
		'lp' => 'last_name',
	),
	'company' => array(
		'label' => Language::_("Namesilo.whois.Organization", true),
		'type' => "text",
		'rp' => 'cp',
		'lp' => 'company',
	),
	'address' => array(
		'label' => Language::_("Namesilo.whois.Address1", true),
		'type' => "text",
		'rp' => 'ad',
		'lp' => 'address1',
	),
	'address2' => array(
		'label' => Language::_("Namesilo.whois.Address2", true),
		'type' => "text",
		'rp' => 'ad2',
		'lp' => 'address2',
	),
	'city' => array(
		'label' => Language::_("Namesilo.whois.City", true),
		'type' => "text",
		'rp' => 'cy',
		'lp' => 'city',
	),
	'state' => array(
		'label' => Language::_("Namesilo.whois.StateProvince", true),
		'type' => "text",
		'rp' => 'st',
		'lp' => 'state',
	),
	'zip' => array(
		'label' => Language::_("Namesilo.whois.PostalCode", true),
		'type' => "text",
		'rp' => 'zp',
		'lp' => 'zip',
	),
	'country' => array(
		'label' => Language::_("Namesilo.whois.Country", true),
		'type' => "text",
		'rp' => 'ct',
		'lp' => 'country',
	),
	'phone' => array(
		'label' => Language::_("Namesilo.whois.Phone", true),
		'type' => "text",
		'rp' => 'ph',
		'lp' => 'phone',
	),
	'email' => array(
		'label' => Language::_("Namesilo.whois.EmailAddress", true),
		'type' => "text",
		'rp' => 'em',
		'lp' => 'email',
	),
));

// .US
Configure::set("Namesilo.domain_fields.us", array(
	'usnc' => array(
		'label' => Language::_("Namesilo.domain.RegistrantNexus", true),
		'type' => "select",
		'options' => array(
			'C11' => Language::_("Namesilo.domain.RegistrantNexus.c11", true),
			'C12' => Language::_("Namesilo.domain.RegistrantNexus.c12", true),
			'C21' => Language::_("Namesilo.domain.RegistrantNexus.c21", true),
			'C31' => Language::_("Namesilo.domain.RegistrantNexus.c31", true),
			'C32' => Language::_("Namesilo.domain.RegistrantNexus.c32", true)
		)
	),
	'usap' => array(
		'label' => Language::_("Namesilo.domain.RegistrantPurpose", true),
		'type' => "select",
		'options' => array(
			'P1' => Language::_("Namesilo.domain.RegistrantPurpose.p1", true),
			'P2' => Language::_("Namesilo.domain.RegistrantPurpose.p2", true),
			'P3' => Language::_("Namesilo.domain.RegistrantPurpose.p3", true),
			'P4' => Language::_("Namesilo.domain.RegistrantPurpose.p4", true),
			'P5' => Language::_("Namesilo.domain.RegistrantPurpose.p5", true)
		)
	)
));


require_once __DIR__ . '/codes.php';
require_once __DIR__ . '/notices.php';
