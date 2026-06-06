<?php

namespace App\Interfaces\Tenants;

interface AnamneseRepositoryInterface
{
    public function all();
    public function find($patient_id);
    public function create(
        array $treatmentData,
        array $historyOfPresentIllnessData,
        array $personalHistoryData,
        array $familyHistoryData,
        array $attitudeWithInterviewerData,
        array $guidanceData,
        array $notesData,
        array $contentData,
        array $sensePerceptionData,
        array $thoughtData,
        array $expansionOfSelfData,
        array $denialOfSelfData,
        array $languageData,
        array $moodData,
        array $awarenessOfCurrentIllnessData,
        array $diagnosticHypothesisData,
        array $psychicExamData,
        array $anamneseData
    );
    public function update($id, array $data);
    public function delete($id);
}