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

    /** @var array<string, array<mixed>> */
    private array $patients = [];

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
            'ibuprofen-400-de' => [
                'resourceType' => 'Medication',
                'id' => 'ibuprofen-400-de',
                'meta' => [
                    'profile' => ['https://fhir.kbv.de/StructureDefinition/KBV_PR_Base_Medication|1.5.0'],
                ],
                'status' => 'active',
                'code' => [
                    'coding' => [
                        [
                            'system' => 'http://fhir.de/CodeSystem/ifa/pzn',
                            'code' => '00123456',
                            'display' => 'Ibuprofen 400mg Filmtabletten',
                        ],
                        [
                            'system' => 'http://fhir.de/CodeSystem/bfarm/atc',
                            'version' => '2024',
                            'code' => 'M01AE01',
                            'display' => 'Ibuprofen',
                        ],
                    ],
                    'text' => 'Ibuprofen 400mg Filmtabletten',
                ],
                'form' => [
                    'coding' => [[
                        'system' => 'https://fhir.kbv.de/CodeSystem/KBV_CS_SFHIR_KBV_DARREICHUNGSFORM',
                        'version' => '1.07',
                        'code' => 'FTA',
                        'display' => 'Filmtablette',
                    ]],
                ],
                'amount' => [
                    'numerator' => [
                        'value' => 20,
                        'unit' => 'Stück',
                        'system' => 'http://unitsofmeasure.org',
                        'code' => '{tbl}',
                    ],
                    'denominator' => [
                        'value' => 1,
                        'unit' => 'Packung',
                        'system' => 'http://unitsofmeasure.org',
                        'code' => '{Package}',
                    ],
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
            'request-de-001' => [
                'resourceType' => 'MedicationRequest',
                'id' => 'request-de-001',
                'status' => 'active',
                'intent' => 'order',
                'medicationReference' => [
                    'reference' => 'Medication/ibuprofen-400-de',
                    'display' => 'Ibuprofen 400mg Filmtabletten',
                ],
                'subject' => [
                    'reference' => 'Patient/patient-001',
                ],
                'dosageInstruction' => [[
                    'text' => '1 Tablette bis zu 3-mal täglich bei Schmerzen',
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
                'dispenseRequest' => [
                    'quantity' => [
                        'value' => 1,
                        'unit' => 'Packung',
                        'system' => 'http://unitsofmeasure.org',
                        'code' => '{Package}',
                    ],
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

        $this->patients = [
            'patient-001' => [
                'resourceType' => 'Patient',
                'id' => 'patient-001',
                'name' => [[
                    'family' => 'Smith',
                    'given' => ['John'],
                ]],
                'gender' => 'male',
                'birthDate' => '1980-01-15',
            ],
            'patient-002' => [
                'resourceType' => 'Patient',
                'id' => 'patient-002',
                'name' => [[
                    'family' => 'Müller',
                    'given' => ['Anna'],
                ]],
                'gender' => 'female',
                'birthDate' => '1975-06-20',
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

    #[Route('/Patient', name: 'patient_list', methods: ['GET'])]
    public function patientList(): JsonResponse
    {
        $entries = array_map(
            fn ($resource) => ['resource' => $resource],
            array_values($this->patients)
        );

        return $this->fhirJson([
            'resourceType' => 'Bundle',
            'type' => 'searchset',
            'total' => count($entries),
            'entry' => $entries,
        ]);
    }

    #[Route('/Patient/{id}', name: 'patient_read', methods: ['GET'])]
    public function patientRead(string $id): JsonResponse
    {
        if (!isset($this->patients[$id])) {
            return $this->fhirJson([
                'resourceType' => 'OperationOutcome',
                'issue' => [[
                    'severity' => 'error',
                    'code' => 'not-found',
                    'diagnostics' => "Patient/{$id} not found",
                ]],
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->fhirJson($this->patients[$id]);
    }

    /** @param array<mixed> $data */
    private function fhirJson(array $data, int $status = Response::HTTP_OK): JsonResponse
    {
        $response = new JsonResponse($data, $status);
        $response->headers->set('Content-Type', 'application/fhir+json');

        return $response;
    }
}
