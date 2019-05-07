<?php
namespace Metaregistrar\EPP;

class verisignEppDomainIDN extends eppDomain {

    public function __construct($domainname, $registrant = null, $contacts = null, $hosts = null, $period = 0, $authorisationCode = null) {
        $domainname = idn_to_ascii($domainname, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
        parent::__construct($domainname, $registrant, $contacts, $hosts, $period, $authorisationCode);
    }
    
}