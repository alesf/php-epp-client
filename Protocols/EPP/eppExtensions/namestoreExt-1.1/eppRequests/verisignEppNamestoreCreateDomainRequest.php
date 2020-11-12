<?php
namespace Metaregistrar\EPP;

class verisignEppNamestoreCreateDomainRequest extends eppCreateDomainRequest
{
    use verisignEppRequestTrait;

    public $thin = true;
}
