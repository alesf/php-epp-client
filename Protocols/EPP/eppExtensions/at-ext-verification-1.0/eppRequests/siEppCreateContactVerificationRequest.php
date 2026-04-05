<?php
namespace Metaregistrar\EPP;

class siEppCreateContactVerificationRequest extends siEppCreateContactRequest
{
    /**
     * @var eppVerificationReport|null
     */
    private $verificationReport;

    /**
     * @param eppContact $createinfo
     * @param eppVerificationReport|null $verificationReport
     */
    public function __construct($createinfo, $verificationReport = null)
    {
        $this->verificationReport = $verificationReport;
        parent::__construct($createinfo);

        if ($this->verificationReport) {
            $this->addVerificationExtension();
        }
    }

    private function addVerificationExtension()
    {
        $verificationExt = $this->createElement('verification:create');
        $verificationExt->setAttribute('xmlns:verification', eppVerificationReport::VERIFICATION_NAMESPACE);
        $verificationExt->setAttribute('xsi:schemaLocation', eppVerificationReport::VERIFICATION_SCHEMA_LOCATION);
        $this->verificationReport->exportXML($this, $verificationExt);
        $this->getExtension()->appendChild($verificationExt);
    }
}
