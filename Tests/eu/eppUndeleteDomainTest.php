<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppRestoreDomainTest extends eppTestCase
{
    /**
     * Test successful domain restore
     * @throws \Metaregistrar\EPP\eppException
     */
    public function testRestoreDomainSuccess()
    {
        // Prepare a domain name by creating it
        $domainname = $this->createDomain();
        // And then deleting it
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $delete = new Metaregistrar\EPP\euridEppDeleteRequest($domain);
        $delete->scheduleDomainDelete("2019-04-20T23:59:59.000Z");
        $this->conn->writeandread($delete);

        $restore = new Metaregistrar\EPP\euridEppDeleteRequest($domain);
        $restore->cancelDomainDelete();
        $response = $this->conn->writeandread($restore);
        // Restore done, check the results with assertions
        /* @var $response \Metaregistrar\EPP\eppRgpRestoreResponse */
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse',$response);
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully',$response->getResultMessage());
        $this->assertEquals(1000,$response->getResultCode());

    }
}
