<?php

#
# For use with the Verisign connection
#

include_once(dirname(__FILE__) . '/eppRequests/verisignEppRequestTrait.php');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppCheckContactRequest.php');
include_once(dirname(__FILE__) . '/eppRequests/verisignEppCreateContactRequest.php');
include_once(dirname(__FILE__) . '/eppRequests/verisignEppDeleteContactRequest.php');
include_once(dirname(__FILE__) . '/eppRequests/verisignEppInfoContactRequest.php');
include_once(dirname(__FILE__) . '/eppRequests/verisignEppUpdateContactRequest.php');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppCheckDomainRequest.php');
include_once(dirname(__FILE__) . '/eppRequests/verisignEppNamestoreCreateDomainRequest.php');
include_once(dirname(__FILE__) . '/eppRequests/verisignEppDeleteDomainRequest.php');
include_once(dirname(__FILE__) . '/eppRequests/verisignEppInfoDomainRequest.php');
include_once(dirname(__FILE__) . '/eppRequests/verisignEppUpdateDomainRequest.php');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppCheckRequest.php');
include_once(dirname(__FILE__) . '/eppRequests/verisignEppCheckHostRequest.php');
include_once(dirname(__FILE__) . '/eppRequests/verisignEppCreateHostRequest.php');
include_once(dirname(__FILE__) . '/eppRequests/verisignEppDeleteHostRequest.php');
include_once(dirname(__FILE__) . '/eppRequests/verisignEppInfoHostRequest.php');
include_once(dirname(__FILE__) . '/eppRequests/verisignEppUpdateHostRequest.php');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppTransferRequest.php');

$this->addCommandResponse('Metaregistrar\EPP\verisignEppCheckContactRequest', 'Metaregistrar\EPP\eppCheckContactResponse');
$this->addCommandResponse('Metaregistrar\EPP\verisignEppCreateContactRequest', 'Metaregistrar\EPP\eppCreateContactResponse');
$this->addCommandResponse('Metaregistrar\EPP\verisignEppDeleteContactRequest', 'Metaregistrar\EPP\eppDeleteResponse');
$this->addCommandResponse('Metaregistrar\EPP\verisignEppInfoContactRequest', 'Metaregistrar\EPP\eppInfoContactResponse');
$this->addCommandResponse('Metaregistrar\EPP\verisignEppUpdateContactRequest', 'Metaregistrar\EPP\eppUpdateContactResponse');

$this->addCommandResponse('Metaregistrar\EPP\verisignEppCheckDomainRequest', 'Metaregistrar\EPP\eppCheckDomainResponse');
$this->addCommandResponse('Metaregistrar\EPP\verisignEppNamestoreCreateDomainRequest', 'Metaregistrar\EPP\eppCreateDomainResponse');
$this->addCommandResponse('Metaregistrar\EPP\verisignEppDeleteDomainRequest', 'Metaregistrar\EPP\eppDeleteResponse');
$this->addCommandResponse('Metaregistrar\EPP\verisignEppInfoDomainRequest', 'Metaregistrar\EPP\eppInfoDomainResponse');
$this->addCommandResponse('Metaregistrar\EPP\verisignEppUpdateDomainRequest', 'Metaregistrar\EPP\eppUpdateDomainResponse');

$this->addCommandResponse('Metaregistrar\EPP\verisignEppCheckRequest', 'Metaregistrar\EPP\eppCheckResponse');
$this->addCommandResponse('Metaregistrar\EPP\verisignEppCheckHostRequest', 'Metaregistrar\EPP\eppCheckHostResponse');
$this->addCommandResponse('Metaregistrar\EPP\verisignEppCreateHostRequest', 'Metaregistrar\EPP\eppCreateHostResponse');
$this->addCommandResponse('Metaregistrar\EPP\verisignEppDeleteHostRequest', 'Metaregistrar\EPP\eppDeleteResponse');
$this->addCommandResponse('Metaregistrar\EPP\verisignEppInfoHostRequest', 'Metaregistrar\EPP\eppInfoHostResponse');
$this->addCommandResponse('Metaregistrar\EPP\verisignEppUpdateHostRequest', 'Metaregistrar\EPP\eppUpdateHostResponse');
$this->addCommandResponse('Metaregistrar\EPP\verisignEppRenewRequest', 'Metaregistrar\EPP\eppRenewResponse');

$this->addCommandResponse('Metaregistrar\EPP\verisignEppTransferRequest', 'Metaregistrar\EPP\eppTransferResponse');
