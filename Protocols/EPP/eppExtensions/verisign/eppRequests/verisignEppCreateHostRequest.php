<?php
namespace Metaregistrar\EPP\VeriSign;

class verisignEppCreateHostRequest extends eppCreateHostRequest {
    use verisignEppExtension;
    /**
     * verisignEppCreateHostRequest constructor.
     *
     * @param eppHost $host
     * @throws eppException
     */
    public function __construct(eppHost $host) {
        parent::__construct($host);
        //add namestore extension
        $this->addNamestore();
        $this->addSessionId();
    }
}
