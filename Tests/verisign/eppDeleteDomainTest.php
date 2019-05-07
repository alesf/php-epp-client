<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppDeleteDomainTest extends eppTestCase
{
    public function testDeleteDomain()
    {
        $domainname = $this->createDomain();
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDomain', $domain);
        $delete = new \Metaregistrar\EPP\verisignEppDeleteDomainRequest($domain);
        $delete->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppDeleteDomainRequest', $delete);
        $response = $this->conn->writeandread($delete);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);        
        $this->assertTrue($response->Success());
        // ST: Response depends on the status of the domain
        // $this->assertEquals('Command completed successfully; action pending', $response->getResultMessage());
        // $this->assertEquals(1001, $response->getResultCode());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
        
    }

    public function testDeleteNonexistentDomain()
    {
        $domainname = self::randomstring(30).'.com';
        $domain = new Metaregistrar\EPP\eppDomain($domainname);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDomain', $domain);
        $delete = new \Metaregistrar\EPP\verisignEppDeleteDomainRequest($domain);
        $delete->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppDeleteDomainRequest', $delete);
        $response = $this->conn->writeandread($delete);        
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);
        $this->expectException('Metaregistrar\EPP\eppException', 'Error 2303: Object does not exist');
        $this->assertFalse($response->Success());
    }
}
