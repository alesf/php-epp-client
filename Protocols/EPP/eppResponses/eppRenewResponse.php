<?php
namespace Metaregistrar\EPP;

class eppRenewResponse extends eppResponse {
    public function __construct() {
        parent::__construct();
    }

    public function __destruct() {
        parent::__destruct();
    }

    #
    # DOMAIN RENEW RESPONSES
    #

    public function getDomainName() {
        return $this->queryPath('/epp:epp/epp:response/epp:resData/domain:renData/domain:name');
    }

    public function getDomainExpirationDate() {
        return $this->queryPath('/epp:epp/epp:response/epp:resData/domain:renData/domain:exDate');
    }
}
