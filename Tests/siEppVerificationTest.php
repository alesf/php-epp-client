<?php
require_once(dirname(__FILE__).'/../vendor/autoload.php');
require_once(dirname(__FILE__).'/../autoloader.php');
use PHPUnit\Framework\TestCase;

/*
This tests .si EPP Verification Extension
https://github.com/nic-at/epp-verification-extension
*/

class siEppVerificationTest extends TestCase {

    /**
     * @var \Metaregistrar\EPP\siEppConnection
     */
    protected $connection;

    protected function setUp(): void {
        $this->connection = new \Metaregistrar\EPP\siEppConnection;
    }

    /**
     * Helper to create a response object from XML
     */
    private function createContactInfoResponse($xml) {
        $request = new \Metaregistrar\EPP\eppInfoContactRequest(new \Metaregistrar\EPP\eppContactHandle('dummy'));
        $response = new \Metaregistrar\EPP\siEppInfoContactVerificationResponse($request);
        $response->loadXML($xml);
        $response->setXpath($this->connection->getServices());
        $response->setXpath($this->connection->getExtensions());
        $response->setXpath($this->connection->getXpathExtensions());
        return $response;
    }

    /**
     * Helper to create a domain info response object from XML
     */
    private function createDomainInfoResponse($xml) {
        $domain = new \Metaregistrar\EPP\eppDomain('dummy.si');
        $request = new \Metaregistrar\EPP\eppInfoDomainRequest($domain);
        $response = new \Metaregistrar\EPP\siEppInfoDomainVerificationResponse($request);
        $response->loadXML($xml);
        $response->setXpath($this->connection->getServices());
        $response->setXpath($this->connection->getExtensions());
        $response->setXpath($this->connection->getXpathExtensions());
        return $response;
    }


    /**
     * Tests siEppUpdateContactRequest with verification report (all fields)
     */
    public function testUpdateContactWithVerificationReport() {
        $contact = new \Metaregistrar\EPP\eppContactHandle('SI12345');
        $verificationReport = new \Metaregistrar\EPP\eppVerificationReport(
            \Metaregistrar\EPP\eppVerificationReport::RESULT_SUCCESS,
            '2023-11-26T22:00:00.0Z',
            'ID Card',
            'Process#321',
            'VerificationAgent'
        );

        $request = new \Metaregistrar\EPP\siEppUpdateContactVerificationRequest($contact, null, null, null, $verificationReport);
        $text = $request->saveXML(null, LIBXML_NOEMPTYTAG);

        // Verify verification:update extension structure
        $this->assertTrue(strpos($text, '<verification:update') !== false);
        $this->assertTrue(strpos($text, 'xmlns:verification="http://www.nic.at/xsd/at-ext-verification-1.0"') !== false);
        $this->assertTrue(strpos($text, '<verification:report>') !== false);
        $this->assertTrue(strpos($text, '</verification:report>') !== false);
        $this->assertTrue(strpos($text, '</verification:update>') !== false);

        // Verify report fields
        $this->assertEquals('success', $this->getTextBetween($text, '<verification:result>', '</verification:result>'));
        $this->assertEquals('2023-11-26T22:00:00.0Z', $this->getTextBetween($text, '<verification:verificationDate>', '</verification:verificationDate>'));
        $this->assertEquals('ID Card', $this->getTextBetween($text, '<verification:method>', '</verification:method>'));
        $this->assertEquals('Process#321', $this->getTextBetween($text, '<verification:reference>', '</verification:reference>'));
        $this->assertEquals('VerificationAgent', $this->getTextBetween($text, '<verification:agent>', '</verification:agent>'));

        // Verify contact:update is present
        $this->assertTrue(strpos($text, '<contact:id>SI12345</contact:id>') !== false);
    }


    /**
     * Tests siEppUpdateContactRequest with verification report (mandatory fields only)
     */
    public function testUpdateContactWithVerificationReportMandatoryOnly() {
        $contact = new \Metaregistrar\EPP\eppContactHandle('SI12345');
        $verificationReport = new \Metaregistrar\EPP\eppVerificationReport(
            \Metaregistrar\EPP\eppVerificationReport::RESULT_FAILURE,
            '2024-06-15T10:30:00.0Z'
        );

        $request = new \Metaregistrar\EPP\siEppUpdateContactVerificationRequest($contact, null, null, null, $verificationReport);
        $text = $request->saveXML(null, LIBXML_NOEMPTYTAG);

        $this->assertEquals('failure', $this->getTextBetween($text, '<verification:result>', '</verification:result>'));
        $this->assertEquals('2024-06-15T10:30:00.0Z', $this->getTextBetween($text, '<verification:verificationDate>', '</verification:verificationDate>'));

        // Optional fields should not be present
        $this->assertFalse(strpos($text, '<verification:method>'));
        $this->assertFalse(strpos($text, '<verification:reference>'));
        $this->assertFalse(strpos($text, '<verification:agent>'));
    }


