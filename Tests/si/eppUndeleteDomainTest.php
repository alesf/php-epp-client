<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppRestoreDomainTest extends eppTestCase
{
    /**
     * Test successful domain restore
     * @throws \Metaregistrar\EPP\eppException
     */
    // public function testRestoreDomainSuccess()
    // {
    //     // Prepare a domain name by creating it
    //     $domainname = $this->createDomain();
    //     // And then deleting it
    //     if ($this->deleteDomain($domainname)) {
    //         // Now the actual work starts: Restore the domain name
    //         $domain = new Metaregistrar\EPP\eppDomain($domainname);
    //         $restore = new Metaregistrar\EPP\eppRgpRestoreRequest($domain);
    //         $response = $this->conn->writeandread($restore);
    //         // Restore done, check the results with assertions
    //         /* @var $response \Metaregistrar\EPP\eppRgpRestoreResponse */
    //         $this->assertInstanceOf('Metaregistrar\EPP\eppRgpRestoreResponse', $response);
    //         $statuses = $response->getRestoreStatuses();
    //         $this->assertCount(1, $statuses);
    //         $this->assertEquals('pendingRestore', $statuses[0]);
    //         $this->assertTrue($response->Success());
    //         $this->assertEquals('Command completed succesfully', $response->getResultMessage());
    //         $this->assertEquals(1000, $response->getResultCode());
    //     }
    // }

    public function testRemoveDomainDeleteStatus()
    {
        $domainname = $this->createDomain();
        $domain = new Metaregistrar\EPP\eppDomain($domainname);

        $delete = new \Metaregistrar\EPP\eppDeleteDomainRequest($domain);
        $response = $this->conn->writeandread($delete);

        // $info_domain = new Metaregistrar\EPP\eppInfoDomainRequest($domain);
        // $info_response = $this->conn->writeandread($info_domain);
        // echo $info_response->saveXML();
        // exit();

        $add = null;
        $mod = null;
        $del = new Metaregistrar\EPP\eppDomain($domainname);
        $del->addStatus('pendingDelete');
        $update = new Metaregistrar\EPP\eppUpdateDomainRequest($domain, $add, $del, $mod, true);

        echo $update->saveXML();

        $response = $this->conn->writeandread($update);

        echo $response->saveXML();

        $this->assertTrue($response->Success());
    }
}
