<?php
namespace Metaregistrar\EPP;

class verisignEppCreateDomainRequest extends eppCreateDomainRequest
{
    use verisignEppRequestTrait;

    public $thin = true;

    /**
     *
     * @param eppDomain $domain
     * @return \DOMElement
     * @throws eppException
     */
    public function setDomain(eppDomain $domain)
    {
        if (!strlen($domain->getDomainname())) {
            throw new eppException('No valid domain name in create domain request');
        }
        if (!$this->thin && !strlen($domain->getRegistrant())) {
            throw new eppException('No valid registrant in create domain request');
        }
        #
        # Object create structure
        #
        $this->domainobject->appendChild($this->createElement('domain:name', $domain->getDomainname()));
        if ($domain->getPeriod() > 0) {
            $domainperiod = $this->createElement('domain:period', $domain->getPeriod());
            $domainperiod->setAttribute('unit', $domain->getPeriodUnit());
            $this->domainobject->appendChild($domainperiod);
        }
        $nsobjects = $domain->getHosts();
        if ($domain->getHostLength() > 0) {
            $nameservers = $this->createElement('domain:ns');
            foreach ($nsobjects as $nsobject) {
                /* @var $nsobject eppHost */
                if (($this->getForcehostattr()) || ($nsobject->getIpAddressCount() > 0)) {
                    $nameservers->appendChild($this->addDomainHostAttr($nsobject));
                } else {
                    $nameservers->appendChild($this->addDomainHostObj($nsobject));
                }
            }
            $this->domainobject->appendChild($nameservers);
        }
        $this->domainobject->appendChild($this->createElement('domain:registrant', $domain->getRegistrant()));
        $contacts = $domain->getContacts();
        if ($domain->getContactLength() > 0) {
            foreach ($contacts as $contact) {
                /* @var $contact eppContactHandle */
                $this->addDomainContact($this->domainobject, $contact->getContactHandle(), $contact->getContactType());
            }
        }
        if (strlen($domain->getAuthorisationCode())) {
            $authinfo = $this->createElement('domain:authInfo');
            $authinfo->appendChild($this->createElement('domain:pw', $domain->getAuthorisationCode()));
            $this->domainobject->appendChild($authinfo);
        }

        // Check for DNSSEC keys and add them
        if ($domain->getSecdnsLength() > 0) {
            for ($i = 0; $i < $domain->getSecdnsLength(); $i++) {
                $sd = $domain->getSecdns($i);
                /* @var $sd eppSecdns */
                if ($sd) {
                    $ext = new eppSecdns();
                    $ext->copy($sd);
                    $this->addSecdns($ext);
                }
            }
        }
        return;
    }
}
