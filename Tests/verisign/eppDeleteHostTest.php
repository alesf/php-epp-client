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
        $hostname = self::randomstring(30).'.HCtM7kDdxtU3dBNXzy2X.com';
        $hostname_result = $this->createHost($hostname);
        $this->assertEquals($hostname_result, $hostname);
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $delete = new Metaregistrar\EPP\verisignEppDeleteHostRequest($host);
        $delete->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppDeleteHostRequest', $delete);
        $response = $this->conn->writeandread($delete);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);
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
        $hostname = 'ns1.'.self::randomstring(30).'.com';
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $delete = new Metaregistrar\EPP\verisignEppDeleteHostRequest($host);
        $delete->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppDeleteHostRequest', $delete);
        $response = $this->conn->writeandread($delete);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);
        try {
            $this->assertFalse($response->Success());
        } catch (Metaregistrar\EPP\eppException $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals('Error 2303: Object does not exist', $message);
    }

    public function testDeleteHostThatBelongsToDomain()
    {
        // ST: todo create domain, create host and add it to domain, remove this host
        $domain = self::randomstring(30).'.com';
        $domain_name = $this->createDomain($domain);
        $this->assertEquals($domain_name, $domain);
        $hostname = self::randomstring(30).'.'.$domain;
        $hostname_result = $this->createHost($hostname);
        $this->assertEquals($hostname_result, $hostname);
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $delete = new Metaregistrar\EPP\verisignEppDeleteHostRequest($host);
        $delete->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppDeleteHostRequest', $delete);
        $response = $this->conn->writeandread($delete);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }
}
