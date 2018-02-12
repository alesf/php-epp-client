<?php
namespace Metaregistrar\EPP;

trait verisignEppRequestTrait
{
    protected $namestoreextension = null;
    protected $subProduct = null;

    protected $subProducts = [
        'com' => 'dotCOM',
        'net' => 'dotNET',
        'edu' => 'dotEDU',
        'bz' => 'dotBZ',
        'cc' => 'dotCC',
        'tv' => 'dotTV',
        'jobs' => 'dotJOBS',
    ];

    public function setSubProductSmart($domain)
    {
        list($domainName, $ext) = explode('.', $domain);
        $this->subProduct = $ext;
        $this->setNamestoreExtExtension($ext);
        $this->addSessionId();
    }

    public function setSubProduct($name)
    {
        $this->subProduct = $name;
        $this->setNamestoreExtExtension($name);
        $this->addSessionId();
    }

    private function setNamestoreExtExtension($subProduct)
    {
        $this->namestoreextension = $this->createElement('namestoreExt:namestoreExt');
        $subProductNode = $this->createElement('namestoreExt:subProduct', $subProduct);
        $this->namestoreextension->appendChild($subProductNode);
        $this->namestoreextension->setAttribute('xmlns:namestoreExt', 'http://www.verisign-grs.com/epp/namestoreExt-1.1');
        $this->namestoreextension->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $this->namestoreextension->setAttribute('xsi:schemaLocation', 'http://www.verisign-grs.com/epp/namestoreExt-1.1 namestoreExt-1.1.xsd');

        $this->getExtension()->appendChild($this->namestoreextension);

        return $this->namestoreextension;
    }
}
