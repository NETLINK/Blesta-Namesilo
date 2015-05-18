<?php
// Basics
$lang['Namesilo.name'] = "Namesilo";
$lang['Namesilo.module_row'] = "Account";
$lang['Namesilo.module_row_plural'] = "Accounts";


// Module management
$lang['Namesilo.add_module_row'] = "Add Account";
$lang['Namesilo.manage.module_rows_title'] = "Accounts";
$lang['Namesilo.manage.module_rows_heading.user'] = "User";
$lang['Namesilo.manage.module_rows_heading.key'] = "API Key";
$lang['Namesilo.manage.module_rows_heading.sandbox'] = "Sandbox";
$lang['Namesilo.manage.module_rows_heading.options'] = "Options";
$lang['Namesilo.manage.module_rows.edit'] = "Edit";
$lang['Namesilo.manage.module_rows.delete'] = "Delete";
$lang['Namesilo.manage.module_rows.confirm_delete'] = "Are you sure you want to delete this account?";
$lang['Namesilo.manage.module_rows_no_results'] = "There are no accounts.";

// Row Meta
$lang['Namesilo.row_meta.user'] = "User";
$lang['Namesilo.row_meta.key'] = "API Key";
$lang['Namesilo.row_meta.sandbox'] = "Sandbox";
$lang['Namesilo.row_meta.sandbox_true'] = "Yes";
$lang['Namesilo.row_meta.sandbox_false'] = "No";

// Add row
$lang['Namesilo.add_row.box_title'] = "Add Namesilo Account";
$lang['Namesilo.add_row.basic_title'] = "Basic Settings";
$lang['Namesilo.add_row.add_btn'] = "Add Account";

// Edit row
$lang['Namesilo.edit_row.box_title'] = "Edit Namesilo Account";
$lang['Namesilo.edit_row.basic_title'] = "Basic Settings";
$lang['Namesilo.edit_row.add_btn'] = "Update Account";

// Package fields
$lang['Namesilo.package_fields.type'] = "Type";
$lang['Namesilo.package_fields.type_domain'] = "Domain Registration";
$lang['Namesilo.package_fields.type_ssl'] = "SSL Certificate";
$lang['Namesilo.package_fields.tld_options'] = "TLDs";
$lang['Namesilo.package_fields.ns1'] = "Name Server 1";
$lang['Namesilo.package_fields.ns2'] = "Name Server 2";
$lang['Namesilo.package_fields.ns3'] = "Name Server 3";
$lang['Namesilo.package_fields.ns4'] = "Name Server 4";
$lang['Namesilo.package_fields.ns5'] = "Name Server 5";

// Service management
$lang['Namesilo.tab_whois.title'] = "Whois";
$lang['Namesilo.tab_whois.section_registrant'] = "Registrant";
$lang['Namesilo.tab_whois.section_admin'] = "Administrative";
$lang['Namesilo.tab_whois.section_tech'] = "Technical";
$lang['Namesilo.tab_whois.section_billing'] = "Billing";
$lang['Namesilo.tab_whois.field_submit'] = "Update Whois";

$lang['Namesilo.tab_nameservers.title'] = "Name Servers";
$lang['Namesilo.tab_nameserver.field_ns'] = "Name Server %1\$s"; // %1$s is the name server number
$lang['Namesilo.tab_nameservers.field_submit'] = "Update Name Servers";

$lang['Namesilo.tab_settings.title'] = "Settings";
$lang['Namesilo.tab_settings.field_registrar_lock'] = "Registrar Lock";
$lang['Namesilo.tab_settings.field_registrar_lock_yes'] = "Set the registrar lock. Recommended to prevent unauthorized transfer.";
$lang['Namesilo.tab_settings.field_registrar_lock_no'] = "Release the registrar lock so the domain can be transferred.";
$lang['Namesilo.tab_settings.field_request_epp'] = "Request EPP Code/Transfer Key";
$lang['Namesilo.tab_settings.field_submit'] = "Update Settings";

// Errors
$lang['Namesilo.!error.user.valid'] = "Please enter a user";
$lang['Namesilo.!error.key.valid'] = "Please enter a key";
$lang['Namesilo.!error.key.valid_connection'] = "The user and key combination appear to be invalid, or your Namesilo account may not be configured to allow API access.";


