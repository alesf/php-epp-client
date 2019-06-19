<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppDeleteDomainTest extends eppTestCase
{
    /**
     * Test successful domain restore
     * @throws \Metaregistrar\EPP\eppException
     * @group ignore_me
     *
     * EURID doesn't support rgp-1.0
     * response:  Error 2103: Unimplemented extension; value:urn:ietf:params:xml:ns:rgp-1.0 (Unknown extURI.)
     *
     */
    public function testDeleteDomainSuccess()
    {
        // Prepare a domain name by creating it
        $domainname = $this->createDomain();
        // And then deleting it
        if ($this->deleteDomain($domainname)) {
            // Now the actual work starts: Restore the domain name
            $domain = new Metaregistrar\EPP\eppDomain($domainname);
            $restore = new Metaregistrar\EPP\eppRgpRestoreRequest($domain);
            $response = $this->conn->writeandread($restore);
            // Restore done, check the results with assertions
            /* @var $response \Metaregistrar\EPP\eppRgpRestoreResponse */
            $this->assertInstanceOf('Metaregistrar\EPP\eppRgpRestoreResponse', $response);
            $statuses = $response->getRestoreStatuses();
            $this->assertCount(1, $statuses);
            $this->assertEquals('pendingRestore', $statuses[0]);
            $this->assertTrue($response->Success());
            $this->assertEquals('Command completed succesfully', $response->getResultMessage());
            $this->assertEquals(1000, $response->getResultCode());
        }
    }
}