    /**
     * Tests siEppCreateContactRequest with verification report
     */
    public function testCreateContactWithVerificationReport() {
        $postalinfo = new \Metaregistrar\EPP\siEppContactPostalInfo(
            'Test Name', 'Ljubljana', 'SI', 'Test Org', 'Test Street 1', null, '1000',
            \Metaregistrar\EPP\siEppContactPostalInfo::POSTAL_TYPE_INT,
            \Metaregistrar\EPP\siEppContactPostalInfo::ARNES_CONTACT_TYPE_PERSON
        );
        $contact = new \Metaregistrar\EPP\eppContact($postalinfo, 'test@test.si', '+386.12345678');

        $verificationReport = new \Metaregistrar\EPP\eppVerificationReport(
            \Metaregistrar\EPP\eppVerificationReport::RESULT_SUCCESS,
            '2024-01-15T12:00:00.0Z',
            'Personal ID',
            'REF-001',
            'AgentX'
        );

        $request = new \Metaregistrar\EPP\siEppCreateContactVerificationRequest($contact, $verificationReport);
        $text = $request->saveXML(null, LIBXML_NOEMPTYTAG);

        // Verify dnssi extension is present
        $this->assertTrue(strpos($text, '<dnssi:ext>') !== false);
        $this->assertTrue(strpos($text, '<dnssi:create>') !== false);

        // Verify verification:create extension
        $this->assertTrue(strpos($text, '<verification:create') !== false);
        $this->assertTrue(strpos($text, 'xmlns:verification="http://www.nic.at/xsd/at-ext-verification-1.0"') !== false);
        $this->assertTrue(strpos($text, '<verification:report>') !== false);
        $this->assertEquals('success', $this->getTextBetween($text, '<verification:result>', '</verification:result>'));
        $this->assertEquals('2024-01-15T12:00:00.0Z', $this->getTextBetween($text, '<verification:verificationDate>', '</verification:verificationDate>'));
        $this->assertEquals('Personal ID', $this->getTextBetween($text, '<verification:method>', '</verification:method>'));
        $this->assertEquals('REF-001', $this->getTextBetween($text, '<verification:reference>', '</verification:reference>'));
        $this->assertEquals('AgentX', $this->getTextBetween($text, '<verification:agent>', '</verification:agent>'));
    }


    /**
     * Tests siEppCreateContactRequest without verification report (backwards compatible)
     */
    public function testCreateContactWithoutVerificationReport() {
        $postalinfo = new \Metaregistrar\EPP\siEppContactPostalInfo(
            'Test Name', 'Ljubljana', 'SI', 'Test Org', 'Test Street 1', null, '1000',
            \Metaregistrar\EPP\siEppContactPostalInfo::POSTAL_TYPE_INT,
            \Metaregistrar\EPP\siEppContactPostalInfo::ARNES_CONTACT_TYPE_PERSON
        );
        $contact = new \Metaregistrar\EPP\eppContact($postalinfo, 'test@test.si', '+386.12345678');

        $request = new \Metaregistrar\EPP\siEppCreateContactRequest($contact);
        $text = $request->saveXML(null, LIBXML_NOEMPTYTAG);

        // dnssi extension should still be present
        $this->assertTrue(strpos($text, '<dnssi:ext>') !== false);

        // verification extension should NOT be present
        $this->assertFalse(strpos($text, '<verification:create'));
        $this->assertFalse(strpos($text, 'at-ext-verification'));
    }