// Domain Transfer Fields
$lang['Namesilo.transfer.DomainName'] = "Domain Name";
$lang['Namesilo.transfer.EPPCode'] = "EPP Code";

// Domain Fields
$lang['Namesilo.domain.DomainName'] = "Domain Name";
$lang['Namesilo.domain.Years'] = "Years";

// Nameserver Fields
$lang['Namesilo.nameserver.ns1'] = "Name Server 1";
$lang['Namesilo.nameserver.ns2'] = "Name Server 2";
$lang['Namesilo.nameserver.ns3'] = "Name Server 3";
$lang['Namesilo.nameserver.ns4'] = "Name Server 4";
$lang['Namesilo.nameserver.ns5'] = "Name Server 5";

//$lang['Namesilo.domain.IdnCode'] = "";
//$lang['Namesilo.domain.Nameservers'] = "";
//$lang['Namesilo.domain.AddFreeWhoisguard'] = "";
//$lang['Namesilo.domain.WGEnabled Enables'] = "";
//$lang['Namesilo.domain.AddFreePositiveSSL'] = "";

// Whois Fields
//$lang['Namesilo.whois.RegistrantOrganizationName'] = "Organization";
//$lang['Namesilo.whois.RegistrantJobTitle'] = "Job Title";
$lang['Namesilo.whois.Nickname'] = "Nickname";
$lang['Namesilo.whois.FirstName'] = "First Name";
$lang['Namesilo.whois.LastName'] = "Last Name";
$lang['Namesilo.whois.Address1'] = "Address 1";
$lang['Namesilo.whois.Address2'] = "Address 2";
$lang['Namesilo.whois.City'] = "City";
$lang['Namesilo.whois.StateProvince'] = "State/Province";
//$lang['Namesilo.whois.RegistrantStateProvinceChoice'] = "State/Province Choice";
$lang['Namesilo.whois.PostalCode'] = "Postal Code";
$lang['Namesilo.whois.Country'] = "Country";
$lang['Namesilo.whois.Phone'] = "Phone";
//$lang['Namesilo.whois.RegistrantPhoneExt'] = "Phone Extension";
//$lang['Namesilo.whois.RegistrantFax'] = "Fax";
$lang['Namesilo.whois.EmailAddress'] = "Email";

// .US domain fields
$lang['Namesilo.domain.RegistrantNexus'] = "Registrant Type";
$lang['Namesilo.domain.RegistrantNexus.c11'] = "US citizen";
$lang['Namesilo.domain.RegistrantNexus.c12'] = "Permanent resident of the US";
$lang['Namesilo.domain.RegistrantNexus.c21'] = "US entity or organization";
$lang['Namesilo.domain.RegistrantNexus.c31'] = "Foreign organization";
$lang['Namesilo.domain.RegistrantNexus.c32'] = "Foreign organization with an office in the US";
$lang['Namesilo.domain.RegistrantPurpose'] = "Purpose";
$lang['Namesilo.domain.RegistrantPurpose.p1'] = "Business";
$lang['Namesilo.domain.RegistrantPurpose.p2'] = "Non-profit";
$lang['Namesilo.domain.RegistrantPurpose.p3'] = "Personal";
$lang['Namesilo.domain.RegistrantPurpose.p4'] = "Educational";
$lang['Namesilo.domain.RegistrantPurpose.p5'] = "Governmental";

// .EU domain fields
$lang['Namesilo.domain.EUAgreeWhoisPolicy'] = "Whois Policy";
$lang['Namesilo.domain.EUAgreeWhoisPolicy.yes'] = "I hereby agree that the Registry is entitled to transfer the data contained in this application to third parties(i) if ordered to do so by a public authority, carrying out its legitimate tasks; and (ii) upon demand of an ADR Provider as mentioned in section 16 of the Terms and Conditions which are published at www.eurid.eu; and (iii) as provided in Section 2 (WHOIS look-up facility) of the .eu Domain Name WHOIS Policy which is published at www.eurid.eu.";
$lang['Namesilo.domain.EUAgreeDeletePolicy'] = "Deleteion Rules";
$lang['Namesilo.domain.EUAgreeDeletePolicy.yes'] = "I agree and acknowledge to the special renewal and expiration terms set forth below for this domain name, including those terms set forth in the Registration Agreement. I understand that unless I have set this domain for autorenewal, this domain name must be explicitly renewed by the expiration date or the 20th of the month of expiration, whichever is sooner. (e.g. If the name expires on Sept 4th, 2008, then a manual renewal must be received by Sept 4th, 2008. If name expires on Sep 27th, 2008, the renewal request must be received prior to Sep 20th, 2008). If the name is not manually renewed or previously set to autorenew, a delete request will be issued by Namesilo. When a delete request is issued, the name will remain fully functional in my account until expiration, but will no longer be renewable nor will I be able to make any modifications to the name. These terms are subject to change.";

