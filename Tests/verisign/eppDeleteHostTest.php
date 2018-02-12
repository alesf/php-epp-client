<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppDeleteHostTest extends eppTestCase
{

    /**
     * Test succesful host deletion
     * @throws \Metaregistrar\EPP\eppException
     */
    public function testDeleteHost()
    {
        $hostname = 'ns1.'.self::randomstring(30).'.net';
        $hostname_result = $this->createHost($hostname);

        $this->assertEquals($hostname_result, $hostname);
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $delete = new Metaregistrar\EPP\eppDeleteHostRequest($host);

        $response = $this->conn->writeandread($delete);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);
        /* @var $response Metaregistrar\EPP\eppDeleteResponse */
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }

    /**
     * Test unsuccesful deletion because contact does not exist
     * @throws \Metaregistrar\EPP\eppException
     */
    public function testDeleteNonexistentHost()
    {
        $message = null;
        $domainname = self::randomstring(8).'.net';
        $hostname = 'ns1.'.$domainname;
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $delete = new Metaregistrar\EPP\eppDeleteHostRequest($host);
        $response = $this->conn->writeandread($delete);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);
        /* @var $response Metaregistrar\EPP\eppDeleteResponse */
        try {
            $this->assertFalse($response->Success());
        } catch (Metaregistrar\EPP\eppException $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals('Error 2303: Object does not exist', $message);
    }

    public function testDeleteHostThatBelongsToDomain()
    {
        // TODO: create domain, create host and addit to domain, remote this host
        $this->assertTrue(true);
    }
}
