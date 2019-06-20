<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppCreateHostTest extends eppTestCase
{
    public function testCreateHostSi()
    {
        // Host creation from another registrar
        $hostname = self::randomstring(16).'.test.si';
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $create = new Metaregistrar\EPP\eppCreateHostRequest($host);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostRequest', $create);

        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostResponse', $response);

        if ($response instanceof Metaregistrar\EPP\eppCreateHostResponse) {
            $this->expectException('Metaregistrar\EPP\eppException', "Error 2308: Data management policy violation");
            $this->assertFalse($response->Success());
        }
    }


    public function testCreateHostSiWithIp()
    {
        // host creation own domain wit IP
        $domain_name = self::randomstring(30).'.si';
        $domain = $this->createDomain($domain_name);
        $hostname = self::randomstring(9) . '.' . $domain_name;
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $host->setIpAddress('8.8.8.8');
        $create = new Metaregistrar\EPP\eppCreateHostRequest($host);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostRequest', $create);
        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCreateHostResponse) {
            $this->assertTrue($response->Success());
        }
    }

    public function testCreateHostSiWithIpError()
    {
        // Host creation from another registrar with IP - should return error
        $hostname = self::randomstring(30).'.test.si';
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $host->setIpAddress('8.8.8.8');
        $create = new Metaregistrar\EPP\eppCreateHostRequest($host);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostRequest', $create);

        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCreateHostResponse) {
            $this->expectException('Metaregistrar\EPP\eppException', "Error 2306: Parameter value policy error");
            $this->assertFalse($response->Success());
        }
    }

    public function testCreateHostSiNotRegistered()
    {
        $hostname = 'ns1.'.self::randomstring(30).'.si';
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $create = new Metaregistrar\EPP\eppCreateHostRequest($host);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostRequest', $create);

        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCreateHostResponse) {
            $this->expectException('Metaregistrar\EPP\eppException', "Error 2308: Data management policy violation");
            $this->assertFalse($response->Success());
        }
    }

    public function testCreateHostNotSi()
    {
        $hostname = 'ns1.'.self::randomstring(30).'.net';
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $create = new Metaregistrar\EPP\eppCreateHostRequest($host);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostRequest', $create);

        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCreateHostResponse) {
            $this->assertTrue($response->Success());
        }
    }

    public function testCreateHostNotSiWithIp()
    {
        $hostname = 'ns1.'.self::randomstring(30).'.net';
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $host->setIpAddress('8.8.8.8');
        $create = new Metaregistrar\EPP\eppCreateHostRequest($host);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostRequest', $create);

        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostResponse', $response);

        if ($response instanceof Metaregistrar\EPP\eppCreateHostResponse) {
            $this->expectException('Metaregistrar\EPP\eppException', "Error 2306: Parameter value policy error");
            $this->assertFalse($response->Success());
        }
    }

    public function testCreateExistingHost()
    {
        $hostname = 'ns1.'.self::randomstring(30).'.net';
        $this->createHost($hostname);
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $create = new Metaregistrar\EPP\eppCreateHostRequest($host);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostRequest', $create);

        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostResponse', $response);

        if ($response instanceof Metaregistrar\EPP\eppCreateHostResponse) {
            $this->expectException('Metaregistrar\EPP\eppException', "Error 2202: Object exists");
            $this->assertFalse($response->Success());
        }
    }
}