// .CA domain fields
$lang['Namesilo.domain.CIRALegalType'] = "Legal Type";
$lang['Namesilo.domain.RegistrantPurpose.cco'] = "Corporation";
$lang['Namesilo.domain.RegistrantPurpose.cct'] = "Canadian citizen";
$lang['Namesilo.domain.RegistrantPurpose.res'] = "Canadian resident";
$lang['Namesilo.domain.RegistrantPurpose.gov'] = "Government entity";
$lang['Namesilo.domain.RegistrantPurpose.edu'] = "Educational";
$lang['Namesilo.domain.RegistrantPurpose.ass'] = "Unincorporated Association";
$lang['Namesilo.domain.RegistrantPurpose.hop'] = "Hospital";
$lang['Namesilo.domain.RegistrantPurpose.prt'] = "Partnership";
$lang['Namesilo.domain.RegistrantPurpose.tdm'] = "Trade-mark";
$lang['Namesilo.domain.RegistrantPurpose.trd'] = "Trade Union";
$lang['Namesilo.domain.RegistrantPurpose.plt'] = "Political Party";
$lang['Namesilo.domain.RegistrantPurpose.lam'] = "Libraries, Archives and Museums";
$lang['Namesilo.domain.RegistrantPurpose.trs'] = "Trust";
$lang['Namesilo.domain.RegistrantPurpose.abo'] = "Aboriginal Peoples";
$lang['Namesilo.domain.RegistrantPurpose.inb'] = "Indian Band";
$lang['Namesilo.domain.RegistrantPurpose.lgr'] = "Legal Representative";
$lang['Namesilo.domain.RegistrantPurpose.omk'] = "Official Mark";
$lang['Namesilo.domain.RegistrantPurpose.maj'] = "The Queen";
$lang['Namesilo.domain.CIRAWhoisDisplay'] = "Whois";
$lang['Namesilo.domain.CIRAWhoisDisplay.full'] = "Make Public";
$lang['Namesilo.domain.CIRAWhoisDisplay.private'] = "Keep Private";

// .CO.UK domain fields
$lang['Namesilo.domain.COUKLegalType'] = "Legal Type";
$lang['Namesilo.domain.COUKLegalType.ind'] = "UK individual";
$lang['Namesilo.domain.COUKLegalType.find'] = "Non-UK individual";
$lang['Namesilo.domain.COUKLegalType.ltd'] = "UK Limited Company";
$lang['Namesilo.domain.COUKLegalType.plc'] = "UK Public Limited Company";
$lang['Namesilo.domain.COUKLegalType.ptnr'] = "UK Partnership";
$lang['Namesilo.domain.COUKLegalType.llp'] = "UK Limited Liability Partnership";
$lang['Namesilo.domain.COUKLegalType.ip'] = "UK Industrial/Provident Registered Company";
$lang['Namesilo.domain.COUKLegalType.stra'] = "UK Sole Trader";
$lang['Namesilo.domain.COUKLegalType.sch'] = "UK School";
$lang['Namesilo.domain.COUKLegalType.rchar'] = "UK Registered Charity";
$lang['Namesilo.domain.COUKLegalType.gov'] = "UK Government Body";
$lang['Namesilo.domain.COUKLegalType.other'] = "UK Entity (other)";
$lang['Namesilo.domain.COUKLegalType.crc'] = "UK Corporation by Royal Charter";
$lang['Namesilo.domain.COUKLegalType.fcorp'] = "Foreign Organization";
$lang['Namesilo.domain.COUKLegalType.stat'] = "UK Statutory Body FIND";
$lang['Namesilo.domain.COUKLegalType.fother'] = "Other Foreign Organizations";
$lang['Namesilo.domain.COUKCompanyID'] = "Company ID Number";
$lang['Namesilo.domain.COUKRegisteredfor'] = "Registrant Name";

