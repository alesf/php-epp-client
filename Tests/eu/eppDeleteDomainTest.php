<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppDeleteDomainTest extends eppTestCase
{
    /**
     * Test successful domain restore
     * @throws \Metaregistrar\EPP\eppException
     */
    public function testDeleteDomainSuccess()
    {
        // Prepare a domain name by creating it
        $domainname = $this->createDomain();
        // And then deleting it
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $delete = new Metaregistrar\EPP\euridEppDeleteRequest($domain);
        $delete->scheduleDomainDelete("2019-04-20T23:59:59.000Z");
//        print_r($delete->saveXML());
        $response = $this->conn->writeandread($delete);
        // Restore done, check the results with assertions
        /* @var $response \Metaregistrar\EPP\eppRgpRestoreResponse */
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully; action pending', $response->getResultMessage());
        $this->assertEquals(1001, $response->getResultCode());

    }

    /**
     * Test successful domain restore
     * @throws \Metaregistrar\EPP\eppException
     */
    public function testImmediateDeleteDomainSuccess()
    {
        // Prepare a domain name by creating it
        $domainname = $this->createDomain();
        // And then deleting it
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $delete = new Metaregistrar\EPP\euridEppDeleteRequest($domain);
//        print_r($delete->saveXML());
        $response = $this->conn->writeandread($delete);
        // Restore done, check the results with assertions
        /* @var $response \Metaregistrar\EPP\eppRgpRestoreResponse */
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());

    }
}
