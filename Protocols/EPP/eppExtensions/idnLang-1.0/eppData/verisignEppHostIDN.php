<?php
namespace Metaregistrar\EPP;

class verisignEppHostIDN extends eppHost {

    public function __construct($hostname, $ipaddress = null, $hoststatus = null) {
        $hostname = idn_to_ascii($hostname, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
        parent::__construct($hostname, $ipaddress, $hoststatus);
    }
    
}