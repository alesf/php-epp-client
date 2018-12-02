<?php
include_once(dirname(__FILE__).'/eppTestCase.php');

class eppUpdateContactTest extends eppTestCase
{
    public function testUpdateContactEmail()
    {
        $contact = new Metaregistrar\EPP\eppContact($this->createContact());

        $this->assertInstanceOf('Metaregistrar\EPP\eppContact', $contact);

        $newContact = new Metaregistrar\EPP\eppContact();
        $newContact->setId($contact->getId());
        $newContact->setEmail('ftwtest@ftwtest.ftwtest');
        $handle = new Metaregistrar\EPP\eppContactHandle($contact->getId(), Metaregistrar\EPP\eppContactHandle::CONTACT_TYPE_REGISTRANT);
        $request = new Metaregistrar\EPP\eppUpdateContactRequest($handle, null, null, $newContact);
        $this->assertInstanceOf('Metaregistrar\EPP\eppUpdateContactRequest', $request);
        $response = $this->conn->writeandread($request);
        echo get_class($response);
        $this->assertInstanceOf('Metaregistrar\EPP\eppUpdateResponse', $response);
        if ($this->assertTrue($response->Success())) {
            echo "response success";
            $this->assertEquals('1000', $response->getResultCode());
        }
    }
}
