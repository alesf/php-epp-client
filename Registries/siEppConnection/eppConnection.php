<?php
namespace Metaregistrar\EPP;

class siEppConnection extends eppConnection
{
    public function __construct($logging = false, $settingsfile = null)
    {
        parent::__construct($logging, $settingsfile);
        parent::setServices(array(
            'urn:ietf:params:xml:ns:domain-1.0' => 'domain',
            'urn:ietf:params:xml:ns:host-1.0' => 'host',
            'urn:ietf:params:xml:ns:contact-1.0' => 'contact'
        ));
        parent::addExtension("dnssi", "http://www.arnes.si/xml/epp/dnssi-1.2");
        parent::addExtension("registrar", "http://www.arnes.si/xml/epp/registrar-1.0");
        parent::addExtension("DNScheck", "http://www.arnes.si/xml/epp/DNScheck-1.0");
        parent::setLanguage('en-US');
        parent::setVersion('1.0');

        parent::enableDnssec();

        parent::addCommandResponse('Metaregistrar\EPP\siEppCreateDomainRequest', 'Metaregistrar\EPP\eppCreateDomainResponse');
        parent::addCommandResponse('Metaregistrar\EPP\siEppCreateContactRequest', 'Metaregistrar\EPP\eppCreateContactResponse');
    }
}