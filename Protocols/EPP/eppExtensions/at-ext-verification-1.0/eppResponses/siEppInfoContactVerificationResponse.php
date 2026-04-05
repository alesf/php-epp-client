<?php
namespace Metaregistrar\EPP;

class siEppInfoContactVerificationResponse extends siEppInfoContactResponse
{
    /**
     * @return eppVerificationReport|null
     */
    public function getVerificationReport()
    {
        $xpath = $this->xPath();
        $xpath->registerNamespace('verification', eppVerificationReport::VERIFICATION_NAMESPACE);

        $result = $xpath->query('/epp:epp/epp:response/epp:extension/verification:infData/verification:report');
        if (!is_null($result) && $result->length > 0) {
            $verificationReport = new eppVerificationReport();
            $verificationReport->setReceivedDate($result->item(0)->getAttribute('receivedDate'));
            $verificationReport->setClID($result->item(0)->getAttribute('clID'));

            $resultEl = $result->item(0)->getElementsByTagName('result');
            if ($resultEl->length > 0) {
                $verificationReport->setResult($resultEl->item(0)->nodeValue);
            }
            $dateEl = $result->item(0)->getElementsByTagName('verificationDate');
            if ($dateEl->length > 0) {
                $verificationReport->setVerificationDate($dateEl->item(0)->nodeValue);
            }
            $methodEl = $result->item(0)->getElementsByTagName('method');
            if ($methodEl->length > 0) {
                $verificationReport->setMethod($methodEl->item(0)->nodeValue);
            }
            $referenceEl = $result->item(0)->getElementsByTagName('reference');
            if ($referenceEl->length > 0) {
                $verificationReport->setReference($referenceEl->item(0)->nodeValue);
            }
            $agentEl = $result->item(0)->getElementsByTagName('agent');
            if ($agentEl->length > 0) {
                $verificationReport->setAgent($agentEl->item(0)->nodeValue);
            }

            return $verificationReport;
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getVerificationStatus()
    {
        $xpath = $this->xPath();
        $xpath->registerNamespace('verification', eppVerificationReport::VERIFICATION_NAMESPACE);

        $result = $xpath->query('/epp:epp/epp:response/epp:extension/verification:infData/verification:status/@s');
        if (!is_null($result) && $result->length > 0) {
            return $result->item(0)->nodeValue;
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getVerificationActionDate()
    {
        $xpath = $this->xPath();
        $xpath->registerNamespace('verification', eppVerificationReport::VERIFICATION_NAMESPACE);

        $result = $xpath->query('/epp:epp/epp:response/epp:extension/verification:infData/verification:actionDate');
        if (!is_null($result) && $result->length > 0) {
            return $result->item(0)->nodeValue;
        }

        return null;
    }
}
