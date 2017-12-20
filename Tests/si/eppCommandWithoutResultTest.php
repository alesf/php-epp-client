<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppCommandWithoutResultTest extends eppTestCase
{
    public function testCreateCommandWithoutResult()
    {
        $name = 'test';
        $tel = '+31.00000000';
        $postalinfo = new Metaregistrar\EPP\eppContactPostalInfo($name, $name, $name, $name, $name, $name, $name, Metaregistrar\EPP\eppContact::TYPE_LOC);
        $contactinfo = new Metaregistrar\EPP\eppContact($postalinfo, $name, $tel);
        $contact = new Metaregistrar\EPP\siEppCreateContactRequest($contactinfo);
        $this->expectException('Metaregistrar\EPP\eppException', 'No valid response class found for request class Metaregistrar\EPP\siEppCreateContactRequest');
        $this->conn->writeandread($contact);
    }
}