// .ME.UK domain fields
$lang['Namesilo.domain.MEUKLegalType'] = "Legal Type";
$lang['Namesilo.domain.MEUKLegalType.ind'] = "UK individual";
$lang['Namesilo.domain.MEUKLegalType.find'] = "Non-UK individual";
$lang['Namesilo.domain.MEUKLegalType.ltd'] = "UK Limited Company";
$lang['Namesilo.domain.MEUKLegalType.plc'] = "UK Public Limited Company";
$lang['Namesilo.domain.MEUKLegalType.ptnr'] = "UK Partnership";
$lang['Namesilo.domain.MEUKLegalType.llp'] = "UK Limited Liability Partnership";
$lang['Namesilo.domain.MEUKLegalType.ip'] = "UK Industrial/Provident Registered Company";
$lang['Namesilo.domain.MEUKLegalType.stra'] = "UK Sole Trader";
$lang['Namesilo.domain.MEUKLegalType.sch'] = "UK School";
$lang['Namesilo.domain.MEUKLegalType.rchar'] = "UK Registered Charity";
$lang['Namesilo.domain.MEUKLegalType.gov'] = "UK Government Body";
$lang['Namesilo.domain.MEUKLegalType.other'] = "UK Entity (other)";
$lang['Namesilo.domain.MEUKLegalType.crc'] = "UK Corporation by Royal Charter";
$lang['Namesilo.domain.MEUKLegalType.fcorp'] = "Foreign Organization";
$lang['Namesilo.domain.MEUKLegalType.stat'] = "UK Statutory Body FIND";
$lang['Namesilo.domain.MEUKLegalType.fother'] = "Other Foreign Organizations";
$lang['Namesilo.domain.MEUKCompanyID'] = "Company ID Number";
$lang['Namesilo.domain.MEUKRegisteredfor'] = "Registrant Name";

// .ORG.UK domain fields
$lang['Namesilo.domain.ORGUKLegalType'] = "Legal Type";
$lang['Namesilo.domain.ORGUKLegalType.ind'] = "UK individual";
$lang['Namesilo.domain.ORGUKLegalType.find'] = "Non-UK individual";
$lang['Namesilo.domain.ORGUKLegalType.ltd'] = "UK Limited Company";
$lang['Namesilo.domain.ORGUKLegalType.plc'] = "UK Public Limited Company";
$lang['Namesilo.domain.ORGUKLegalType.ptnr'] = "UK Partnership";
$lang['Namesilo.domain.ORGUKLegalType.llp'] = "UK Limited Liability Partnership";
$lang['Namesilo.domain.ORGUKLegalType.ip'] = "UK Industrial/Provident Registered Company";
$lang['Namesilo.domain.ORGUKLegalType.stra'] = "UK Sole Trader";
$lang['Namesilo.domain.ORGUKLegalType.sch'] = "UK School";
$lang['Namesilo.domain.ORGUKLegalType.rchar'] = "UK Registered Charity";
$lang['Namesilo.domain.ORGUKLegalType.gov'] = "UK Government Body";
$lang['Namesilo.domain.ORGUKLegalType.other'] = "UK Entity (other)";
$lang['Namesilo.domain.ORGUKLegalType.crc'] = "UK Corporation by Royal Charter";
$lang['Namesilo.domain.ORGUKLegalType.fcorp'] = "Foreign Organization";
$lang['Namesilo.domain.ORGUKLegalType.stat'] = "UK Statutory Body FIND";
$lang['Namesilo.domain.ORGUKLegalType.fother'] = "Other Foreign Organizations";
$lang['Namesilo.domain.ORGUKCompanyID'] = "Company ID Number";
$lang['Namesilo.domain.ORGUKRegisteredfor'] = "Registrant Name";

