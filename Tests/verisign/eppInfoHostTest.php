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
        $hostname = 'ns1.'.self::randomstring(30).'.cc';
        $hostname = $this->createHost();
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $info = new Metaregistrar\EPP\verisignEppInfoHostRequest($host);
        $info->setSubProduct('dotCOM');
        $response = $this->conn->writeandread($info);
        echo $response->saveXML();
        $this->assertInstanceOf('Metaregistrar\EPP\eppInfoHostResponse', $response);
        /* @var $response Metaregistrar\EPP\eppInfoHostResponse */
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }
}
