<?php
namespace Metaregistrar\EPP;

class verisignEppCreateDomainRequest extends eppCreateDomainRequest
{
    use verisignEppRequestTrait;

    public $thin = true;        
}
