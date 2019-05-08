<?php

#
# For use with the Verisign connection
#

include_once(dirname(__FILE__) . '/eppRequests/verisignEppCreateDomainRequest.php');


$this->addCommandResponse('Metaregistrar\EPP\verisignEppCreateDomainRequest', 'Metaregistrar\EPP\eppCreateDomainResponse');

