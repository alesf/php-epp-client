<?php
namespace Metaregistrar\EPP\VeriSign;

class verisignEppCheckContactRequest extends eppCheckContactRequest {
    use verisignEppExtension;
    public function __construct(eppContactHandle $contact) {
        parent::__construct($contact);
        //add namestore extension
        $this->addNamestore();
        $this->addSessionId();

    }
}