// .ASIA domain fields
$lang['Namesilo.domain.ASIALegalEntityType'] = "Legal Type";
$lang['Namesilo.domain.ASIALegalEntityType.corporation'] = "Corporations or Companies";
$lang['Namesilo.domain.ASIALegalEntityType.cooperative'] = "Cooperatives";
$lang['Namesilo.domain.ASIALegalEntityType.partnership'] = "Partnerships or Collectives";
$lang['Namesilo.domain.ASIALegalEntityType.government'] = "Government Bodies";
$lang['Namesilo.domain.ASIALegalEntityType.politicalParty'] = "Political parties or Trade Unions";
$lang['Namesilo.domain.ASIALegalEntityType.society'] = "Trusts, Estates, Associations or Societies";
$lang['Namesilo.domain.ASIALegalEntityType.institution'] = "Institutions";
$lang['Namesilo.domain.ASIALegalEntityType.naturalPerson'] = "Natural Persons";
$lang['Namesilo.domain.ASIAIdentForm'] = "Form of Identity";
$lang['Namesilo.domain.ASIAIdentForm.certificate'] = "Certificate of Incorporation";
$lang['Namesilo.domain.ASIAIdentForm.legislation'] = "Charter";
$lang['Namesilo.domain.ASIAIdentForm.societyRegistry'] = "Societies Registry";
$lang['Namesilo.domain.ASIAIdentForm.politicalPartyRegistry'] = "Political Party Registry";
$lang['Namesilo.domain.ASIAIdentForm.passport'] = "Passport/ Citizenship ID";
$lang['Namesilo.domain.ASIAIdentNumber'] = "Identity Number";

// .FR domain fields
$lang['Namesilo.!tooltip.FRRegistrantBirthDate'] = "Set your birth date in the format: YYYY-MM-DD";
$lang['Namesilo.!tooltip.FRRegistrantLegalId'] = "The SIREN number is the first part of the SIRET NUMBER and consists of 9 digits. The SIRET number is a unique identification number with 14 digits.";
$lang['Namesilo.!tooltip.FRRegistrantDunsNumber'] = "The DUNS number consists of 9 digits, issued by Dun & Bradstreet.";
$lang['Namesilo.!tooltip.FRRegistrantJoDateDec'] = "French associations listed with the Journal Officiel de la République Francaise should set a declaration date in the format: YYYY-MM-DD";
$lang['Namesilo.!tooltip.FRRegistrantJoDatePub'] = "Enter the publication date in the Journal Officiel in the format: YYYY-MM-DD";

$lang['Namesilo.domain.FRLegalType'] = "Legal Type";
$lang['Namesilo.domain.FRLegalType.individual'] = "Individual";
$lang['Namesilo.domain.FRLegalType.company'] = "Company";
$lang['Namesilo.domain.FRRegistrantBirthDate'] = "Birth Date";
$lang['Namesilo.domain.FRRegistrantBirthplace'] = "Birth Place";
$lang['Namesilo.domain.FRRegistrantLegalId'] = "SIREN/SIRET Number";
$lang['Namesilo.domain.FRRegistrantTradeNumber'] = "Trademark Number";
$lang['Namesilo.domain.FRRegistrantDunsNumber'] = "DUNS Number";
$lang['Namesilo.domain.FRRegistrantLocalId'] = "European Economic Area Local ID";
$lang['Namesilo.domain.FRRegistrantJoDateDec'] = "The Journal Official Declaration Date";
$lang['Namesilo.domain.FRRegistrantJoDatePub'] = "The Journal Official Publication Date";
$lang['Namesilo.domain.FRRegistrantJoNumber'] = "The Journal Official Number";
$lang['Namesilo.domain.FRRegistrantJoPage'] = "The Journal Official Announcement Page Number";



// Errors
$lang['Namesilo.!error.FRLegalType.format'] = "Please select a valid Legal Type";
$lang['Namesilo.!error.FRRegistrantBirthDate.format'] = "Please set your birth date in the format: YYYY-MM-DD";
$lang['Namesilo.!error.FRRegistrantBirthplace.format'] = "Please set your birth place.";
$lang['Namesilo.!error.FRRegistrantLegalId.format'] = "Please set your SIREN/SIRET Number";
$lang['Namesilo.!error.FRRegistrantTradeNumber.format'] = "Please set your Trademark Number.";
$lang['Namesilo.!error.FRRegistrantDunsNumber.format'] = "Please set your DUNS Number.";
$lang['Namesilo.!error.FRRegistrantLocalId.format'] = "Please set your EEA Local ID.";
$lang['Namesilo.!error.FRRegistrantJoDateDec.format'] = "Please set the Journal Declaration Date in the format: YYYY-MM-DD";
$lang['Namesilo.!error.FRRegistrantJoDatePub.format'] = "Please set the Journal Publication Date in the format: YYYY-MM-DD";
$lang['Namesilo.!error.FRRegistrantJoNumber.format'] = "Please set the Journal Number.";
$lang['Namesilo.!error.FRRegistrantJoPage.format'] = "Please set the Journal Announcement Page Number.";
?>