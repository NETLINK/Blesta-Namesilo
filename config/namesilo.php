<?php

// DNS Record fields
Configure::set("Namesilo.dns_records", [
    'record_type' => [
        'label' => 'Record type:',
        'type' => 'select',
        'options' => [
            '' => Language::_("AppController.select.please", true),
            'A'  => 'A - Record',
            'AAAA' => 'AAAA - Record',
            'CNAME' => 'CNAME - Record',
            'MX' => 'MX - Record',
            'TXT' => 'TXT - Record'
        ]
    ],
    'host' => [
        'label' => 'Host:',
        'type'  => 'text'
    ],
    'value' => [
        'label' => 'Value / Target:',
        'type' => 'text'
    ],
    'ttl' => [
        'label' => 'TTL',
        'type' => 'text'

    ]
]);

// Transfer fields
Configure::set('Namesilo.transfer_fields', [
    'domain' => [
        'label' => Language::_('Namesilo.transfer.domain', true),
        'type' => 'text'
    ],
    'auth' => [
        'label' => Language::_('Namesilo.transfer.EPPCode', true),
        'type' => 'text'
    ],
    'private' => [
        'label' => Language::_('Namesilo.domain.WhoisPrivacy', true),
        'type' => 'checkbox',
        'options' => [ '1' => 'Yes' ],
    ],
]);

// Domain fields
Configure::set('Namesilo.domain_fields', [
    'domain' => [
        'label' => Language::_('Namesilo.domain.domain', true),
        'type' => 'text'
    ],
    'private' => [
        'label' => Language::_('Namesilo.domain.WhoisPrivacy', true),
        'type' => 'checkbox',
        'options' => [ '1' => 'Yes' ],
    ],
]);

// Nameserver fields
Configure::set('Namesilo.nameserver_fields', [
    'ns1' => [
        'label' => Language::_('Namesilo.nameserver.ns1', true),
        'type' => 'text'
    ],
    'ns2' => [
        'label' => Language::_('Namesilo.nameserver.ns2', true),
        'type' => 'text'
    ],
    'ns3' => [
        'label' => Language::_('Namesilo.nameserver.ns3', true),
        'type' => 'text'
    ],
    'ns4' => [
        'label' => Language::_('Namesilo.nameserver.ns4', true),
        'type' => 'text'
    ],
    'ns5' => [
        'label' => Language::_('Namesilo.nameserver.ns5', true),
        'type' => 'text'
    ]
]);

// DNSSEC
Configure::set('Namesilo.dnssec', [
    'digest_type' => [
       'label' => Language::_('Namesilo.dnssec.digest_type', true),
       'type' => 'select',
       'options' => [
           '' => Language::_('AppController.select.please', true),
           '1' => 'SHA-1 (1)',
           '2' => 'SHA-256 (2)',
           '3' => 'GOST R 34.11-94 (3)',
           '4' => 'SHA-384 (4)'
       ]
    ],
    'algorithm' => [
        'label' => Language::_('Namesilo.dnssec.algorithm', true),
        'type' => 'select',
        'options' => [
            '' => Language::_('AppController.select.please', true),
            '1' => 'RSA/MD5 (1)',
            '2' => 'Diffie-Hellman (2)',
            '3' => 'DSA/SHA-1 (3)',
            '4' => 'Elliptic Curve (4)',
            '5' => 'RSA/SHA-1 (5)',
            '6' => 'DSA-NSEC3-SHA1 (6)',
            '7' => 'RSASHA1-NSEC3-SHA1 (7)',
            '8' => 'RSA/SHA-256 (8)',
            '10' => 'RSA/SHA-512 (10)',
            '12' => 'ECC-GOST (12)',
            '13' => 'ECDSA Curve P-256 with SHA-256 (13)',
            '14' => 'ECDSA Curve P-384 with SHA-384 (14)',
            '252' => 'Indirect (252)',
            '253' => 'Private DNS (253)',
            '254' => 'Private OID (254)'
        ]
    ],
]);

// Whois fields
Configure::set('Namesilo.whois_fields', [
    /*
    'nickname' => array(
        'label' => Language::_("Namesilo.whois.Nickname", true),
        'type' => "text",
        'key' => 'nn',
    ),
    */
    'first_name' => [
        'label' => Language::_('Namesilo.whois.FirstName', true),
        'type' => 'text',
        'rp' => 'fn',
        'lp' => 'first_name',
    ],
    'last_name' => [
        'label' => Language::_('Namesilo.whois.LastName', true),
        'type' => 'text',
        'rp' => 'ln',
        'lp' => 'last_name',
    ],
    'company' => [
        'label' => Language::_('Namesilo.whois.Organization', true),
        'type' => 'text',
        'rp' => 'cp',
        'lp' => 'company',
    ],
    'address' => [
        'label' => Language::_('Namesilo.whois.Address1', true),
        'type' => 'text',
        'rp' => 'ad',
        'lp' => 'address1',
    ],
    'address2' => [
        'label' => Language::_('Namesilo.whois.Address2', true),
        'type' => 'text',
        'rp' => 'ad2',
        'lp' => 'address2',
    ],
    'city' => [
        'label' => Language::_('Namesilo.whois.City', true),
        'type' => 'text',
        'rp' => 'cy',
        'lp' => 'city',
    ],
    'state' => [
        'label' => Language::_('Namesilo.whois.StateProvince', true),
        'type' => 'text',
        'rp' => 'st',
        'lp' => 'state',
    ],
    'zip' => [
        'label' => Language::_('Namesilo.whois.PostalCode', true),
        'type' => 'text',
        'rp' => 'zp',
        'lp' => 'zip',
    ],
    'country' => [
        'label' => Language::_('Namesilo.whois.Country', true),
        'type' => 'text',
        'rp' => 'ct',
        'lp' => 'country',
    ],
    'phone' => [
        'label' => Language::_('Namesilo.whois.Phone', true),
        'type' => 'text',
        'rp' => 'ph',
        'lp' => 'phone',
    ],
    'email' => [
        'label' => Language::_('Namesilo.whois.EmailAddress', true),
        'type' => 'text',
        'rp' => 'em',
        'lp' => 'email',
    ],
]);

