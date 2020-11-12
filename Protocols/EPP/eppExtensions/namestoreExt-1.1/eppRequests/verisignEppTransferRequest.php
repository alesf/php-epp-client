<?php
namespace Metaregistrar\EPP;

class verisignEppTransferRequest extends eppTransferRequest
{
    use verisignEppRequestTrait;

    function __construct($operation, $object) {
        $this->setUseCdata(true);

        parent::__construct($operation, $object);
    }
}
