<?php
namespace Metaregistrar\EPP;

class euridEppConnection extends eppConnection
{
    public function __construct($logging = false, $settingsfile = null)
    {
        parent::__construct($logging, $settingsfile);
        //parent::enableDnssec();
        parent::setServices(array(
            'urn:ietf:params:xml:ns:domain-1.0' => 'domain',
            'urn:ietf:params:xml:ns:contact-1.0' => 'contact',
            // 'http://www.eurid.eu/xml/epp/contact-ext-1.1' => 'contact-ext',
            // 'http://www.eurid.eu/xml/epp/registrar-1.0' => 'registrar',
        ));
        // parent::addextension('registrar', 'http://www.eurid.eu/xml/epp/registrar-1.0');
        parent::addExtension('contact-ext', 'http://www.eurid.eu/xml/epp/contact-ext-1.1');
        parent::addExtension('domain-ext', 'http://www.eurid.eu/xml/epp/domain-ext-2.1');
        // parent::addExtension('nsgroup', 'http://www.eurid.eu/xml/epp/nsgroup-1.1');
        // parent::addExtension('authInfo', 'http://www.eurid.eu/xml/epp/authInfo-1.0');
        // parent::addExtension('idn', 'http://www.eurid.eu/xml/epp/idn-1.0');
        parent::addCommandResponse('Metaregistrar\EPP\euridEppCreateContactRequest', 'Metaregistrar\EPP\eppCreateContactResponse');
        parent::addCommandResponse('Metaregistrar\EPP\euridEppInfoDomainRequest', 'Metaregistrar\EPP\euridEppInfoDomainResponse');
        parent::addCommandResponse('Metaregistrar\EPP\euridEppDeleteRequest', 'Metaregistrar\EPP\eppDeleteResponse');
    }
}
