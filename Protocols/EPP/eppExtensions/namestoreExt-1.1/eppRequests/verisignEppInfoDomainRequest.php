<?php
namespace Metaregistrar\EPP;

class verisignEppInfoDomainRequest extends eppInfoDomainRequest
{
    use verisignEppRequestTrait;

    function __construct($infodomain, $hosts = null, $namespacesinroot = true) {
        $this->setUseCdata(true);

        parent::__construct($infodomain, $hosts, $namespacesinroot);
    }
}
