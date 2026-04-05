<?php
namespace Metaregistrar\EPP;

class siEppCreateContactRequest extends eppCreateContactRequest
{
    /**
     * @var siEppVerificationReport|null
     */
    private $verificationReport;

    /**
     * @param eppContact $createinfo
     * @param siEppVerificationReport|null $verificationReport
     */
    public function __construct($createinfo, $verificationReport = null)
    {
        $this->verificationReport = $verificationReport;
        parent::__construct($createinfo);

        if ($createinfo instanceof eppContact) {
            $this->addExtension('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            $this->addExtension('xmlns:dnssi', 'http://www.arnes.si/xml/epp/dnssi-1.2');
            $this->addDnssiExtension($createinfo);
        }
        if ($this->verificationReport) {
            $this->addVerificationExtension();
        }
        $this->addSessionId();
    }

    private function addVerificationExtension()
    {
        $verificationExt = $this->createElement('verification:create');
        $verificationExt->setAttribute('xmlns:verification', siEppVerificationReport::VERIFICATION_NAMESPACE);
        $verificationExt->setAttribute('xsi:schemaLocation', siEppVerificationReport::VERIFICATION_SCHEMA_LOCATION);
        $this->verificationReport->exportXML($this, $verificationExt);
        $this->getExtension()->appendChild($verificationExt);
    }

    private function addDnssiExtension(eppContact $contact)
    {
        $postalInfo = $contact->getPostalInfo(0);

        /* @var $postalInfo \Metaregistrar\EPP\siEppContactPostalInfo */
        if ($postalInfo) {
            $dnssiext = $this->createElement('dnssi:ext');
            $create = $this->createElement('dnssi:create');
            $contact = $this->createElement('dnssi:contact');
            $typeAttribute = $this->createAttribute('type');
            $typeAttribute->value = $postalInfo->getContactType();
            $contact->appendChild($typeAttribute);
            $create->appendChild($contact);

            /* Tega ni vec v novem*/
            /*if($postalInfo->getContactID()) {
                $id = $this->createElement('dnssi:'.$postalInfo->getIDType(), $postalInfo->getContactID());
                $create->appendChild($id);
            }*/

            $dnssiext->appendChild($create);
            $this->getExtension()->appendChild($dnssiext);
        }
    }

    /**
     *
     * @param eppContact $contact
     * @throws eppException
     */
    public function setContact(eppContact $contact) {
        #
        # Object create structure
        #
        $this->setContactId($contact->getId());
        $this->setPostalInfo($contact->getPostalInfo(0));
        if ($contact->getPostalInfoLength() == 2) {
            $this->setPostalInfo($contact->getPostalInfo(1));
        }
        $this->setVoice($contact->getVoice());
        $this->setFax($contact->getFax());
        $this->setEmail($contact->getEmail());
        $this->setPassword($contact->getPassword());
        $this->setDisclose($contact->getDisclose());
    }
}
