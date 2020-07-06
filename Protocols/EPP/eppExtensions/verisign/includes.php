<?php
$this->addService('registry', 'http://www.verisign.com/epp/registry-1.0');
//$this->addService('lowbalance-poll', 'http://www.verisign.com/epp/lowbalance-poll-1.0');
$this->addService('rgp-poll', 'http://www.verisign.com/epp/rgp-poll-1.0');
$this->addExtension('rgp','urn:ietf:params:xml:ns:rgp-1.0');
//$this->addExtension('whoisInf', 'http://www.verisign.com/epp/whoisInf-1.0');
//$this->addExtension('idnLang', 'http://www.verisign.com/epp/idnLang-1.0');
//$this->addExtension('coa', 'urn:ietf:params:xml:ns:coa-1.0');
$this->addExtension('namestore-ext', 'http://www.verisign-grs.com/epp/namestoreExt-1.1');
//$this->addExtension('sync', 'http://www.verisign.com/epp/sync-1.0');
//$this->addExtension('relatedDomain', 'http://www.verisign.com/epp/relatedDomain-1.0');
$this->addExtension('verificationCode', 'urn:ietf:params:xml:ns:verificationCode-1.0');
$this->addExtension('changePoll', 'urn:ietf:params:xml:ns:changePoll-1.0');
//$this->addExtension('loginSec', 'urn:ietf:params:xml:ns:epp:loginSec-0.4');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppExtension.php');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppCheckDomainRequest.php');
$this->addCommandResponse('Metaregistrar\EPP\VeriSign\verisignEppCheckDomainRequest', 'Metaregistrar\EPP\eppCheckDomainResponse');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppInfoDomainRequest.php');
include_once(dirname(__FILE__) . '/eppResponses/verisignEppInfoDomainResponse.php');
$this->addCommandResponse('Metaregistrar\EPP\VeriSign\verisignEppInfoDomainRequest', 'Metaregistrar\EPP\VeriSign\verisignEppInfoDomainResponse');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppCreateDomainRequest.php');
$this->addCommandResponse('Metaregistrar\EPP\VeriSign\verisignEppCreateDomainRequest', 'Metaregistrar\EPP\eppCreateDomainResponse');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppDeleteDomainRequest.php');
$this->addCommandResponse('Metaregistrar\EPP\VeriSign\verisignEppDeleteDomainRequest', 'Metaregistrar\EPP\eppDeleteResponse');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppRenewDomainRequest.php');
$this->addCommandResponse('Metaregistrar\EPP\VeriSign\verisignEppRenewDomainRequest', 'Metaregistrar\EPP\eppRenewResponse');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppUpdateDomainRequest.php');
$this->addCommandResponse('Metaregistrar\EPP\VeriSign\verisignEppUpdateDomainRequest', 'Metaregistrar\EPP\eppUpdateDomainResponse');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppRealNameDomainRequest.php');
$this->addCommandResponse('Metaregistrar\EPP\VeriSign\verisignEppRealNameDomainRequest', 'Metaregistrar\EPP\eppUpdateDomainResponse');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppTransferDomainRequest.php');
$this->addCommandResponse('Metaregistrar\EPP\VeriSign\verisignEppTransferDomainRequest', 'Metaregistrar\EPP\eppTransferResponse');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppRestoreDomainRequest.php');
include_once(dirname(__FILE__) . '/eppResponses/verisignEppRestoreDomainResponse.php');
$this->addCommandResponse('Metaregistrar\EPP\VeriSign\verisignEppRestoreDomainRequest', 'Metaregistrar\EPP\VeriSign\verisignEppRestoreDomainResponse');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppCheckContactRequest.php');
$this->addCommandResponse('Metaregistrar\EPP\VeriSign\verisignEppCheckContactRequest', 'Metaregistrar\EPP\eppCheckContactResponse');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppInfoContactRequest.php');
$this->addCommandResponse('Metaregistrar\EPP\VeriSign\verisignEppInfoContactRequest', 'Metaregistrar\EPP\eppInfoContactResponse');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppCreateContactRequest.php');
$this->addCommandResponse('Metaregistrar\EPP\VeriSign\verisignEppCreateContactRequest', 'Metaregistrar\EPP\eppCreateContactResponse');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppDeleteContactRequest.php');
$this->addCommandResponse('Metaregistrar\EPP\VeriSign\verisignEppDeleteContactRequest', 'Metaregistrar\EPP\eppDeleteResponse');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppUpdateContactRequest.php');
$this->addCommandResponse('Metaregistrar\EPP\VeriSign\verisignEppUpdateContactRequest', 'Metaregistrar\EPP\eppUpdateContactResponse');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppCheckHostRequest.php');
$this->addCommandResponse('Metaregistrar\EPP\VeriSign\verisignEppCheckHostRequest', 'Metaregistrar\EPP\eppCheckHostResponse');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppInfoHostRequest.php');
$this->addCommandResponse('Metaregistrar\EPP\VeriSign\verisignEppInfoHostRequest', 'Metaregistrar\EPP\eppInfoHostResponse');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppCreateHostRequest.php');
$this->addCommandResponse('Metaregistrar\EPP\VeriSign\verisignEppCreateHostRequest', 'Metaregistrar\EPP\eppCreateHostResponse');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppDeleteHostRequest.php');
$this->addCommandResponse('Metaregistrar\EPP\VeriSign\verisignEppDeleteHostRequest', 'Metaregistrar\EPP\eppDeleteResponse');

include_once(dirname(__FILE__) . '/eppRequests/verisignEppUpdateHostRequest.php');
$this->addCommandResponse('Metaregistrar\EPP\VeriSign\verisignEppUpdateHostRequest', 'Metaregistrar\EPP\eppUpdateHostResponse');

include_once(dirname(__FILE__) . '/eppResponses/verisignEppPollResponse.php');
$this->addCommandResponse('Metaregistrar\EPP\eppPollRequest', 'Metaregistrar\EPP\VeriSign\verisignEppPollResponse');
