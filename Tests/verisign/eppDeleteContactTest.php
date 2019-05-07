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
        $this->assertInstanceOf('Metaregistrar\EPP\eppContactHandle', $contact);
        $delete = new Metaregistrar\EPP\verisignEppDeleteContactRequest($contact);        
        $delete->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppDeleteContactRequest', $delete);
        $response = $this->conn->writeandread($delete);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);
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
        $this->assertInstanceOf('Metaregistrar\EPP\eppContactHandle', $contact);
        $delete = new Metaregistrar\EPP\verisignEppDeleteContactRequest($contact);
        $delete->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppDeleteContactRequest', $delete);
        $response = $this->conn->writeandread($delete);        
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);         
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
        // below is domain with specific contact handle id that should return error
        //
        // <domain:name>rs59ZfVND8xp5tV92f8g.com</domain:name>       
        // <domain:contact type="admin">MRG5cc02328eab18</domain:contact>

        $message = null;
        $contacthandle = 'MRG5cc02328eab18';
        $contact = new Metaregistrar\EPP\eppContactHandle($contacthandle);
        $this->assertInstanceOf('Metaregistrar\EPP\eppContactHandle', $contact);
        $delete = new Metaregistrar\EPP\verisignEppDeleteContactRequest($contact);
        $delete->setSubProduct('dotCOM');
        $this->assertInstanceOf('Metaregistrar\EPP\verisignEppDeleteContactRequest', $delete);
        $response = $this->conn->writeandread($delete);
        $this->assertInstanceOf('Metaregistrar\EPP\eppDeleteResponse', $response);
        /* @var $response Metaregistrar\EPP\eppDeleteResponse */
        try {
            $this->assertFalse($response->Success());
        } catch (Metaregistrar\EPP\eppException $e) {
            $message = $e->getMessage();
        }
        $this->assertEquals('Error 2305: Object association prohibits operation (Contact is associated with other objects)', $message);
    }
}