    /**
     * Tests siEppInfoContactResponse with full verification report
     */
    public function testInfoContactResponseWithVerificationReport() {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
        <epp xmlns="urn:ietf:params:xml:ns:epp-1.0"
             xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
             xsi:schemaLocation="urn:ietf:params:xml:ns:epp-1.0 epp-1.0.xsd">
          <response>
            <result code="1000">
              <msg>Command completed successfully</msg>
            </result>
            <resData>
              <contact:infData xmlns:contact="urn:ietf:params:xml:ns:contact-1.0"
                               xsi:schemaLocation="urn:ietf:params:xml:ns:contact-1.0 contact-1.0.xsd">
                <contact:id>SI12345</contact:id>
                <contact:roid>CNT-12345-ARNES</contact:roid>
                <contact:status s="ok"/>
                <contact:postalInfo type="int">
                  <contact:name>Test Name</contact:name>
                  <contact:org>Test Org</contact:org>
                  <contact:addr>
                    <contact:street>Test Street 1</contact:street>
                    <contact:city>Ljubljana</contact:city>
                    <contact:pc>1000</contact:pc>
                    <contact:cc>SI</contact:cc>
                  </contact:addr>
                </contact:postalInfo>
                <contact:voice>+386.12345678</contact:voice>
                <contact:email>test@test.si</contact:email>
                <contact:clID>registrar1</contact:clID>
                <contact:crID>registrar1</contact:crID>
                <contact:crDate>2024-01-01T00:00:00.0Z</contact:crDate>
              </contact:infData>
            </resData>
            <extension>
              <dnssi:ext xmlns:dnssi="http://www.arnes.si/xml/epp/dnssi-1.2">
                <dnssi:info>
                  <dnssi:contact type="person"/>
                </dnssi:info>
              </dnssi:ext>
              <verification:infData xmlns:verification="http://www.nic.at/xsd/at-ext-verification-1.0"
                                    xsi:schemaLocation="http://www.nic.at/xsd/at-ext-verification-1.0 at-ext-verification-1.0.xsd">
                <verification:report receivedDate="2024-03-26T22:00:00.0Z" clID="registrar1">
                  <verification:result>success</verification:result>
                  <verification:verificationDate>2023-11-26T22:00:00.0Z</verification:verificationDate>
                  <verification:method>ID Card</verification:method>
                  <verification:reference>Process#321</verification:reference>
                  <verification:agent>VerificationAgent</verification:agent>
                </verification:report>
                <verification:status s="verified"/>
              </verification:infData>
            </extension>
            <trID>
              <clTRID>ABC-12345</clTRID>
              <svTRID>54322-XYZ</svTRID>
            </trID>
          </response>
        </epp>';

        $response = $this->createContactInfoResponse($xml);

        // Standard contact fields
        $this->assertEquals('SI12345', $response->getContactId());
        $this->assertEquals('test@test.si', $response->getContactEmail());

        // Verification report
        $report = $response->getVerificationReport();
        $this->assertNotNull($report);
        $this->assertEquals('success', $report->getResult());
        $this->assertEquals('2023-11-26T22:00:00.0Z', $report->getVerificationDate());
        $this->assertEquals('ID Card', $report->getMethod());
        $this->assertEquals('Process#321', $report->getReference());
        $this->assertEquals('VerificationAgent', $report->getAgent());
        $this->assertEquals('2024-03-26T22:00:00.0Z', $report->getReceivedDate());
        $this->assertEquals('registrar1', $report->getClID());

        // Verification status
        $this->assertEquals('verified', $response->getVerificationStatus());
    }


    /**
     * Tests siEppInfoContactResponse with verification status only (no report)
     */
    public function testInfoContactResponseWithStatusOnly() {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
        <epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
          <response>
            <result code="1000">
              <msg>Command completed successfully</msg>
            </result>
            <resData>
              <contact:infData xmlns:contact="urn:ietf:params:xml:ns:contact-1.0">
                <contact:id>SI12345</contact:id>
                <contact:roid>CNT-12345-ARNES</contact:roid>
                <contact:status s="ok"/>
                <contact:postalInfo type="int">
                  <contact:name>Test Name</contact:name>
                  <contact:addr>
                    <contact:street>Test Street 1</contact:street>
                    <contact:city>Ljubljana</contact:city>
                    <contact:pc>1000</contact:pc>
                    <contact:cc>SI</contact:cc>
                  </contact:addr>
                </contact:postalInfo>
                <contact:voice>+386.12345678</contact:voice>
                <contact:email>test@test.si</contact:email>
                <contact:clID>registrar1</contact:clID>
                <contact:crID>registrar1</contact:crID>
                <contact:crDate>2024-01-01T00:00:00.0Z</contact:crDate>
              </contact:infData>
            </resData>
            <extension>
              <verification:infData xmlns:verification="http://www.nic.at/xsd/at-ext-verification-1.0">
                <verification:status s="pending"/>
              </verification:infData>
            </extension>
            <trID>
              <clTRID>ABC-12345</clTRID>
              <svTRID>54322-XYZ</svTRID>
            </trID>
          </response>
        </epp>';

        $response = $this->createContactInfoResponse($xml);

        $this->assertNull($response->getVerificationReport());
        $this->assertEquals('pending', $response->getVerificationStatus());
    }


