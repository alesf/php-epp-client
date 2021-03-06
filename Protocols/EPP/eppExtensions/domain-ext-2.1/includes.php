<?php
$this->addExtension('domain-ext', 'http://www.eurid.eu/xml/epp/domain-ext-2.1');

include_once(dirname(__FILE__) . '/eppRequests/euridEppDeleteRequest.php');
include_once(dirname(__FILE__) . '/eppRequests/euridEppTransferDomainRequest.php');

$this->addCommandResponse('Metaregistrar\EPP\euridEppTransferDomainRequest', 'Metaregistrar\EPP\eppTransferResponse');
$this->addCommandResponse('Metaregistrar\EPP\euridEppDeleteRequest', 'Metaregistrar\EPP\euridEppDeleteResponse');
