<?php

#
# EPP Verification Extension
# https://github.com/nic-at/epp-verification-extension
#

$this->addExtension('verification', 'http://www.nic.at/xsd/at-ext-verification-1.0');

include_once(dirname(__FILE__) . '/eppData/eppVerificationReport.php');
include_once(dirname(__FILE__) . '/eppRequests/siEppCreateContactVerificationRequest.php');
include_once(dirname(__FILE__) . '/eppRequests/siEppUpdateContactVerificationRequest.php');
include_once(dirname(__FILE__) . '/eppResponses/siEppInfoContactVerificationResponse.php');
include_once(dirname(__FILE__) . '/eppResponses/siEppInfoDomainVerificationResponse.php');

$this->addCommandResponse('Metaregistrar\EPP\siEppCreateContactVerificationRequest', 'Metaregistrar\EPP\eppCreateContactResponse');
$this->addCommandResponse('Metaregistrar\EPP\siEppUpdateContactVerificationRequest', 'Metaregistrar\EPP\eppUpdateContactResponse');
$this->addCommandResponse('Metaregistrar\EPP\eppInfoContactRequest', 'Metaregistrar\EPP\siEppInfoContactVerificationResponse');
$this->addCommandResponse('Metaregistrar\EPP\eppInfoDomainRequest', 'Metaregistrar\EPP\siEppInfoDomainVerificationResponse');
