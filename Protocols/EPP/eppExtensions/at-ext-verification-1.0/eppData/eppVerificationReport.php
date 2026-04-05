<?php
namespace Metaregistrar\EPP;

/**
 * Implementation of https://github.com/nic-at/epp-verification-extension
 */
class eppVerificationReport
{
    const VERIFICATION_NAMESPACE = 'http://www.nic.at/xsd/at-ext-verification-1.0';
    const VERIFICATION_SCHEMA_LOCATION = 'http://www.nic.at/xsd/at-ext-verification-1.0 at-ext-verification-1.0.xsd';

    const RESULT_SUCCESS = 'success';
    const RESULT_FAILURE = 'failure';

    const STATUS_NONE = 'none';
    const STATUS_PENDING = 'pending';
    const STATUS_SERVERHOLD = 'serverHold';
    const STATUS_VERIFIED = 'verified';
    const STATUS_FAILED = 'failed';

    /**
     * @var string
     */
    private $result;

    /**
     * @var string
     */
    private $verificationDate;

    /**
     * @var string|null
     */
    private $method;

    /**
     * @var string|null
     */
    private $reference;

    /**
     * @var string|null
     */
    private $agent;

    /**
     * @var string|null
     */
    private $receivedDate;

    /**
     * @var string|null
     */
    private $clID;

    /**
     * @param string $result
     * @param string $verificationDate
     * @param string|null $method
     * @param string|null $reference
     * @param string|null $agent
     * @param string|null $receivedDate
     * @param string|null $clID
     */
    public function __construct($result = null, $verificationDate = null, $method = null, $reference = null, $agent = null, $receivedDate = null, $clID = null)
    {
        if ($result) {
            $this->setResult($result);
        }
        if ($verificationDate) {
            $this->setVerificationDate($verificationDate);
        }
        if ($method) {
            $this->setMethod($method);
        }
        if ($reference) {
            $this->setReference($reference);
        }
        if ($agent) {
            $this->setAgent($agent);
        }
        if ($receivedDate) {
            $this->setReceivedDate($receivedDate);
        }
        if ($clID) {
            $this->setClID($clID);
        }
    }

    /* setters */

    public function setResult($result)
    {
        $this->result = $result;
    }

    public function setVerificationDate($verificationDate)
    {
        $this->verificationDate = $verificationDate;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    public function setAgent($agent)
    {
        $this->agent = $agent;
    }

    public function setReceivedDate($receivedDate)
    {
        $this->receivedDate = $receivedDate;
    }

    public function setClID($clID)
    {
        $this->clID = $clID;
    }

    /* getters */

    public function getResult()
    {
        return $this->result;
    }

    public function getVerificationDate()
    {
        return $this->verificationDate;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function getAgent()
    {
        return $this->agent;
    }

    public function getReceivedDate()
    {
        return $this->receivedDate;
    }

    public function getClID()
    {
        return $this->clID;
    }

    /**
     * Generates the verification report XML for EPP requests
     *
     * @param eppRequest $request
     * @param \DOMElement $ext
     */
    public function exportXML(eppRequest $request, \DOMElement $ext)
    {
        $report = $request->createElement('verification:report');

        # mandatory fields
        foreach (['result', 'verificationDate'] as $element) {
            $report->appendChild($request->createElement('verification:' . $element, $this->$element));
        }

        # optional fields
        foreach (['method', 'reference', 'agent'] as $element) {
            if (!is_null($this->$element)) {
                $report->appendChild($request->createElement('verification:' . $element, $this->$element));
            }
        }

        $ext->appendChild($report);
    }
}
