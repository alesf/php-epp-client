<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppDeleteContactTest extends eppTestCase
{

    /**
     * Test succesful contact deletion
     * @throws \Metaregistrar\EPP\eppException
     */
    public function testDeleteContact()
    {
        $contacthandle = $this->createContact();
        $contact = new Metaregistrar\EPP\eppContactHandle($contacthandle);
        $delete = new Metaregistrar\EPP\eppDeleteContactRequest($contact);
        $response = $this->conn->writeandread($delete);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);
        /* @var $response Metaregistrar\EPP\eppDeleteResponse */
        $this->assertTrue($response->Success());
        $this->assertEquals('Command completed successfully', $response->getResultMessage());
        $this->assertEquals(1000, $response->getResultCode());
    }

    /**
     * Test unsuccesful deletion because contact does not exist
     * @throws \Metaregistrar\EPP\eppException
     */
    public function testDeleteNonexistentContact()
    {
        $message = null;
        $contacthandle = 'O99999';
        $contact = new Metaregistrar\EPP\eppContactHandle($contacthandle);
        $delete = new Metaregistrar\EPP\eppDeleteContactRequest($contact);
        // echo $delete->saveXML();
        $response = $this->conn->writeandread($delete);
        // print_r($response);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);
        /* @var $response Metaregistrar\EPP\eppDeleteResponse */
        try {
            $this->assertFalse($response->Success());
        } catch (Metaregistrar\EPP\eppException $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals('Error 2303: Object does not exist', $message);
    }

    /**
     * Test unsuccesful deletion because contact is linked to a domain
     * @throws \Metaregistrar\EPP\eppException
     */
    public function testDeleteWrongContact()
    {
        // TODO: create contact that is linked to domain and then try to remove it
        // it should return an erroe

        $message = null;
        $contacthandle = self::randomstring(8);
        $contact = new Metaregistrar\EPP\eppContactHandle($contacthandle);
        $delete = new Metaregistrar\EPP\eppDeleteContactRequest($contact);
        $response = $this->conn->writeandread($delete);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);
        /* @var $response Metaregistrar\EPP\eppDeleteResponse */
        try {
            $this->assertFalse($response->Success());
        } catch (Metaregistrar\EPP\eppException $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals('Error 2303: Object does not exist', $message);
    }

    /**
     * Test
    */
    public function testDeleteAssociatedContact()
    {
        $contactid = $this->createContact();
        $domain1 = $this->createDomain($this->randomstring(20).'.si', $contactid, true);
        $delete = new Metaregistrar\EPP\eppDeleteContactRequest(new Metaregistrar\EPP\eppContactHandle($contactid));
        $response = $this->conn->writeandread($delete);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);
        $this->expectException('Metaregistrar\EPP\eppException', '2305: Object association prohibits operation');
        $this->assertFalse($response->Success());

    }
}