    /**
     * Tests siEppInfoContactResponse without verification extension
     */
    public function testInfoContactResponseWithoutVerification() {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
        <epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
          <response>
            <result code="1000">
              <msg>Command completed successfully</msg>
            </result>
            <resData>
              <contact:infData xmlns:contact="urn:ietf:params:xml:ns:contact-1.0">
                <contact:id>SI12345</contact:id>
                <contact:roid>CNT-12345-ARNES</contact:roid>
                <contact:status s="ok"/>
                <contact:postalInfo type="int">
                  <contact:name>Test Name</contact:name>
                  <contact:addr>
                    <contact:street>Test Street 1</contact:street>
                    <contact:city>Ljubljana</contact:city>
                    <contact:pc>1000</contact:pc>
                    <contact:cc>SI</contact:cc>
                  </contact:addr>
                </contact:postalInfo>
                <contact:voice>+386.12345678</contact:voice>
                <contact:email>test@test.si</contact:email>
                <contact:clID>registrar1</contact:clID>
                <contact:crID>registrar1</contact:crID>
                <contact:crDate>2024-01-01T00:00:00.0Z</contact:crDate>
              </contact:infData>
            </resData>
            <extension>
              <dnssi:ext xmlns:dnssi="http://www.arnes.si/xml/epp/dnssi-1.2">
                <dnssi:info>
                  <dnssi:contact type="person"/>
                </dnssi:info>
              </dnssi:ext>
            </extension>
            <trID>
              <clTRID>ABC-12345</clTRID>
              <svTRID>54322-XYZ</svTRID>
            </trID>
          </response>
        </epp>';

        $response = $this->createContactInfoResponse($xml);

        $this->assertNull($response->getVerificationReport());
        $this->assertNull($response->getVerificationStatus());
        $this->assertNull($response->getVerificationActionDate());
    }


    /**
     * Tests siEppInfoDomainResponse with verification status
     */
    public function testInfoDomainResponseWithVerificationStatus() {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
        <epp xmlns="urn:ietf:params:xml:ns:epp-1.0"
             xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
             xsi:schemaLocation="urn:ietf:params:xml:ns:epp-1.0 epp-1.0.xsd">
          <response>
            <result code="1000">
              <msg>Command completed successfully</msg>
            </result>
            <resData>
              <domain:infData xmlns:domain="urn:ietf:params:xml:ns:domain-1.0"
                              xsi:schemaLocation="urn:ietf:params:xml:ns:domain-1.0 domain-1.0.xsd">
                <domain:name>test.si</domain:name>
                <domain:roid>DOM-12345-ARNES</domain:roid>
                <domain:status s="ok"/>
                <domain:registrant>SI12345</domain:registrant>
                <domain:ns>
                  <domain:hostObj>ns1.test.si</domain:hostObj>
                  <domain:hostObj>ns2.test.si</domain:hostObj>
                </domain:ns>
                <domain:clID>registrar1</domain:clID>
                <domain:crDate>2024-01-01T00:00:00.0Z</domain:crDate>
                <domain:exDate>2025-01-01T00:00:00.0Z</domain:exDate>
              </domain:infData>
            </resData>
            <extension>
              <verification:infData xmlns:verification="http://www.nic.at/xsd/at-ext-verification-1.0"
                                    xsi:schemaLocation="http://www.nic.at/xsd/at-ext-verification-1.0 at-ext-verification-1.0.xsd">
                <verification:status s="pending"/>
                <verification:actionDate>2025-11-26T22:00:00.0Z</verification:actionDate>
              </verification:infData>
            </extension>
            <trID>
              <clTRID>ABC-12345</clTRID>
              <svTRID>54322-XYZ</svTRID>
            </trID>
          </response>
        </epp>';

        $response = $this->createDomainInfoResponse($xml);

        // Standard domain fields
        $this->assertEquals('test.si', $response->getDomainName());
        $this->assertEquals('SI12345', $response->getDomainRegistrant());

        // Verification extension
        $this->assertEquals('pending', $response->getVerificationStatus());
        $this->assertEquals('2025-11-26T22:00:00.0Z', $response->getVerificationActionDate());
    }


