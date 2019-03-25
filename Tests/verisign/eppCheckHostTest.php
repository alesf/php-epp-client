<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppCheckHostTest extends eppTestCase
{

    /**
     * @group test
     * Test if random host handle is available
     * Expects a standard result for a free host handle
     */
    public function testCheckHostAvailable()
    {
        $hostname = 'ns1.'.self::randomstring(30).'.test.com';
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $check = new Metaregistrar\EPP\verisignEppCheckHostRequest($host);
        $check->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppCheckHostRequest', $check);
        $response = $this->conn->writeandread($check);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCheckHostResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCheckHostResponse) {
            $this->assertTrue($response->Success());
            // echo $response->getResultReason();
            // echo $response->saveXML();
            if ($response->Success()) {
                $checks = $response->getCheckedHosts();
                $this->assertCount(1, $checks);
                $this->assertArrayHasKey($hostname, $checks);
                $this->assertTrue($checks[$hostname]);
            }
        }
    }

    /**
     * Test if random host handle is available
     * Expects a standard result for a free host handle
     */
    public function testCheckHostAvailableExtended()
    {
        $hostname = 'ns1.'.self::randomstring(30).'.test.com';
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $check = new Metaregistrar\EPP\verisignEppCheckHostRequest($host);
        $check->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppCheckHostRequest', $check);
        $response = $this->conn->writeandread($check);
        echo $response->saveXML();
        $this->assertInstanceOf('Metaregistrar\EPP\eppCheckHostResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCheckHostResponse) {
            $this->assertTrue($response->Success());
            if ($response->Success()) {
                $checks = $response->getCheckedHostsExtended();

                $this->assertCount(1, $checks);
                foreach ($checks as $check) {
                    $this->assertEquals($check['hostname'], $hostname);
                    $this->assertTrue($check['available']);
                }
            }
        }
    }

    /**
     * Test if used host handle is available
     * Expects a error result
     */
    public function testCheckHostNotAvailable()
    {
        $hostname = 'ns1.'.self::randomstring(30).'.net';
        $this->createHost($hostname);
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $check = new Metaregistrar\EPP\verisignEppCheckHostRequest($host);
        $check->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppCheckHostRequest', $check);

        $response = $this->conn->writeandread($check);

        $this->assertInstanceOf('Metaregistrar\EPP\eppCheckHostResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCheckHostResponse) {
            $this->assertTrue($response->Success());
            if ($response->Success()) {
                $checks = $response->getCheckedHosts();
                $this->assertCount(1, $checks);
                $this->assertArrayHasKey($hostname, $checks);
                $this->assertFalse($checks[$hostname]);
            }
        }
    }


    /**
     * Test if random contact handle is available
     * Expects a standard result for a free contact handle
     */
    public function testCheckHostIllegarChars()
    {
        $hostname = 'ns1.test%@test.com';
        $host = new Metaregistrar\EPP\eppHost($hostname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $check = new Metaregistrar\EPP\verisignEppCheckHostRequest($host);
        $check->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppCheckHostRequest', $check);
        //echo $check->saveXML();
        $response = $this->conn->writeandread($check);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCheckHostResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCheckHostResponse) {
            //echo $response->saveXML();
            //$this->setExpectedException('Metaregistrar\EPP\eppException');
            $this->assertTrue($response->Success());
        }
    }
}
