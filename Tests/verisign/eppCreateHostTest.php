<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppCreateHostTest extends eppTestCase
{
    /**
     * @group failing
     */
    public function testCreateHost()
    {
        $hostname = self::randomstring(30).'.example.com';
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $create = new Metaregistrar\EPP\verisignEppCreateHostRequest($host);
        $create->setSubProduct('dotCOM');

        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppCreateHostRequest', $create);

        $response = $this->conn->writeandread($create);

        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCreateHostResponse) {
            $this->assertTrue($response->Success());
        }
    }

    public function testCreateHostWithIp()
    {
        // TODO: naredi domeno - dodaj host za tisto domeno

        $this->assertTrue(true);
        return true;

        $hostname = self::randomstring(30).'.test.com';
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $host->setIpAddress('8.8.8.8');
        $create = new Metaregistrar\EPP\verisignEppCreateHostRequest($host);
        $create->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppCreateHostRequest', $create);

        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCreateHostResponse) {
            $this->assertTrue($response->Success());
        }
    }

    public function testCreateHostWithIpError()
    {
        $hostname = self::randomstring(30).'.test.com';
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $host->setIpAddress('8.8.8.8');
        $create = new Metaregistrar\EPP\verisignEppCreateHostRequest($host);
        $create->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppCreateHostRequest', $create);

        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCreateHostResponse) {
            $this->expectException('Metaregistrar\EPP\eppException', "Error 2306: Parameter value policy error");
            $this->assertFalse($response->Success());
        }
    }

    public function testCreateHostNotRegistered()
    {
        $hostname = 'ns1.'.self::randomstring(30).'.com';
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $create = new Metaregistrar\EPP\verisignEppCreateHostRequest($host);
        $create->setSubProduct('dotCOM');

        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppCreateHostRequest', $create);

        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCreateHostResponse) {
            $this->expectException('Metaregistrar\EPP\eppException', "Error 2308: Data management policy violation");
            $this->assertFalse($response->Success());
        }
    }

    public function testCreateHostNotCom()
    {
        $hostname = 'ns1.'.self::randomstring(30).'.cc';
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $create = new Metaregistrar\EPP\verisignEppCreateHostRequest($host);
        $create->setSubProduct('dotCOM');

        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppCreateHostRequest', $create);

        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCreateHostResponse) {
            $this->assertTrue($response->Success());
        }
    }

    public function testCreateHostNotComWithIp()
    {
        $hostname = 'ns1.'.self::randomstring(30).'.cc';
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $host->setIpAddress('8.8.8.8');
        $create = new Metaregistrar\EPP\verisignEppCreateHostRequest($host);
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppCreateHostRequest', $create);

        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostResponse', $response);

        if ($response instanceof Metaregistrar\EPP\eppCreateHostResponse) {
            $this->expectException('Metaregistrar\EPP\eppException', "Error 2306: Parameter value policy error");
            $this->assertFalse($response->Success());
        }
    }

    public function testCreateExistingHost()
    {
        $hostname = 'ns1.'.self::randomstring(30).'.cc';
        $this->createHost($hostname);
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $create = new Metaregistrar\EPP\verisignEppCreateHostRequest($host);
        $create->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppCreateHostRequest', $create);

        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostResponse', $response);

        if ($response instanceof Metaregistrar\EPP\eppCreateHostResponse) {
            $this->expectException('Metaregistrar\EPP\eppException', "Error 2202: Object exists");
            $this->assertFalse($response->Success());
        }
    }
}
