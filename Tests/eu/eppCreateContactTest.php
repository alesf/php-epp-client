<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppCreateContactTest extends eppTestCase
{
    public function testCreateContact()
    {
        $name = 'Test name';
        $city = 'City';
        $country = 'DE';
        $organization = 'Test company';
        $address = 'Teststreet 1';
        $province = '';
        $postcode = '1000';
        $email = 'ewout@mdmailaaaa.eu';
        $telephone = '+383.31222222';
        $password = self::randomstring(8);
        $postalinfo = new Metaregistrar\EPP\eppContactPostalInfo(
            $name,
            $city,
            $country,
            $organization,
            $address,
            $province,
            $postcode,
            Metaregistrar\EPP\eppContact::TYPE_LOC
        );
        $contactinfo = new Metaregistrar\EPP\euridEppContact($postalinfo, $email, $telephone);
        $contactinfo->setPassword($password);
        $contactinfo->setContactExtType('registrant');
        $contactinfo->setContactExtLang('sl');
        $create = new Metaregistrar\EPP\euridEppCreateContactRequest($contactinfo);

        try {
            $response = $this->conn->request($create);
        } catch (eppException $e) {
            // print_r($e->getMessage());
            // print_r($e->getLastCommand());
            // $create->contactobject->ownerDocument->formatOutput = true;
            // print_r($create->contactobject->ownerDocument->saveXML());
            // exit();
        }

        $this->assertInstanceOf('Metaregistrar\EPP\eppCreateContactResponse', $response);
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }
}
