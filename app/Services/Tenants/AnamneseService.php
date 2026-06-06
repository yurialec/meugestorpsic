<?php

namespace App\Services\Tenants;

use App\Helpers\Utils;
use App\Repositories\Tenants\AnamneseRepository;

class AnamneseService
{
    protected $AnamneseRepository;

    public function __construct(AnamneseRepository $AnamneseRepository)
    {
        $this->AnamneseRepository = $AnamneseRepository;
    }

    public function all()
    {
        return $this->AnamneseRepository->all();
    }

    public function create($data)
    {
        // anamnese (registro principal)
        $anamneseData = [
            'patient_id' => $data['patient_id'],
            'service_frequency' => $data['service_frequency'] ?? '',
        ];

        // anamnese_treatment
        $treatmentData = [
            'main_complaint' => $data['main_complaint'],
            'secondary_complaint' => $data['secondary_complaint'],
            'symptoms' => $data['symptoms'],
        ];

        // anamnese_history_of_present_illness
        $historyOfPresentIllnessData = [
            'beginning_of_the_pathology' => $data['beginning_of_the_pathology'],
            'frequency' => $data['frequency'],
            'intensity' => $data['intensity'],
            'previous_treatments' => $data['previous_treatments'],
            'medications' => $data['medications'],
        ];

        // anamnese_personal_history
        $personalHistoryData = [
            'childhood' => $data['childhood'],
            'routine' => $data['routine'],
            'addiction' => $data['addiction'],
            'hobbies' => $data['hobbies'],
            'work' => $data['work'],
        ];

        // anamnese_family_history
        $familyHistoryData = [
            'parents' => $data['parents'],
            'siblings' => $data['siblings'],
            'partner' => $data['partner'],
            'children' => $data['children'],
            'home' => $data['home'],
        ];

        // anamnese_attitude_with_the_interviewer
        $attitudeWithInterviewerData = [
            'cooperative' => Utils::booleanToInt($data['cooperative']),
            'resistant' => Utils::booleanToInt($data['resistant']),
            'indifferent' => Utils::booleanToInt($data['indifferent']),
        ];

        // anamnese_guidance
        $guidanceData = [
            'self_identification' => Utils::booleanToInt($data['self_identification']),
            'body' => Utils::booleanToInt($data['body']),
            'temporal' => Utils::booleanToInt($data['temporal']),
            'spatial' => Utils::booleanToInt($data['spatial']),
            'pathology_oriented' => Utils::booleanToInt($data['pathology_oriented']),
        ];

        // anamnese_notes
        $notesData = [
            'attention' => $data['attention'],
            'surveillance' => $data['surveillance'],
            'tenacity' => $data['tenacity'],
            'memory' => $data['memory'],
            'intelligence' => $data['intelligence'],
        ];

        // anamnese_contents
        $contentData = [
            'obsessions' => Utils::booleanToInt($data['obsessions']),
            'hypochondriasis' => Utils::booleanToInt($data['hypochondriasis']),
            'phobias' => Utils::booleanToInt($data['phobias']),
            'delusions' => Utils::booleanToInt($data['delusions']),
        ];

        // anamnese_sense_perception
        $sensePerceptionData = [
            'normal' => Utils::booleanToInt($data['normal']),
            'hallucination' => Utils::booleanToInt($data['hallucination']),
        ];

        // anamnese_thought
        $thoughtData = [
            'accelerated' => Utils::booleanToInt($data['accelerated']),
            'slowed' => Utils::booleanToInt($data['slowed']),
            'escape' => Utils::booleanToInt($data['escape']),
            'blockage' => Utils::booleanToInt($data['blockage']),
            'wordy' => Utils::booleanToInt($data['wordy']),
            'repetition' => Utils::booleanToInt($data['repetition']),
        ];

        // anamnese_expansion_of_the_self
        $expansionOfSelfData = [
            'greatness' => Utils::booleanToInt($data['greatness']),
            'jealousy' => Utils::booleanToInt($data['jealousy']),
            'claim' => Utils::booleanToInt($data['claim']),
            'genealogical' => Utils::booleanToInt($data['genealogical']),
            'mystical_of_a_saving_mission' => Utils::booleanToInt($data['mystical_of_a_saving_mission']),
            'deification' => Utils::booleanToInt($data['deification']),
            'erotic' => Utils::booleanToInt($data['erotic']),
            'invention_or_reform' => Utils::booleanToInt($data['invention_or_reform']),
            'fantastic_ideas' => Utils::booleanToInt($data['fantastic_ideas']),
            'excessive_health' => Utils::booleanToInt($data['excessive_health']),
            'physical_capacity' => Utils::booleanToInt($data['physical_capacity']),
            'beauty' => Utils::booleanToInt($data['beauty']),
            'others' => $data['others_expansion'] ?? '',
        ];

        // anamnese_denial_of_self
        $denialOfSelfData = [
            'hypochondriac' => Utils::booleanToInt($data['hypochondriac']),
            'denial_and_bodily_transformation' => Utils::booleanToInt($data['denial_and_bodily_transformation']),
            'self_accusation' => Utils::booleanToInt($data['self_accusation']),
            'guilt' => Utils::booleanToInt($data['guilt']),
            'ruin' => Utils::booleanToInt($data['ruin']),
            'nihilism' => Utils::booleanToInt($data['nihilism']),
            'tendency_to_suicide' => Utils::booleanToInt($data['tendency_to_suicide']),
            'others' => $data['others_denial'] ?? '',
        ];

        // anamnese_language
        $languageData = [
            'dysarthria' => Utils::booleanToInt($data['dysarthria']),
            'aphasia' => Utils::booleanToInt($data['aphasia']),
            'paraphasia' => Utils::booleanToInt($data['paraphasia']),
            'neologism' => Utils::booleanToInt($data['neologism']),
            'mussitation' => Utils::booleanToInt($data['mussitation']),
            'logorrhea' => Utils::booleanToInt($data['logorrhea']),
            'para_responses' => Utils::booleanToInt($data['para_responses']),
            'others' => $data['others_language'] ?? '',
        ];

        // anamnese_mood
        $moodData = [
            'normal' => Utils::booleanToInt($data['normal_mood']),
            'elated' => Utils::booleanToInt($data['elated']),
            'low_mood' => Utils::booleanToInt($data['low_mood']),
            'sudde_change_mood' => Utils::booleanToInt($data['sudde_change_mood']),
        ];

        // anamnese_awareness_of_current_illness
        $awarenessOfCurrentIllnessData = [
            'yes' => $data['awareness'] === 'yes',
            'partially' => $data['awareness'] === 'partially',
            'no' => $data['awareness'] === 'no',
        ];

        // anamnese_diagnostic_hypothesis
        $diagnosticHypothesisData = [
            'content' => $data['content'] ?? '',
        ];

        // anamnese_psychic_exam
        $psychicExamData = [
            'appearance' => $data['appearance'] ?? '',
            'behavior' => $data['behavior'] ?? '',
            'affectivity' => $data['affectivity'] ?? '',
        ];

        // Chamada ao repository
        return $this->AnamneseRepository->create(
            $anamneseData,
            $treatmentData,
            $historyOfPresentIllnessData,
            $personalHistoryData,
            $familyHistoryData,
            $attitudeWithInterviewerData,
            $guidanceData,
            $notesData,
            $contentData,
            $sensePerceptionData,
            $thoughtData,
            $expansionOfSelfData,
            $denialOfSelfData,
            $languageData,
            $moodData,
            $awarenessOfCurrentIllnessData,
            $diagnosticHypothesisData,
            $psychicExamData,

        );
    }

    public function find($patient_id)
    {
        return $this->AnamneseRepository->find($patient_id);
    }
}