<?php
namespace Metaregistrar\EPP;

/**
 * Class eppRgpRestoreRequest
 */
class eppRgpRestoreRequest extends eppUpdateDomainRequest
{
    /**
     * eppRgpRestoreRequest constructor.
     * @param eppDomain      $objectname
     * @param eppDomain|null $addinfo
     * @param eppDomain|null $removeinfo
     * @param eppDomain|null $updateinfo
     */
    public function __construct(eppDomain $objectname, $addinfo = null, $removeinfo = null, $updateinfo = null, $type = 'request', $reportData = null)
    {
        if ($objectname instanceof eppDomain) {
            $domainname = $objectname->getDomainname();
        } else {
            $domainname = $objectname;
        }
        if ($updateinfo == null) {
            $updateinfo = new eppDomain($domainname);
        }
        parent::__construct($domainname, null, null, $updateinfo);
        $rgp = $this->createElement('rgp:update');
        // $this->addExtension('xmlns:rgp', 'urn:ietf:params:xml:ns:rgp-1.0');
        $rgp->setAttribute('xmlns:rgp', 'urn:ietf:params:xml:ns:rgp-1.0');
        $restore = $this->createElement('rgp:restore');
        $restore->setAttribute('op', $type);
        if ($type == 'report') {
            $report = $this->createElement('rgp:report');
            // $preData = $this->createElement('rgp:preData', '<![CDATA[' . $report['preData'] . ']]>');
            // $postData = $this->createElement('rgp:postData', '<![CDATA[' . $report['postData'] . ']]>');
            $preData = $this->createElement('rgp:preData');
            $preData->appendChild($this->createCDATASection($reportData['preData']));
            $postData = $this->createElement('rgp:postData');
            $postData->appendChild($this->createCDATASection($reportData['postData']));
            $delTime = $this->createElement('rgp:delTime', $reportData['delTime']);
            $resTime = $this->createElement('rgp:resTime', $reportData['resTime']);
            $resReason = $this->createElement('rgp:resReason', 'Registrant error.');
            $statement = $this->createElement('rgp:statement', 'This registrar has not restored the ' . $domainname . ' in order to assume the rights to use or sell the ' . $domainname . ' for itself or for any third party.');
            $statement2 = $this->createElement('rgp:statement', 'The information in this report is true to best of this registrar\'s knowledge, and this registrar acknowledges that intentionally supplying false information in this report shall constitute an incurable material breach of the Registry-Registrar Agreement.');
            $report->appendChild($preData);
            $report->appendChild($postData);
            $report->appendChild($delTime);
            $report->appendChild($resTime);
            $report->appendChild($resReason);
            $report->appendChild($statement);
            $report->appendChild($statement2);
            $restore->appendChild($report);
        }
        $rgp->appendChild($restore);
        $this->getExtension()->appendChild($rgp);
        $this->addSessionId();
    }
}