    /**
     * Tests siEppInfoDomainResponse with serverHold verification status
     */
    public function testInfoDomainResponseWithServerHoldStatus() {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
        <epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
          <response>
            <result code="1000">
              <msg>Command completed successfully</msg>
            </result>
            <resData>
              <domain:infData xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
                <domain:name>held.si</domain:name>
                <domain:roid>DOM-99999-ARNES</domain:roid>
                <domain:status s="serverHold"/>
                <domain:registrant>SI99999</domain:registrant>
                <domain:clID>registrar1</domain:clID>
                <domain:crDate>2024-01-01T00:00:00.0Z</domain:crDate>
                <domain:exDate>2025-01-01T00:00:00.0Z</domain:exDate>
              </domain:infData>
            </resData>
            <extension>
              <verification:infData xmlns:verification="http://www.nic.at/xsd/at-ext-verification-1.0">
                <verification:status s="serverHold"/>
              </verification:infData>
            </extension>
            <trID>
              <clTRID>ABC-12345</clTRID>
              <svTRID>54322-XYZ</svTRID>
            </trID>
          </response>
        </epp>';

        $response = $this->createDomainInfoResponse($xml);

        $this->assertEquals('serverHold', $response->getVerificationStatus());
        $this->assertNull($response->getVerificationActionDate());
    }


    /**
     * Tests siEppInfoDomainResponse without verification extension
     */
    public function testInfoDomainResponseWithoutVerification() {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
        <epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
          <response>
            <result code="1000">
              <msg>Command completed successfully</msg>
            </result>
            <resData>
              <domain:infData xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
                <domain:name>normal.si</domain:name>
                <domain:roid>DOM-11111-ARNES</domain:roid>
                <domain:status s="ok"/>
                <domain:registrant>SI11111</domain:registrant>
                <domain:clID>registrar1</domain:clID>
                <domain:crDate>2024-01-01T00:00:00.0Z</domain:crDate>
                <domain:exDate>2025-01-01T00:00:00.0Z</domain:exDate>
              </domain:infData>
            </resData>
            <trID>
              <clTRID>ABC-12345</clTRID>
              <svTRID>54322-XYZ</svTRID>
            </trID>
          </response>
        </epp>';

        $response = $this->createDomainInfoResponse($xml);

        $this->assertNull($response->getVerificationStatus());
        $this->assertNull($response->getVerificationActionDate());
    }


    /**
     * Tests siEppVerificationReport data class
     */
    public function testVerificationReportDataClass() {
        $report = new \Metaregistrar\EPP\eppVerificationReport();

        $report->setResult(\Metaregistrar\EPP\eppVerificationReport::RESULT_SUCCESS);
        $report->setVerificationDate('2024-01-15T12:00:00.0Z');
        $report->setMethod('Passport');
        $report->setReference('REF-999');
        $report->setAgent('Agent Smith');
        $report->setReceivedDate('2024-01-16T08:00:00.0Z');
        $report->setClID('registrar1');

        $this->assertEquals('success', $report->getResult());
        $this->assertEquals('2024-01-15T12:00:00.0Z', $report->getVerificationDate());
        $this->assertEquals('Passport', $report->getMethod());
        $this->assertEquals('REF-999', $report->getReference());
        $this->assertEquals('Agent Smith', $report->getAgent());
        $this->assertEquals('2024-01-16T08:00:00.0Z', $report->getReceivedDate());
        $this->assertEquals('registrar1', $report->getClID());
    }


    /**
     * Tests siEppVerificationReport constants
     */
    public function testVerificationReportConstants() {
        $this->assertEquals('success', \Metaregistrar\EPP\eppVerificationReport::RESULT_SUCCESS);
        $this->assertEquals('failure', \Metaregistrar\EPP\eppVerificationReport::RESULT_FAILURE);
        $this->assertEquals('none', \Metaregistrar\EPP\eppVerificationReport::STATUS_NONE);
        $this->assertEquals('pending', \Metaregistrar\EPP\eppVerificationReport::STATUS_PENDING);
        $this->assertEquals('serverHold', \Metaregistrar\EPP\eppVerificationReport::STATUS_SERVERHOLD);
        $this->assertEquals('verified', \Metaregistrar\EPP\eppVerificationReport::STATUS_VERIFIED);
        $this->assertEquals('failed', \Metaregistrar\EPP\eppVerificationReport::STATUS_FAILED);
    }


