<?php
namespace Metaregistrar\EPP;

class verisignEppCreateDomainRequest extends verisignEppNamestoreCreateDomainRequest
{
    
    protected $idnlangextension = null;
    protected $idnlang = null;

    public function setIDNLang($languuage)
    {
        $this->idnlang = $languuage;
        $this->setIDNLangExtension($languuage);
        $this->addSessionId();
    }

    private function setIDNLangExtension($language)
    {        
        if ($this->getExtension()->getElementsByTagName('idnLang:tag')->count() > 0) 
        {                        
            $this->getExtension()->removeChild($this->getExtension()->getElementsByTagName('idnLang:tag')->item(0));
        }
        $this->idnlangextension = $this->createElement('idnLang:tag', $language);
        $this->idnlangextension->setAttribute('xmlns:idnLang', 'http://www.verisign.com/epp/idnLang-1.0');
        $this->idnlangextension->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $this->idnlangextension->setAttribute('xsi:schemaLocation', 'http://www.verisign-grs.com/epp/idnLang-1.0 idnLang-1.0.xsd"');
        $this->getExtension()->appendChild($this->idnlangextension);
        return $this->idnlangextension;
    }
}
