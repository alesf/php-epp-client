<?php
namespace Metaregistrar\EPP;

class euridEppDeleteRequest extends eppDeleteRequest
{
    public function __construct($deleteinfo)
    {
        parent::__construct($deleteinfo);
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function scheduleDomainDelete($date = null)
    {
        if ($date) {
            $this->addDomainScheduleDeleteExtension($date);
        }
        return $this;
    }

    public function cancelDomainDelete()
    {
        $this->addDomainCancelDeleteExtension();
        return $this;
    }

    public function addDomainScheduleDeleteExtension()
    {
        $ext = $this->createElement('extension');
        $contactext = $this->createElement('domain-ext:delete');
        $contactext->setAttribute('xmlns:domain-ext', 'http://www.eurid.eu/xml/epp/domain-ext-2.3');
        $schedule = $contactext->appendChild($this->createElement('domain-ext:schedule'));
        $schedule->appendChild($this->createElement('domain-ext:delDate', $this->domain_deleteDate));
        $ext->appendChild($contactext);
        $this->getCommand()->appendChild($ext);
    }

    public function addDomainCancelDeleteExtension()
    {
        $ext = $this->createElement('extension');
        $contactext = $this->createElement('domain-ext:delete');
        $contactext->setAttribute('xmlns:domain-ext', 'http://www.eurid.eu/xml/epp/domain-ext-2.1');
        $schedule = $contactext->appendChild($this->createElement('domain-ext:schedule'));
        $schedule->appendChild($this->createElement('domain-ext:cancel'));
        $ext->appendChild($contactext);
        $this->getCommand()->appendChild($ext);
    }
}