// .US
Configure::set('Namesilo.domain_fields.us', [
    'usnc' => [
        'label' => Language::_('Namesilo.domain.RegistrantNexus', true),
        'type' => 'select',
        'options' => [
            '' => Language::_('AppController.select.please', true),
            'C11' => Language::_('Namesilo.domain.RegistrantNexus.c11', true),
            'C12' => Language::_('Namesilo.domain.RegistrantNexus.c12', true),
            'C21' => Language::_('Namesilo.domain.RegistrantNexus.c21', true),
            'C31' => Language::_('Namesilo.domain.RegistrantNexus.c31', true),
            'C32' => Language::_('Namesilo.domain.RegistrantNexus.c32', true)
        ]
    ],
    'usap' => [
        'label' => Language::_('Namesilo.domain.RegistrantPurpose', true),
        'type' => 'select',
        'options' => [
            '' => Language::_('AppController.select.please', true),
            'P1' => Language::_('Namesilo.domain.RegistrantPurpose.p1', true),
            'P2' => Language::_('Namesilo.domain.RegistrantPurpose.p2', true),
            'P3' => Language::_('Namesilo.domain.RegistrantPurpose.p3', true),
            'P4' => Language::_('Namesilo.domain.RegistrantPurpose.p4', true),
            'P5' => Language::_('Namesilo.domain.RegistrantPurpose.p5', true)
        ]
    ]
]);

// .CA
// the commented types represent options that namesilo doesn't allow via the API.
// hopefully this will change in the future so they're here for future use.
// this is actually a CIRA limitation supposedly so I doubt it will change...
Configure::set('Namesilo.domain_fields.ca', [
    'calf' => [
        'label' => Language::_('Namesilo.domain.CIRALegalType', true),
        'type' => 'select',
        'options' => [
            '' => Language::_('AppController.select.please', true),
            'CCT' => Language::_('Namesilo.domain.RegistrantPurpose.cct', true),
            'RES' => Language::_('Namesilo.domain.RegistrantPurpose.res', true),
            'ABO' => Language::_('Namesilo.domain.RegistrantPurpose.abo', true),
            'LGR' => Language::_('Namesilo.domain.RegistrantPurpose.lgr', true),
            'OTHER' => Language::_('Namesilo.domain.RegistrantPurpose.other', true),
//            'CCO' => Language::_('Namesilo.domain.RegistrantPurpose.cco', true),
//            'GOV' => Language::_('Namesilo.domain.RegistrantPurpose.gov', true),
//            'EDU' => Language::_('Namesilo.domain.RegistrantPurpose.edu', true),
//            'ASS' => Language::_('Namesilo.domain.RegistrantPurpose.ass', true),
//            'HOP' => Language::_('Namesilo.domain.RegistrantPurpose.hop', true),
//            'PRT' => Language::_('Namesilo.domain.RegistrantPurpose.prt', true),
//            'TDM' => Language::_('Namesilo.domain.RegistrantPurpose.tdm', true),
//            'TRD' => Language::_('Namesilo.domain.RegistrantPurpose.trd', true),
//            'PLT' => Language::_('Namesilo.domain.RegistrantPurpose.plt', true),
//            'LAM' => Language::_('Namesilo.domain.RegistrantPurpose.lam', true),
//            'TRS' => Language::_('Namesilo.domain.RegistrantPurpose.trs', true),
//            'INB' => Language::_('Namesilo.domain.RegistrantPurpose.inb', true),
//            'OMK' => Language::_('Namesilo.domain.RegistrantPurpose.omk', true),
//            'MAJ' => Language::_('Namesilo.domain.RegistrantPurpose.maj', true)

        ]
    ],
    'cawd' => [
        'label' => Language::_('Namesilo.domain.CIRAWhoisDisplay', true),
        'type' => 'select',
        'options' => [
            '' => Language::_('AppController.select.please', true),
            '1' => Language::_('Namesilo.domain.CIRAWhoisDisplay.full', true),
            '0' => Language::_('Namesilo.domain.CIRAWhoisDisplay.private', true),
        ]
    ],
    'caln' => [
        'label' => Language::_('Namesilo.domain.CIRALanguage', true),
        'type' => 'select',
        'options' => [
            '' => Language::_('AppController.select.please', true),
            'en' => Language::_('Namesilo.domain.CIRALanguage.en', true),
            'fr' => Language::_('Namesilo.domain.CIRALanguage.fr', true),
        ]
    ],
    'caag' => [
        'type' => 'hidden',
        'options' => '2.0'
    ]
]);

// Email templates
Configure::set('Namesilo.email_templates', [
    'en_us' => [
        'lang' => 'en_us',
        'text' => 'Your new domain has been successfully registered!

Domain: {service.domain}

Thank you for your business!',
        'html' => '<p>Your new domain has been successfully registered!</p>
<p>Domain: {service.domain}</p>
<p>Thank you for your business!</p>'
    ]
]);

require_once __DIR__ . '/codes.php';
require_once __DIR__ . '/notices.php';
