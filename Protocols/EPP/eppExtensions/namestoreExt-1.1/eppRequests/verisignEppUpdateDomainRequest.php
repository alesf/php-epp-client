<?php
namespace Metaregistrar\EPP;

class verisignEppUpdateDomainRequest extends eppUpdateDomainRequest
{
    use verisignEppRequestTrait;

    function __construct($objectname, $addinfo = null, $removeinfo = null, $updateinfo = null, $forcehostattr=false, $namespacesinroot=true) {
        $this->setUseCdata(true);

        parent::__construct($objectname, $addinfo, $removeinfo, $updateinfo, $forcehostattr, $namespacesinroot);
    }
}
