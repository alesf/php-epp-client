<?php

#
# For use with the Verisign connection
#

include_once(dirname(__FILE__) . '/eppData/verisignEppDomainIDN.php');
include_once(dirname(__FILE__) . '/eppData/verisignEppHostIDN.php');
include_once(dirname(__FILE__) . '/eppRequests/verisignEppCreateDomainIDNRequest.php');


$this->addCommandResponse('Metaregistrar\EPP\verisignEppCreateDomainIDNRequest', 'Metaregistrar\EPP\eppCreateDomainResponse');

