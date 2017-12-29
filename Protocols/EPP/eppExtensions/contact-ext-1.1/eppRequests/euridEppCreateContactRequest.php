<?php
namespace Metaregistrar\EPP;

/*
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
  <command>
    <create>
      <contact:create>
        <contact:postalInfo>...</contact:postalInfo>
      </contact:create>
    </create>
    <extension>
       <contact-ext:create xmlns:contact-ext="http://www.eurid.eu/xml/epp/contact-ext-1.1">
         <contact-ext:type></contact-ext:type>
         <contact-ext:vat></contact-ext:vat>
         <contact-ext:lang></contact-ext:lang>
       </contact-ext:create>
    </extension>
  </command>
</epp>
*/

class euridEppCreateContactRequest extends eppCreateContactRequest
{
    public function __construct($createinfo, $namespaceinroot = true)
    {
        parent::__construct($createinfo, $namespaceinroot);
        $this->addContactExtension($createinfo);
        $this->addSessionId();
    }

    public function addContactExtension(eppContact $createinfo)
    {
        $this->addExtension('xmlns:contact-ext', 'http://www.eurid.eu/xml/epp/contact-ext-1.1');

        $create = $this->createElement('contact-ext:create');
        // $create->setAttribute('xmlns:contact-ext', 'http://www.eurid.eu/xml/epp/contact-ext-1.1');

        if (!empty($createinfo->getContactExtType())) {
            $create->appendChild($this->createElement('contact-ext:type', $createinfo->getContactExtType()));
        }
        if (!empty($createinfo->getContactExtVat())) {
            $create->appendChild($this->createElement('contact-ext:vat', $createinfo->getContactExtVat()));
        }
        $create->appendChild($this->createElement('contact-ext:lang', $createinfo->getContactExtLang()));

        $this->getExtension()->appendChild($create);
    }
}
