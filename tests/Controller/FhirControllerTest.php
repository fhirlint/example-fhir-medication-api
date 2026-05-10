<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FhirControllerTest extends WebTestCase
{
    public function testMedicationListReturnsBundle(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fhir/Medication');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/fhir+json');

        $body = json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertSame('Bundle', $body['resourceType']);
        $this->assertSame('searchset', $body['type']);
        $this->assertGreaterThan(0, $body['total']);
    }

    public function testMedicationReadReturnsResource(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fhir/Medication/ibuprofen-400');

        $this->assertResponseIsSuccessful();

        $body = json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertSame('Medication', $body['resourceType']);
        $this->assertSame('ibuprofen-400', $body['id']);
        $this->assertSame('active', $body['status']);
    }

    public function testMedicationReadReturnsNotFound(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fhir/Medication/does-not-exist');

        $this->assertResponseStatusCodeSame(404);

        $body = json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertSame('OperationOutcome', $body['resourceType']);
    }

    public function testMedicationRequestListReturnsBundle(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fhir/MedicationRequest');

        $this->assertResponseIsSuccessful();

        $body = json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertSame('Bundle', $body['resourceType']);
        $this->assertGreaterThan(0, $body['total']);
    }

    public function testMedicationRequestReadReturnsDosageInstruction(): void
    {
        $client = static::createClient();
        $client->request('GET', '/fhir/MedicationRequest/request-001');

        $this->assertResponseIsSuccessful();

        $body = json_decode((string) $client->getResponse()->getContent(), true);
        $this->assertSame('MedicationRequest', $body['resourceType']);
        $this->assertNotEmpty($body['dosageInstruction']);
        $this->assertSame('mg', $body['dosageInstruction'][0]['doseAndRate'][0]['doseQuantity']['unit']);
    }

    public function testFixturesMatchApiResponse(): void
    {
        $client = static::createClient();
        $fixtureDir = __DIR__ . '/../fixtures';

        foreach (glob($fixtureDir . '/medication-*.json') ?: [] as $fixturePath) {
            $fixture = json_decode((string) file_get_contents($fixturePath), true);
            $resourceType = $fixture['resourceType'];
            $id = $fixture['id'];

            $client->request('GET', "/fhir/{$resourceType}/{$id}");

            $this->assertResponseIsSuccessful(
                "Expected 200 for {$resourceType}/{$id} from fixture " . basename($fixturePath)
            );

            $body = json_decode((string) $client->getResponse()->getContent(), true);
            $this->assertSame($resourceType, $body['resourceType']);
            $this->assertSame($id, $body['id']);
        }
    }
}
