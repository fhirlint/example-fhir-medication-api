<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/fhir', name: 'fhir_')]
class FhirController extends AbstractController
{
    /** @var array<string, array<mixed>> */
    private array $medications = [];

    /** @var array<string, array<mixed>> */
    private array $medicationRequests = [];

    public function __construct()
    {
        $this->medications = [
            'ibuprofen-400' => [
                'resourceType' => 'Medication',
                'id' => 'ibuprofen-400',
                'status' => 'active',
                'code' => [
                    'coding' => [[
                        'system' => 'http://www.nlm.nih.gov/research/umls/rxnorm',
                        'code' => '310965',
                    ]],
                    'text' => 'Ibuprofen 400mg',
                ],
                'form' => [
                    'coding' => [[
                        'system' => 'http://snomed.info/sct',
                        'code' => '421026006',
                        'display' => 'Oral tablet',
                    ]],
                ],
            ],
            'metformin-500' => [
                'resourceType' => 'Medication',
                'id' => 'metformin-500',
                'status' => 'active',
                'code' => [
                    'coding' => [[
                        'system' => 'http://www.nlm.nih.gov/research/umls/rxnorm',
                        'code' => '861007',
                        'display' => 'Metformin Hydrochloride 500 MG Oral Tablet',
                    ]],
                    'text' => 'Metformin 500mg',
                ],
                'form' => [
                    'coding' => [[
                        'system' => 'http://snomed.info/sct',
                        'code' => '421026006',
                        'display' => 'Oral tablet',
                    ]],
                ],
            ],
        ];

        $this->medicationRequests = [
            'request-001' => [
                'resourceType' => 'MedicationRequest',
                'id' => 'request-001',
                'status' => 'active',
                'intent' => 'order',
                'medicationReference' => [
                    'reference' => 'Medication/ibuprofen-400',
                    'display' => 'Ibuprofen 400mg',
                ],
                'subject' => [
                    'reference' => 'Patient/patient-001',
                ],
                'dosageInstruction' => [[
                    'text' => '400mg orally every 8 hours as needed for pain',
                    'timing' => [
                        'repeat' => [
                            'frequency' => 3,
                            'period' => 1,
                            'periodUnit' => 'd',
                        ],
                    ],
                    'route' => [
                        'coding' => [[
                            'system' => 'http://snomed.info/sct',
                            'code' => '26643006',
                            'display' => 'Oral route',
                        ]],
                    ],
                    'doseAndRate' => [[
                        'type' => [
                            'coding' => [[
                                'system' => 'http://terminology.hl7.org/CodeSystem/dose-rate-type',
                                'code' => 'ordered',
                                'display' => 'Ordered',
                            ]],
                        ],
                        'doseQuantity' => [
                            'value' => 400,
                            'unit' => 'mg',
                            'system' => 'http://unitsofmeasure.org',
                            'code' => 'mg',
                        ],
                    ]],
                ]],
            ],
            // Intentionally incomplete: missing required 'intent' and 'subject'
            'incomplete-001' => [
                'resourceType' => 'MedicationRequest',
                'id' => 'incomplete-001',
                'status' => 'active',
                'medicationReference' => [
                    'reference' => 'Medication/ibuprofen-400',
                ],
            ],
            'request-002' => [
                'resourceType' => 'MedicationRequest',
                'id' => 'request-002',
                'status' => 'active',
                'intent' => 'order',
                'medicationReference' => [
                    'reference' => 'Medication/metformin-500',
                    'display' => 'Metformin 500mg',
                ],
                'subject' => [
                    'reference' => 'Patient/patient-002',
                ],
                'dosageInstruction' => [[
                    'text' => '500mg orally twice daily with meals',
                    'timing' => [
                        'repeat' => [
                            'frequency' => 2,
                            'period' => 1,
                            'periodUnit' => 'd',
                        ],
                    ],
                    'route' => [
                        'coding' => [[
                            'system' => 'http://snomed.info/sct',
                            'code' => '26643006',
                            'display' => 'Oral route',
                        ]],
                    ],
                    'doseAndRate' => [[
                        'type' => [
                            'coding' => [[
                                'system' => 'http://terminology.hl7.org/CodeSystem/dose-rate-type',
                                'code' => 'ordered',
                                'display' => 'Ordered',
                            ]],
                        ],
                        'doseQuantity' => [
                            'value' => 500,
                            'unit' => 'mg',
                            'system' => 'http://unitsofmeasure.org',
                            'code' => 'mg',
                        ],
                    ]],
                ]],
            ],
        ];
    }

    #[Route('/Medication', name: 'medication_list', methods: ['GET'])]
    public function medicationList(): JsonResponse
    {
        $entries = array_map(
            fn ($resource) => ['resource' => $resource],
            array_values($this->medications)
        );

        return $this->fhirJson([
            'resourceType' => 'Bundle',
            'type' => 'searchset',
            'total' => count($entries),
            'entry' => $entries,
        ]);
    }

    #[Route('/Medication/{id}', name: 'medication_read', methods: ['GET'])]
    public function medicationRead(string $id): JsonResponse
    {
        if (!isset($this->medications[$id])) {
            return $this->fhirJson([
                'resourceType' => 'OperationOutcome',
                'issue' => [[
                    'severity' => 'error',
                    'code' => 'not-found',
                    'diagnostics' => "Medication/{$id} not found",
                ]],
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->fhirJson($this->medications[$id]);
    }

    #[Route('/MedicationRequest', name: 'medication_request_list', methods: ['GET'])]
    public function medicationRequestList(): JsonResponse
    {
        $entries = array_map(
            fn ($resource) => ['resource' => $resource],
            array_values($this->medicationRequests)
        );

        return $this->fhirJson([
            'resourceType' => 'Bundle',
            'type' => 'searchset',
            'total' => count($entries),
            'entry' => $entries,
        ]);
    }

    #[Route('/MedicationRequest/{id}', name: 'medication_request_read', methods: ['GET'])]
    public function medicationRequestRead(string $id): JsonResponse
    {
        if (!isset($this->medicationRequests[$id])) {
            return $this->fhirJson([
                'resourceType' => 'OperationOutcome',
                'issue' => [[
                    'severity' => 'error',
                    'code' => 'not-found',
                    'diagnostics' => "MedicationRequest/{$id} not found",
                ]],
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->fhirJson($this->medicationRequests[$id]);
    }

    #[Route('/MedicationRequest/incomplete', name: 'medication_request_incomplete', methods: ['GET'])]
    public function medicationRequestIncomplete(): JsonResponse
    {
        // Intentionally incomplete: missing required fields 'intent' and 'subject'
        return $this->fhirJson([
            'resourceType' => 'MedicationRequest',
            'id' => 'incomplete-001',
            'status' => 'active',
            'medicationReference' => [
                'reference' => 'Medication/ibuprofen-400',
            ],
        ]);
    }

    /** @param array<mixed> $data */
    private function fhirJson(array $data, int $status = Response::HTTP_OK): JsonResponse
    {
        $response = new JsonResponse($data, $status);
        $response->headers->set('Content-Type', 'application/fhir+json');

        return $response;
    }
}
