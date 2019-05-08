<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppCreateDomainIDNTest extends eppTestCase
{
    
    public function testCheckDomainIDNAvailable()
    {        
        $domainname = $this->randomstring(12).'ščžäžđ.com'; 
        $domainname = idn_to_ascii($domainname, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
        $domain = new \Metaregistrar\EPP\eppDomain($domainname);        
        $this->assertInstanceOf('Metaregistrar\EPP\eppDomain', $domain);
        $check = new Metaregistrar\EPP\verisignEppCheckDomainRequest($domain);
        $check->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppCheckDomainRequest', $check);
        $response = $this->conn->writeandread($check);        
        $this->assertInstanceOf('Metaregistrar\EPP\eppCheckDomainResponse', $response);
        if ($response instanceof Metaregistrar\EPP\eppCheckDomainResponse) {
            $this->assertTrue($response->Success());
            if ($response->Success()) {
                $checks = $response->getCheckedDomains();
                $this->assertCount(1, $checks);
                $check = $checks[0];                
                $this->assertArrayHasKey('available', $check);
                $this->assertTrue($check['available']);
                $this->assertArrayHasKey('reason', $check);
                $this->assertNull($check['reason']);
            }
        }
    }
     
    public function testCreateDomainIDN()
    {
        $contactid = $this->createContact();
        $domain_name = $this->randomstring(12).'öšćžß.com';
        $domain_name = idn_to_ascii($domain_name, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
        $domain = new \Metaregistrar\EPP\eppDomain($domain_name);
        $domain->setPeriod(1);
        $domain->setRegistrant($contactid);
        $domain->setAuthorisationCode($this->randomstring(8, true));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_ADMIN));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_TECH));
        // $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_BILLING));
        $create = new \Metaregistrar\EPP\verisignEppCreateDomainRequest($domain);
        $create->setSubProduct('dotCOM');
        $create->setIDNLang('ENG');        
        $response = $this->conn->writeandread($create);
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateDomainResponse', $response);        
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }    

    // public function testCreateDomainIDNNoLanguage()
    // {
    //     $contactid = $this->createContact();
    //     $domain_name = $this->randomstring(12).'öšćžß.com';        
    //     $domain = new \Metaregistrar\EPP\eppDomainIDN($domain_name);        
    //     $domain->setPeriod(1);
    //     $domain->setRegistrant($contactid);
    //     $domain->setAuthorisationCode($this->randomstring(8, true));
    //     $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_ADMIN));
    //     $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_TECH));
    //     // $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_BILLING));
    //     $create = new \Metaregistrar\EPP\verisignEppCreateDomainRequest($domain);
    //     $create->setSubProduct('dotCOM');                       
    //     $response = $this->conn->writeandread($create);        
    //     $this->expectException('Metaregistrar\EPP\eppException', 'Error 2303: Required parameter missing (Language Extension required for IDN label domain names.)');
    //     $this->assertInstanceOf('Metaregistrar\EPP\eppCreateDomainResponse', $response);        
    //     $this->assertFalse($response->Success());        
    // }
    
    public function testCreateDomainIDNMismatchLanguage()
    {
        $contactid = $this->createContact();
        $domain_name = $this->randomstring(12).'žđöš.com';
        $domain_name = idn_to_ascii($domain_name, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);        
        $domain = new \Metaregistrar\EPP\eppDomain($domain_name);        
        $domain->setPeriod(1);
        $domain->setRegistrant($contactid);
        $domain->setAuthorisationCode($this->randomstring(8, true));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_ADMIN));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_TECH));
        // $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_BILLING));
        $create = new \Metaregistrar\EPP\verisignEppCreateDomainRequest($domain);
        $create->setSubProduct('dotCOM');
        // set chinese language
        $create->setIDNLang('CHI');
        $response = $this->conn->writeandread($create);                  
        $this->expectException('Metaregistrar\EPP\eppException', 'Error 2306: Parameter value policy error (IDN mismatches with Language)');
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateDomainResponse', $response);        
        $this->assertFalse($response->Success());
    }


    public function testCreateDomainIDNUnknownLanguage()
    {
        $contactid = $this->createContact();
        $domain_name = $this->randomstring(12).'žđöš.com';      
        $domain_name = idn_to_ascii($domain_name, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);  
        $domain = new \Metaregistrar\EPP\eppDomain($domain_name);        
        $domain->setPeriod(1);
        $domain->setRegistrant($contactid);
        $domain->setAuthorisationCode($this->randomstring(8, true));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_ADMIN));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_TECH));
        // $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_BILLING));
        $create = new \Metaregistrar\EPP\verisignEppCreateDomainRequest($domain);
        $create->setSubProduct('dotCOM');           
        $create->setIDNLang('anhfj3478949');                
        $response = $this->conn->writeandread($create);                  
        $this->expectException('Metaregistrar\EPP\eppException', 'Error 2306: Parameter value policy error (IDN mismatches with Language)');
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateDomainResponse', $response);        
        $this->assertFalse($response->Success());        
    }

    public function testCreateDomainIDNRussianLanguage()
    {
        $contactid = $this->createContact();
        $domain_name = self::randomnumber(10) . 'Здравству.com';  
        $domain_name = idn_to_ascii($domain_name, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);      
        $domain = new \Metaregistrar\EPP\eppDomain($domain_name);        
        $domain->setPeriod(1);
        $domain->setRegistrant($contactid);
        $domain->setAuthorisationCode($this->randomstring(8, true));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_ADMIN));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_TECH));
        // $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_BILLING));
        $create = new \Metaregistrar\EPP\verisignEppCreateDomainRequest($domain);        
        $create->setSubProduct('dotCOM');           
        $create->setIDNLang('RUS');                    
        $response = $this->conn->writeandread($create);                          
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateDomainResponse', $response);        
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }

    public function testCreateDomainIDNRussianWithLatinLanguage()
    {
        $contactid = $this->createContact();
        $domain_name = 'Здравствуabc.com';        
        $domain_name = idn_to_ascii($domain_name, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
        $domain = new \Metaregistrar\EPP\eppDomain($domain_name);        
        $domain->setPeriod(1);
        $domain->setRegistrant($contactid);
        $domain->setAuthorisationCode($this->randomstring(8, true));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_ADMIN));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_TECH));
        // $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_BILLING));
        $create = new \Metaregistrar\EPP\verisignEppCreateDomainRequest($domain);        
        $create->setSubProduct('dotCOM');           
        $create->setIDNLang('RUS');                    
        $response = $this->conn->writeandread($create);                          
        $this->expectException('Metaregistrar\EPP\eppException', 'Error 2306: Parameter value policy error (IDN mismatches with Language)');
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateDomainResponse', $response);        
        $this->assertFalse($response->Success());        
    }

    public function testCreateHostIDN()
    {        
        $hostname = self::randomstring(30).'.hYayQdqGt5hTöšćžß.com';        
        $hostname = idn_to_ascii($hostname, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
        $ipaddresses = ['8.8.8.8'] ;
        $host = new Metaregistrar\EPP\eppHost($hostname, $ipaddresses);
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

    public function testCreateHostIDNFail()
    {        
        $hostname = self::randomstring(30).'.hYayQdqGt5hTöšćžß.com'; 
        // $hostname = idn_to_ascii($hostname, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);       
        $ipaddresses = ['8.8.8.8'];
        $host = new Metaregistrar\EPP\eppHost($hostname, $ipaddresses);
        $this->assertInstanceOf('Metaregistrar\EPP\eppHost', $host);
        $create = new Metaregistrar\EPP\verisignEppCreateHostRequest($host);
        $create->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppCreateHostRequest', $create);
        $response = $this->conn->writeandread($create);
        $this->expectException('Metaregistrar\EPP\eppException', ' Error 2005: Parameter value syntax error');
        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateHostResponse', $response);        
        $this->assertFalse($response->Success());              
    }    

    public function testDeleteHostIDN()
    {
        $hostname = self::randomstring(30).'.hYayQdqGt5hTöšćžß.com';
        $hostname = idn_to_ascii($hostname, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);             
        $ipaddresses = ['8.8.8.8'] ;
        $host = new Metaregistrar\EPP\eppHost($hostname, $ipaddresses);
        $create = new Metaregistrar\EPP\verisignEppCreateHostRequest($host);
        $create->setSubProduct('dotCOM');        
        $response = $this->conn->writeandread($create);
        $this->assertTrue($response->Success());
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

    public function testDeleteDomainIDN() 
    {
        $contactid = $this->createContact();
        $domain_name = $this->randomstring(12).'öšćžß.com';       
        $domain_name = idn_to_ascii($domain_name, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);    
        $domain = new \Metaregistrar\EPP\eppDomain($domain_name);        
        $domain->setPeriod(1);
        $domain->setRegistrant($contactid);
        $domain->setAuthorisationCode($this->randomstring(8, true));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_ADMIN));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_TECH));
        // $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_BILLING));
        $create = new \Metaregistrar\EPP\verisignEppCreateDomainRequest($domain);
        $create->setSubProduct('dotCOM');      
        $create->setIDNLang('ENG');                    
        $response = $this->conn->writeandread($create);               
        $this->assertEquals(1000, $response->getResultCode());
        $delete = new \Metaregistrar\EPP\verisignEppDeleteDomainRequest($domain);
        $delete->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppDeleteDomainRequest', $delete);
        $response = $this->conn->writeandread($delete);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);        
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }
    
    public function testDeleteNonExistingDomainIDN() 
    {
        $contactid = $this->createContact();
        $domain_name = $this->randomstring(12).'öšćžß.com';           
        $domain_name = idn_to_ascii($domain_name, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
        $domain = new \Metaregistrar\EPP\eppDomain($domain_name);        
        $domain->setPeriod(1);
        $domain->setRegistrant($contactid);
        $domain->setAuthorisationCode($this->randomstring(8, true));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_ADMIN));
        $domain->addContact(new \Metaregistrar\EPP\eppContactHandle($contactid, \Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_TECH));        
        $delete = new \Metaregistrar\EPP\verisignEppDeleteDomainRequest($domain);
        $delete->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppDeleteDomainRequest', $delete);
        $response = $this->conn->writeandread($delete);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);        
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);
        $this->expectException('Metaregistrar\EPP\eppException', 'Error 2303: Object does not exist');
        $this->assertFalse($response->Success());
    }

}
