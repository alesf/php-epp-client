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
        $this->addEURIDExtension($createinfo);
        $this->addSessionId();
    }

    public function addEURIDExtension($createinfo)
    {
        $ext = $this->createElement('extension');
        $contactext = $this->createElement('contact-ext:create');
        $contactext->setAttribute('xmlns:contact-ext', 'http://www.eurid.eu/xml/epp/contact-ext-1.1');
        $contactext->appendChild($this->createElement('contact-ext:type', $createinfo->getExtType()));
        $contactext->appendChild($this->createElement('contact-ext:vat', $createinfo->getExtVat()));
        $contactext->appendChild($this->createElement('contact-ext:lang', $createinfo->getExtLang()));
        $ext->appendChild($contactext);
        $this->getCommand()->appendChild($ext);
    }
}
