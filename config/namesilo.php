<?php
// All available TLDs
Configure::set("Namesilo.tlds", array(
	".co.uk",
	".com.au",
	".com.es",
	".com.pe",
    ".com.sg",
	".de.com",
	".me.uk",
	".net.au",
	".net.pe",
	".nom.es",
	".org.au",
	".org.es",
	".org.pe",
	".org.uk",
	".us.com",
	".asia",
	".biz",
	".bz",
	".ca",
	".cc",
    ".ch",
	".cm",
	".co",
	".com",
	".de",
	".es",
	".eu",
    ".fr",
	".in",
	".info",
    ".io",
    ".li",
	".me",
	".mobi",	
	".net",
	".nu",
	".org",
	".pe",
	".pw",
    ".sg",
	".tv",	
	".us",
	".ws",
	".xxx"
));

// Transfer fields
Configure::set("Namesilo.transfer_fields", array(
	'DomainName' => array(
		'label' => Language::_("Namesilo.transfer.DomainName", true),
		'type' => "text"
	),
	'EPPCode' => array(
		'label' => Language::_("Namesilo.transfer.EPPCode", true),
		'type' => "text"
	)
));

// Domain fields
Configure::set("Namesilo.domain_fields", array(
	'DomainName' => array(
		'label' => Language::_("Namesilo.domain.DomainName", true),
		'type' => "text"
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
	'first_name' => array(
		'label' => Language::_("Namesilo.whois.FirstName", true),
		'type' => "text",
		'key' => 'fn',
	),
	'last_name' => array(
		'label' => Language::_("Namesilo.whois.LastName", true),
		'type' => "text",
		'key' => 'ln',
	),
	'address' => array(
		'label' => Language::_("Namesilo.whois.Address1", true),
		'type' => "text",
		'key' => 'ad',
	),
	'address2' => array(
		'label' => Language::_("Namesilo.whois.Address2", true),
		'type' => "text",
		'key' => 'ad2',
	),
	'city' => array(
		'label' => Language::_("Namesilo.whois.City", true),
		'type' => "text",
		'key' => 'cy',
	),
	'state' => array(
		'label' => Language::_("Namesilo.whois.StateProvince", true),
		'type' => "text",
		'key' => 'st',
	),
	'zip' => array(
		'label' => Language::_("Namesilo.whois.PostalCode", true),
		'type' => "text",
		'key' => 'zp',
	),
	'country' => array(
		'label' => Language::_("Namesilo.whois.Country", true),
		'type' => "text",
		'key' => 'ct',
	),
	'phone' => array(
		'label' => Language::_("Namesilo.whois.Phone", true),
		'type' => "text",
		'key' => 'ph',
	),
	'email' => array(
		'label' => Language::_("Namesilo.whois.EmailAddress", true),
		'type' => "text",
		'key' => 'em',
	),
));

// .US
Configure::set("Namesilo.domain_fields.us", array(
	'RegistrantNexus' => array(
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
	'RegistrantPurpose' => array(
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

// .EU
Configure::set("Namesilo.domain_fields.eu", array(
	'EUAgreeWhoisPolicy' => array(
		'label' => Language::_("Namesilo.domain.EUAgreeWhoisPolicy", true),
		'type' => "checkbox",
		'options' => array(
			'YES' => Language::_("Namesilo.domain.EUAgreeWhoisPolicy.yes", true)
		)
	),
	'EUAgreeDeletePolicy' => array(
		'label' => Language::_("Namesilo.domain.EUAgreeDeletePolicy", true),
		'type' => "checkbox",
		'options' => array(
			'YES' => Language::_("Namesilo.domain.EUAgreeDeletePolicy.yes", true)
		)
	)
));

// .CA
Configure::set("Namesilo.domain_fields.ca", array(
	'CIRALegalType' => array(
		'label' => Language::_("Namesilo.domain.CIRALegalType", true),
		'type' => "select",
		'options' => array(
			'CCO' => Language::_("Namesilo.domain.RegistrantPurpose.cco", true),
			'CCT' => Language::_("Namesilo.domain.RegistrantPurpose.cct", true),
			'RES' => Language::_("Namesilo.domain.RegistrantPurpose.res", true),
			'GOV' => Language::_("Namesilo.domain.RegistrantPurpose.gov", true),
			'EDU' => Language::_("Namesilo.domain.RegistrantPurpose.edu", true),
			'ASS' => Language::_("Namesilo.domain.RegistrantPurpose.ass", true),
			'HOP' => Language::_("Namesilo.domain.RegistrantPurpose.hop", true),
			'PRT' => Language::_("Namesilo.domain.RegistrantPurpose.prt", true),
			'TDM' => Language::_("Namesilo.domain.RegistrantPurpose.tdm", true),
			'TRD' => Language::_("Namesilo.domain.RegistrantPurpose.trd", true),
			'PLT' => Language::_("Namesilo.domain.RegistrantPurpose.plt", true),
			'LAM' => Language::_("Namesilo.domain.RegistrantPurpose.lam", true),
			'TRS' => Language::_("Namesilo.domain.RegistrantPurpose.trs", true),
			'ABO' => Language::_("Namesilo.domain.RegistrantPurpose.abo", true),
			'INB' => Language::_("Namesilo.domain.RegistrantPurpose.inb", true),
			'LGR' => Language::_("Namesilo.domain.RegistrantPurpose.lgr", true),
			'OMK' => Language::_("Namesilo.domain.RegistrantPurpose.omk", true),
			'MAJ' => Language::_("Namesilo.domain.RegistrantPurpose.maj", true)
		)
	),
	'CIRAWhoisDisplay' => array(
		'label' => Language::_("Namesilo.domain.CIRAWhoisDisplay", true),
		'type' => "select",
		'options' => array(
			'Full' => Language::_("Namesilo.domain.CIRAWhoisDisplay.full", true),
			'Private' => Language::_("Namesilo.domain.CIRAWhoisDisplay.private", true),
		)
	),
	'CIRAAgreementVersion' => array(
		'type' => "hidden",
		'options' => "2.0"
	),
	'CIRAAgreementValue' => array(
		'type' => "hidden",
		'options' => "Y"
	)
));

// .CO.UK
Configure::set("Namesilo.domain_fields.co.uk", array(
	'COUKLegalType' => array(
		'label' => Language::_("Namesilo.domain.COUKLegalType", true),
		'type' => "select",
		'options' => array(
			'IND' => Language::_("Namesilo.domain.COUKLegalType.ind", true),
			'FIND' => Language::_("Namesilo.domain.COUKLegalType.find", true),
			'LTD' => Language::_("Namesilo.domain.COUKLegalType.ltd", true),
			'PLC' => Language::_("Namesilo.domain.COUKLegalType.plc", true),
			'PTNR' => Language::_("Namesilo.domain.COUKLegalType.ptnr", true),
			'LLP' => Language::_("Namesilo.domain.COUKLegalType.llp", true),
			'IP' => Language::_("Namesilo.domain.COUKLegalType.ip", true),
			'STRA' => Language::_("Namesilo.domain.COUKLegalType.stra", true),
			'SCH' => Language::_("Namesilo.domain.COUKLegalType.sch", true),
			'RCHAR' => Language::_("Namesilo.domain.COUKLegalType.rchar", true),
			'GOV' => Language::_("Namesilo.domain.COUKLegalType.gov", true),
			'OTHER' => Language::_("Namesilo.domain.COUKLegalType.other", true),
			'CRC' => Language::_("Namesilo.domain.COUKLegalType.crc", true),
			'FCORP' => Language::_("Namesilo.domain.COUKLegalType.fcorp", true),
			'STAT' => Language::_("Namesilo.domain.COUKLegalType.stat", true),
			'FOTHER' => Language::_("Namesilo.domain.COUKLegalType.fother", true)
		)
	),
	'COUKCompanyID' => array(
		'label' => Language::_("Namesilo.domain.COUKCompanyID", true),
		'type' => "text"
	),
	'COUKRegisteredfor' => array(
		'label' => Language::_("Namesilo.domain.COUKRegisteredfor", true),
		'type' => "text"
	)
));
	
// .ME.UK
Configure::set("Namesilo.domain_fields.me.uk", array(
	'MEUKLegalType' => array(
		'label' => Language::_("Namesilo.domain.MEUKLegalType", true),
		'type' => "select",
		'options' => array(
			'IND' => Language::_("Namesilo.domain.MEUKLegalType.ind", true),
			'FIND' => Language::_("Namesilo.domain.MEUKLegalType.find", true),
			'LTD' => Language::_("Namesilo.domain.MEUKLegalType.ltd", true),
			'PLC' => Language::_("Namesilo.domain.MEUKLegalType.plc", true),
			'PTNR' => Language::_("Namesilo.domain.MEUKLegalType.ptnr", true),
			'LLP' => Language::_("Namesilo.domain.MEUKLegalType.llp", true),
			'IP' => Language::_("Namesilo.domain.MEUKLegalType.ip", true),
			'STRA' => Language::_("Namesilo.domain.MEUKLegalType.stra", true),
			'SCH' => Language::_("Namesilo.domain.MEUKLegalType.sch", true),
			'RCHAR' => Language::_("Namesilo.domain.MEUKLegalType.rchar", true),
			'GOV' => Language::_("Namesilo.domain.MEUKLegalType.gov", true),
			'OTHER' => Language::_("Namesilo.domain.MEUKLegalType.other", true),
			'CRC' => Language::_("Namesilo.domain.MEUKLegalType.crc", true),
			'FCORP' => Language::_("Namesilo.domain.MEUKLegalType.fcorp", true),
			'STAT' => Language::_("Namesilo.domain.MEUKLegalType.stat", true),
			'FOTHER' => Language::_("Namesilo.domain.MEUKLegalType.fother", true)
		)
	),
	'MEUKCompanyID' => array(
		'label' => Language::_("Namesilo.domain.MEUKCompanyID", true),
		'type' => "text"
	),
	'MEUKRegisteredfor' => array(
		'label' => Language::_("Namesilo.domain.MEUKRegisteredfor", true),
		'type' => "text"
	)
));

// .ORG.UK
Configure::set("Namesilo.domain_fields.org.uk", array(
	'ORGUKLegalType' => array(
		'label' => Language::_("Namesilo.domain.ORGUKLegalType", true),
		'type' => "select",
		'options' => array(
			'IND' => Language::_("Namesilo.domain.ORGUKLegalType.ind", true),
			'FIND' => Language::_("Namesilo.domain.ORGUKLegalType.find", true),
			'LTD' => Language::_("Namesilo.domain.ORGUKLegalType.ltd", true),
			'PLC' => Language::_("Namesilo.domain.ORGUKLegalType.plc", true),
			'PTNR' => Language::_("Namesilo.domain.ORGUKLegalType.ptnr", true),
			'LLP' => Language::_("Namesilo.domain.ORGUKLegalType.llp", true),
			'IP' => Language::_("Namesilo.domain.ORGUKLegalType.ip", true),
			'STRA' => Language::_("Namesilo.domain.ORGUKLegalType.stra", true),
			'SCH' => Language::_("Namesilo.domain.ORGUKLegalType.sch", true),
			'RCHAR' => Language::_("Namesilo.domain.ORGUKLegalType.rchar", true),
			'GOV' => Language::_("Namesilo.domain.ORGUKLegalType.gov", true),
			'OTHER' => Language::_("Namesilo.domain.ORGUKLegalType.other", true),
			'CRC' => Language::_("Namesilo.domain.ORGUKLegalType.crc", true),
			'FCORP' => Language::_("Namesilo.domain.ORGUKLegalType.fcorp", true),
			'STAT' => Language::_("Namesilo.domain.ORGUKLegalType.stat", true),
			'FOTHER' => Language::_("Namesilo.domain.ORGUKLegalType.fother", true)
		)
	),
	'ORGUKCompanyID' => array(
		'label' => Language::_("Namesilo.domain.ORGUKCompanyID", true),
		'type' => "text"
	),
	'ORGUKRegisteredfor' => array(
		'label' => Language::_("Namesilo.domain.ORGUKRegisteredfor", true),
		'type' => "text"
	)
));

// .ASIA
Configure::set("Namesilo.domain_fields.asia", array(
	'ASIACCLocality' => array(
		'type' => "hidden",
		'options' => null
	),
	'ASIALegalEntityType' => array(
		'label' => Language::_("Namesilo.domain.ASIALegalEntityType", true),
		'type' => "select",
		'options' => array(
			'corporation' => Language::_("Namesilo.domain.ASIALegalEntityType.corporation", true),
			'cooperative' => Language::_("Namesilo.domain.ASIALegalEntityType.cooperative", true),
			'partnership' => Language::_("Namesilo.domain.ASIALegalEntityType.partnership", true),
			'government' => Language::_("Namesilo.domain.ASIALegalEntityType.government", true),
			'politicalParty' => Language::_("Namesilo.domain.ASIALegalEntityType.politicalParty", true),
			'society' => Language::_("Namesilo.domain.ASIALegalEntityType.society", true),
			'institution' => Language::_("Namesilo.domain.ASIALegalEntityType.institution", true),
			'naturalPerson' => Language::_("Namesilo.domain.ASIALegalEntityType.naturalPerson", true)
		)
	),
	'ASIAIdentForm' => array(
		'label' => Language::_("Namesilo.domain.ASIAIdentForm", true),
		'type' => "select",
		'options' => array(
			'certificate' => Language::_("Namesilo.domain.ASIAIdentForm.certificate", true),
			'legislation' => Language::_("Namesilo.domain.ASIAIdentForm.legislation", true),
			'societyRegistry' => Language::_("Namesilo.domain.ASIAIdentForm.societyRegistry", true),
			'politicalPartyRegistry' => Language::_("Namesilo.domain.ASIAIdentForm.politicalPartyRegistry", true),
			'passport' => Language::_("Namesilo.domain.ASIAIdentForm.passport", true)
		)
	),
	'ASIAIdentNumber' => array(
		'label' => Language::_("Namesilo.domain.ASIAIdentNumber", true),
		'type' => "text"
	)
));

// .DE
Configure::set("Namesilo.domain_fields.de", array(
	'DEConfirmAddress' => array(
		'type' => "hidden",
		'options' => "DE"
	),
	'DEAgreeDelete' => array(
		'type' => "hidden",
		'options' => "YES"
	)
));

// .FR
Configure::set("Namesilo.domain_fields.fr", array(
    'FRLegalType' => array(
        'label' => Language::_("Namesilo.domain.FRLegalType", true),
		'type' => "select",
		'options' => array(
            'Individual' => Language::_("Namesilo.domain.FRLegalType.individual", true),
            'Company' => Language::_("Namesilo.domain.FRLegalType.company", true),
        )
    ),
    'FRRegistrantBirthDate' => array(
        'label' => Language::_("Namesilo.domain.FRRegistrantBirthDate", true),
        'type' => "text",
        'tooltip' => Language::_("Namesilo.!tooltip.FRRegistrantBirthDate", true)
    ),
    'FRRegistrantBirthplace' => array(
        'label' => Language::_("Namesilo.domain.FRRegistrantBirthplace", true),
        'type' => "text"
    ),
    'FRRegistrantLegalId' => array(
        'label' => Language::_("Namesilo.domain.FRRegistrantLegalId", true),
        'type' => "text",
        'tooltip' => Language::_("Namesilo.!tooltip.FRRegistrantLegalId", true)
    ),
    'FRRegistrantTradeNumber' => array(
        'label' => Language::_("Namesilo.domain.FRRegistrantTradeNumber", true),
        'type' => "text"
    ),
    'FRRegistrantDunsNumber' => array(
        'label' => Language::_("Namesilo.domain.FRRegistrantDunsNumber", true),
        'type' => "text",
        'tooltip' => Language::_("Namesilo.!tooltip.FRRegistrantDunsNumber", true)
    ),
    'FRRegistrantLocalId' => array(
        'label' => Language::_("Namesilo.domain.FRRegistrantLocalId", true),
        'type' => "text"
    ),
    'FRRegistrantJoDateDec' => array(
        'label' => Language::_("Namesilo.domain.FRRegistrantJoDateDec", true),
        'type' => "text",
        'tooltip' => Language::_("Namesilo.!tooltip.FRRegistrantJoDateDec", true)
    ),
    'FRRegistrantJoDatePub' => array(
        'label' => Language::_("Namesilo.domain.FRRegistrantJoDatePub", true),
        'type' => "text",
        'tooltip' => Language::_("Namesilo.!tooltip.FRRegistrantJoDatePub", true)
    ),
    'FRRegistrantJoNumber' => array(
        'label' => Language::_("Namesilo.domain.FRRegistrantJoNumber", true),
        'type' => "text"
    ),
    'FRRegistrantJoPage' => array(
        'label' => Language::_("Namesilo.domain.FRRegistrantJoPage", true),
        'type' => "text"
    )
));

require_once dirname( __FILE__ ) . '/codes.php';
