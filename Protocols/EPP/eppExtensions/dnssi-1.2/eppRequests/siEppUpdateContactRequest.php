<?php
namespace Metaregistrar\EPP;

class siEppUpdateContactRequest extends eppUpdateContactRequest
{
    /**
     * @var siEppVerificationReport|null
     */
    private $verificationReport;

    /**
     * @param eppContactHandle|string $objectname
     * @param eppContact|null $addinfo
     * @param eppContact|null $removeinfo
     * @param eppContact|null $updateinfo
     * @param siEppVerificationReport|null $verificationReport
     */
    public function __construct($objectname, $addinfo = null, $removeinfo = null, $updateinfo = null, $verificationReport = null)
    {
        $this->verificationReport = $verificationReport;
        parent::__construct($objectname, $addinfo, $removeinfo, $updateinfo);

        if ($this->verificationReport) {
            $this->addVerificationExtension();
        }
        $this->addSessionId();
    }

    private function addVerificationExtension()
    {
        $this->addExtension('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');

        $verificationExt = $this->createElement('verification:update');
        $verificationExt->setAttribute('xmlns:verification', siEppVerificationReport::VERIFICATION_NAMESPACE);
        $verificationExt->setAttribute('xsi:schemaLocation', siEppVerificationReport::VERIFICATION_SCHEMA_LOCATION);
        $this->verificationReport->exportXML($this, $verificationExt);
        $this->getExtension()->appendChild($verificationExt);
    }
}
