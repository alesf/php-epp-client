<?php

#
# For use with the SI connection
#

$this->addExtension("dnssi", "http://www.arnes.si/xml/epp/dnssi-1.2");
$this->addExtension("DNScheck", "http://www.arnes.si/xml/epp/DNScheck-1.0");

include_once(dirname(__FILE__) . '/eppData/siEppContactPostalInfo.php');
include_once(dirname(__FILE__) . '/eppRequests/siEppCreateContactRequest.php');
include_once(dirname(__FILE__) . '/eppRequests/siEppCreateDomainRequest.php');
include_once(dirname(__FILE__) . '/eppRequests/siEppUpdateDomainRequest.php');
include_once(dirname(__FILE__) . '/eppRequests/siEppLogoutRequest.php');
include_once(dirname(__FILE__) . '/eppResponses/siEppInfoContactResponse.php');

$this->addCommandResponse('Metaregistrar\EPP\siEppCreateDomainRequest', 'Metaregistrar\EPP\eppCreateDomainResponse');
$this->addCommandResponse('Metaregistrar\EPP\siEppUpdateDomainRequest', 'Metaregistrar\EPP\eppUpdateDomainResponse');
$this->addCommandResponse('Metaregistrar\EPP\siEppCreateContactRequest', 'Metaregistrar\EPP\eppCreateContactResponse');
$this->addCommandResponse('Metaregistrar\EPP\siEppLogoutRequest', 'Metaregistrar\EPP\eppLogoutResponse');
$this->addCommandResponse('Metaregistrar\EPP\eppInfoContactRequest', 'Metaregistrar\EPP\siEppInfoContactResponse');
