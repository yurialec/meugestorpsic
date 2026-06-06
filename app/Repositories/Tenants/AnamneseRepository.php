<?php

namespace App\Repositories\Tenants;

use App\Interfaces\Tenants\AnamneseRepositoryInterface;
use App\Models\Tenants\Anamnese;
use App\Models\Tenants\AnamneseTreatment;
use App\Models\Tenants\AnamneseHistoryOfPresentIllness;
use App\Models\Tenants\AnamnesePersonalHistory;
use App\Models\Tenants\AnamneseFamilyHistory;
use App\Models\Tenants\AnamneseAttitudeWithTheInterviewer;
use App\Models\Tenants\AnamneseGuidance;
use App\Models\Tenants\AnamneseNote;
use App\Models\Tenants\AnamneseContent;
use App\Models\Tenants\AnamneseSensePerception;
use App\Models\Tenants\AnamneseThought;
use App\Models\Tenants\AnamneseExpansionOfTheSelf;
use App\Models\Tenants\AnamneseDenialOfSelf;
use App\Models\Tenants\AnamneseLanguage;
use App\Models\Tenants\AnamneseMood;
use App\Models\Tenants\AnamneseAwarenessOfCurrentIllness;
use App\Models\Tenants\AnamneseDiagnosticHypothesis;
use App\Models\Tenants\AnamnesePsychicExam;
use Exception;
use Illuminate\Support\Facades\DB;
use Log;

class AnamneseRepository implements AnamneseRepositoryInterface
{
    protected $anamnese;

    public function __construct(Anamnese $anamnese)
    {
        $this->anamnese = $anamnese;
    }

    public function all()
    {
        return $this->anamnese->all();
    }

    public function find($patient_id)
    {
        try {
            if (empty($patient_id)) {
                return null;
            }

            return $this->anamnese
                ->with([
                    'attitudeWithTheInterviewer',
                    'awarenessOfCurrentIllness',
                    'contents',
                    'denialOfSelf',
                    'diagnosticHypothesis',
                    'expansionOfTheSelf',
                    'familyHistory',
                    'guidance',
                    'historyOfPresentIllness',
                    'language',
                    'mood',
                    'notes',
                    'personalHistory',
                    'psychicExam',
                    'sensePerception',
                    'thought',
                    'treatment',
                ])
                ->where('patient_id', $patient_id)
                ->first();

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao localizar anamnese', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Cria todos os registros relacionados à anamnese
     */
    public function create(
        array $anamneseData,
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
        array $psychicExamData
    ) {
        try {
            DB::transaction(function () use ($anamneseData, $treatmentData, $historyOfPresentIllnessData, $personalHistoryData, $familyHistoryData, $attitudeWithInterviewerData, $guidanceData, $notesData, $contentData, $sensePerceptionData, $thoughtData, $expansionOfSelfData, $denialOfSelfData, $languageData, $moodData, $awarenessOfCurrentIllnessData, $diagnosticHypothesisData, $psychicExamData) {
                // Busca anamnese existente ou cria nova
                $anamnese = Anamnese::firstOrNew(['patient_id' => $anamneseData['patient_id']]);
                $anamnese->fill([
                    'service_frequency' => $anamneseData['service_frequency'] ?? null
                ])->save();

                // Atualiza ou cria os relacionamentos
                $this->updateOrCreateRelated($anamnese, 'treatment', $treatmentData);
                $this->updateOrCreateRelated($anamnese, 'historyOfPresentIllness', $historyOfPresentIllnessData);
                $this->updateOrCreateRelated($anamnese, 'personalHistory', $personalHistoryData);
                $this->updateOrCreateRelated($anamnese, 'familyHistory', $familyHistoryData);
                $this->updateOrCreateRelated($anamnese, 'attitudeWithTheInterviewer', $attitudeWithInterviewerData);
                $this->updateOrCreateRelated($anamnese, 'guidance', $guidanceData);
                $this->updateOrCreateRelated($anamnese, 'notes', $notesData);
                $this->updateOrCreateRelated($anamnese, 'contents', $contentData);
                $this->updateOrCreateRelated($anamnese, 'sensePerception', $sensePerceptionData);
                $this->updateOrCreateRelated($anamnese, 'thought', $thoughtData);
                $this->updateOrCreateRelated($anamnese, 'expansionOfTheSelf', $expansionOfSelfData);
                $this->updateOrCreateRelated($anamnese, 'denialOfSelf', $denialOfSelfData);
                $this->updateOrCreateRelated($anamnese, 'language', $languageData);
                $this->updateOrCreateRelated($anamnese, 'mood', $moodData);
                $this->updateOrCreateRelated($anamnese, 'awarenessOfCurrentIllness', $awarenessOfCurrentIllnessData);
                $this->updateOrCreateRelated($anamnese, 'diagnosticHypothesis', $diagnosticHypothesisData);
                $this->updateOrCreateRelated($anamnese, 'psychicExam', $psychicExamData);
            });

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar anamnese', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Método auxiliar para atualizar ou criar relacionamentos
     */
    protected function updateOrCreateRelated(Anamnese $anamnese, string $relation, array $data)
    {
        if ($anamnese->$relation) {
            $anamnese->$relation()->update($data);
        } else {
            $anamnese->$relation()->create($data);
        }
    }


    public function update($id, array $data)
    {
        try {
            $anamnese = $this->anamnese->findOrFail($id);

            // Atualizar cada tabela individualmente aqui, se necessário
            // Exemplo:
            // $anamnese->treatment()->update([...]);

            // Depois atualiza o registro principal
            return $anamnese->update($data);
        } catch (Exception $e) {
            Log::error('Erro ao atualizar anamnese', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $anamnese = $this->anamnese->findOrFail($id);
            return $anamnese->delete();
        } catch (Exception $e) {
            Log::error('Erro ao excluir anamnese', ['error' => $e->getMessage()]);
            return false;
        }
    }
}