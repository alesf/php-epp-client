<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppDeleteDomainTest extends eppTestCase
{
    public function testDeleteDomain()
    {
        $domainname = $this->createDomain();
        $domain = new Metaregistrar\EPP\eppDomain($domainname);

        $delete = new \Metaregistrar\EPP\eppDeleteDomainRequest($domain);

        $response = $this->conn->writeandread($delete);

        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);

        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully; action pending', $response->getResultMessage());
        $this->assertEquals(1001, $response->getResultCode());
    }

    public function testDeleteNonexistentDomain()
    {
        $domainname = self::randomstring(30).'.si';
        $domain = new Metaregistrar\EPP\eppDomain($domainname);

        $delete = new \Metaregistrar\EPP\eppDeleteDomainRequest($domain);
        $response = $this->conn->writeandread($delete);

        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);

        $this->expectException('Metaregistrar\EPP\eppException', 'Error 2303: Object does not exist');
        $this->assertFalse($response->Success());
    }
}
