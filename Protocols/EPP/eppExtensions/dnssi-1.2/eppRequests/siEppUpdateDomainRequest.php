<?php
namespace Metaregistrar\EPP;

class siEppUpdateDomainRequest extends eppUpdateDomainRequest
{
    public function __construct($updateinfo)
    {
        parent::__construct($updateinfo);
    }

    public function dnsCheck($check = true)
    {
        $dnscheck = $this->createElement('dnsCheck:create', $check);
        $this->domainobject->appendChild($dnscheck);
    }
}
