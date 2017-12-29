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
        parent::setLanguage('en-US');
        parent::setVersion('1.0');
        parent::enableDnssec();

        parent::useExtension("dnssi-1.2");
        parent::useExtension("registrar-1.0");
        // parent::useExtension("DNScheck");

        // These extensions are not supported yet but will be sent to the registry
        parent::addExtension("DNScheck", "http://www.arnes.si/xml/epp/DNScheck-1.0");
    }

    /**
     * Performs an EPP logout and checks the result
     * @return bool
     * @throws eppException
     */
    public function logout()
    {
        $logout = new siEppLogoutRequest();
        if ($response = $this->request($logout)) {
            $this->writeLog("Logged out", "LOGOUT");
            $this->loggedin = false;
            return true;
        } else {
            throw new eppException("Logout failed: ".$response->getResultMessage(), 0, null, null, $logout->saveXML());
        }
    }
}
