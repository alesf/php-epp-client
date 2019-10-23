<?php
namespace Metaregistrar\EPP;

trait verisignEppRequestTrait
{
    protected $namestoreExtension = null;
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

    public function setSubProductAuto($domain)
    {
        list($domainName, $ext) = explode('.', $domain);
        $this->subProduct = 'dot' . strtoupper($ext);
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
        $this->namestoreExtension = $this->createElement('namestoreExt:namestoreExt');
        $subProductNode = $this->createElement('namestoreExt:subProduct', $subProduct);
        $this->namestoreExtension->appendChild($subProductNode);
        $this->namestoreExtension->setAttribute('xmlns:namestoreExt', 'http://www.verisign-grs.com/epp/namestoreExt-1.1');
        $this->namestoreExtension->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $this->namestoreExtension->setAttribute('xsi:schemaLocation', 'http://www.verisign-grs.com/epp/namestoreExt-1.1 namestoreExt-1.1.xsd');

        $this->getExtension()->appendChild($this->namestoreExtension);

        return $this->namestoreExtension;
    }


}