    /**
     * Tests contact info response with verification report having optional fields missing
     */
    public function testInfoContactResponseWithPartialVerificationReport() {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
        <epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
          <response>
            <result code="1000">
              <msg>Command completed successfully</msg>
            </result>
            <resData>
              <contact:infData xmlns:contact="urn:ietf:params:xml:ns:contact-1.0">
                <contact:id>SI12345</contact:id>
                <contact:roid>CNT-12345-ARNES</contact:roid>
                <contact:status s="ok"/>
                <contact:postalInfo type="int">
                  <contact:name>Test Name</contact:name>
                  <contact:addr>
                    <contact:street>Test Street 1</contact:street>
                    <contact:city>Ljubljana</contact:city>
                    <contact:pc>1000</contact:pc>
                    <contact:cc>SI</contact:cc>
                  </contact:addr>
                </contact:postalInfo>
                <contact:voice>+386.12345678</contact:voice>
                <contact:email>test@test.si</contact:email>
                <contact:clID>registrar1</contact:clID>
                <contact:crID>registrar1</contact:crID>
                <contact:crDate>2024-01-01T00:00:00.0Z</contact:crDate>
              </contact:infData>
            </resData>
            <extension>
              <verification:infData xmlns:verification="http://www.nic.at/xsd/at-ext-verification-1.0">
                <verification:report receivedDate="2024-03-26T22:00:00.0Z" clID="registrar1">
                  <verification:result>failure</verification:result>
                  <verification:verificationDate>2024-03-20T10:00:00.0Z</verification:verificationDate>
                </verification:report>
                <verification:status s="failed"/>
              </verification:infData>
            </extension>
            <trID>
              <clTRID>ABC-12345</clTRID>
              <svTRID>54322-XYZ</svTRID>
            </trID>
          </response>
        </epp>';

        $response = $this->createContactInfoResponse($xml);

        $report = $response->getVerificationReport();
        $this->assertNotNull($report);
        $this->assertEquals('failure', $report->getResult());
        $this->assertEquals('2024-03-20T10:00:00.0Z', $report->getVerificationDate());
        $this->assertNull($report->getMethod());
        $this->assertNull($report->getReference());
        $this->assertNull($report->getAgent());
        $this->assertEquals('2024-03-26T22:00:00.0Z', $report->getReceivedDate());
        $this->assertEquals('registrar1', $report->getClID());

        $this->assertEquals('failed', $response->getVerificationStatus());
    }


    /**
     * Tests contact info response with actionDate
     */
    public function testInfoContactResponseWithActionDate() {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
        <epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
          <response>
            <result code="1000">
              <msg>Command completed successfully</msg>
            </result>
            <resData>
              <contact:infData xmlns:contact="urn:ietf:params:xml:ns:contact-1.0">
                <contact:id>SI12345</contact:id>
                <contact:roid>CNT-12345-ARNES</contact:roid>
                <contact:status s="ok"/>
                <contact:postalInfo type="int">
                  <contact:name>Test Name</contact:name>
                  <contact:addr>
                    <contact:street>Test Street 1</contact:street>
                    <contact:city>Ljubljana</contact:city>
                    <contact:pc>1000</contact:pc>
                    <contact:cc>SI</contact:cc>
                  </contact:addr>
                </contact:postalInfo>
                <contact:voice>+386.12345678</contact:voice>
                <contact:email>test@test.si</contact:email>
                <contact:clID>registrar1</contact:clID>
                <contact:crID>registrar1</contact:crID>
                <contact:crDate>2024-01-01T00:00:00.0Z</contact:crDate>
              </contact:infData>
            </resData>
            <extension>
              <verification:infData xmlns:verification="http://www.nic.at/xsd/at-ext-verification-1.0">
                <verification:status s="pending"/>
                <verification:actionDate>2025-06-15T00:00:00.0Z</verification:actionDate>
              </verification:infData>
            </extension>
            <trID>
              <clTRID>ABC-12345</clTRID>
              <svTRID>54322-XYZ</svTRID>
            </trID>
          </response>
        </epp>';

        $response = $this->createContactInfoResponse($xml);

        $this->assertEquals('pending', $response->getVerificationStatus());
        $this->assertEquals('2025-06-15T00:00:00.0Z', $response->getVerificationActionDate());
    }


    /*
     * Helper function to get value from string
     */
    protected function getTextBetween($text, $start, $end) {
        if (($startpos = strpos(strtolower($text), strtolower($start))) === false) {
            return $text;
        }
        if (($endpos = strpos(strtolower($text), strtolower($end))) === false) {
            return $text;
        }
        $startpos = $startpos + strlen($start);
        $length = $endpos - $startpos;
        $text = substr($text, $startpos, $length);
        return $text;
    }
}
