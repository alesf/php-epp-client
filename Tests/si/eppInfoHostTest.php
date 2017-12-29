<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppInfoHostTest extends eppTestCase
{
    /**
     * Test succesful host info
     * @throws \Metaregistrar\EPP\eppException
     */
    public function testInfoHostSuccess()
    {
        $hostname = $this->createHost();
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $info = new Metaregistrar\EPP\eppInfoHostRequest($host);
        $response = $this->conn->writeandread($info);
        $this->assertInstanceOf('Metaregistrar\EPP\eppInfoHostResponse', $response);
        /* @var $response Metaregistrar\EPP\eppInfoHostResponse */
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }
}
