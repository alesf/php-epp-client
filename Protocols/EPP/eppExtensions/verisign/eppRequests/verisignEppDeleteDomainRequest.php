<?php
namespace Metaregistrar\EPP\VeriSign;

class verisignEppDeleteDomainRequest extends eppDeleteDomainRequest {
    use verisignEppExtension;
    /**
     * verisignEppDeleteDomainRequest constructor.
     *
     * @param eppDomain $domain
     */
    public function __construct(eppDomain $domain) {
        parent::__construct($domain);
        //add namestore extension
        $this->addNamestore($domain);
        $this->addSessionId();

    }
}
