<?php

namespace Tests\Unit\Services\ModelService\ContactServiceTest;

use App\Models\Contact;
use App\Services\ModelServices\ContactService;
use Tests\Unit\BaseTest;

class DeleteTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccess()
    {
        $contact = Contact::create([
            'title' => 'name',
        ]);

        $contactServiceMock = $this->getMockService(ContactService::class, [], [
            new Contact(),
        ]);
        $response = $contactServiceMock->delete($contact->id);
        $this->assertEquals(true, $response);
    }

    public function testContactNotFound()
    {
        $contactServiceMock = $this->getMockService(ContactService::class, [], [
            new Contact(),
        ]);
        $response = $contactServiceMock->delete(1);
        $this->assertEquals(false, $response);
    }
}
