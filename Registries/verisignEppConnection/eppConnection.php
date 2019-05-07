<?php
namespace Metaregistrar\EPP;

class verisignEppConnection extends eppConnection
{
    public function __construct($logging = false, $settingsfile = null)
    {
        parent::__construct($logging, $settingsfile);

        parent::setLanguage('en');
        parent::setVersion('1.0');

        parent::addService('registry', 'http://www.verisign.com/epp/registry-1.0');
        parent::addService('lowbalance-poll', 'http://www.verisign.com/epp/lowbalance-poll-1.0');
        parent::addService('rgp-poll', 'http://www.verisign.com/epp/rgp-poll-1.0');

        parent::useExtension("namestoreExt-1.1");
        parent::useExtension("idnLang-1.0");
        // parent::useExtension("thinRegistrar");

        parent::enableLaunchphase('claim');
        parent::enableDnssec();
        parent::enableRgp();
        parent::addExtension("whoisInf", "http://www.verisign.com/epp/whoisInf-1.0");
        parent::addExtension("idnLang", "http://www.verisign.com/epp/idnLang-1.0");
        parent::addExtension("namestoreExt", "http://www.verisign-grs.com/epp/namestoreExt-1.1");
        parent::addExtension("sync", "http://www.verisign.com/epp/sync-1.0");
        parent::addExtension("relatedDomain", "http://www.verisign.com/epp/relatedDomain-1.0");

        // <extURI>urn:ietf:params:xml:ns:coa-1.0</extURI>
        // <extURI>urn:ietf:params:xml:ns:verificationCode-1.0</extURI>
        // <extURI>urn:ietf:params:xml:ns:launch-1.0</extURI>
        // <extURI>urn:ietf:params:xml:ns:changePoll-1.0</extURI>
    }

    public function getNamestoreExtExtension($subProduct = 'dotCOM')
    {
        $this->namestoreextension = $this->createElement('namestoreExt:namestoreExt');
        $subProduct = $this->createElement('namestoreExt:subProduct', $subProduct);
        $this->namestoreextension->appendChild($subProduct);
        $this->namestoreextension->setAttribute('xmlns:namestoreExt', 'http://www.verisign-grs.com/epp/namestoreExt-1.1');

        return $this->namestoreextension;
    }
}
