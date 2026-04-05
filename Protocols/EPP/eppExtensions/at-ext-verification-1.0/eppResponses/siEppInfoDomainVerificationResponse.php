<?php
namespace Metaregistrar\EPP;

class siEppInfoDomainVerificationResponse extends eppInfoDomainResponse
{
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
