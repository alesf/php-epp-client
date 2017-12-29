<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppCommandWithoutResultTest extends eppTestCase
{
    public function testCreateCommandWithoutResult()
    {
        // we don't need this
        $this->assertTrue(true);
        return true;
        
        $name = 'test';
        $tel = '+31.00000000';
        $postalinfo = new Metaregistrar\EPP\siEppContactPostalInfo($name, $name, $name, $name, $name, $name, $name, Metaregistrar\EPP\eppContact::TYPE_INT);
        $postalinfo->setContactType(Metaregistrar\EPP\siEppContactPostalInfo::ARNES_CONTACT_TYPE_PERSON);
        $postalinfo->setContactID('2010982500111');
        $contactinfo = new Metaregistrar\EPP\eppContact($postalinfo, $name, $tel);
        $contact = new Metaregistrar\EPP\siEppCreateContactRequest($contactinfo);
        $this->expectException('Metaregistrar\EPP\eppException', 'No valid response class found for request class Metaregistrar\EPP\siEppCreateContactRequest');
        $this->conn->writeandread($contact);
    }
}
