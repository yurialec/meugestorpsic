<?php

namespace App\Http\Requests\Tenant\Anamnese;

use Illuminate\Foundation\Http\FormRequest;

class AnamneseStoreUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            // Dados principais da anamnese
            'patient_id' => 'required|uuid|exists:patients,id',
            'service_frequency' => 'sometimes|max:1000',

            // Tratamento
            'treatment.main_complaint' => 'sometimes|string',
            'treatment.secondary_complaint' => 'nullable|max:1000',
            'treatment.symptoms' => 'nullable|max:1000',

            // Histórico da doença atual
            'history_of_present_illness.beginning_of_the_pathology' => 'sometimes|max:1000',
            'history_of_present_illness.frequency' => 'sometimes|max:1000',
            'history_of_present_illness.intensity' => 'sometimes|max:1000',
            'history_of_present_illness.previous_treatments' => 'sometimes|max:1000',
            'history_of_present_illness.medications' => 'sometimes|max:1000',

            // Histórico pessoal
            'personal_history.childhood' => 'sometimes|max:1000',
            'personal_history.routine' => 'sometimes|max:1000',
            'personal_history.addiction' => 'sometimes|max:1000',
            'personal_history.hobbies' => 'sometimes|max:1000',
            'personal_history.work' => 'sometimes|max:1000',

            // Histórico familiar
            'family_history.parents' => 'sometimes|max:1000',
            'family_history.siblings' => 'sometimes|max:1000',
            'family_history.partner' => 'sometimes|max:1000',
            'family_history.children' => 'sometimes|max:1000',
            'family_history.home' => 'sometimes|max:1000',

            // Exame psíquico
            'psychic_exam.appearance' => 'sometimes|max:1000',
            'psychic_exam.behavior' => 'sometimes|max:1000',
            'psychic_exam.sense_perception' => 'sometimes|string|max:50',
            'psychic_exam.thought_process' => 'sometimes|string',
            'psychic_exam.thought_content' => 'sometimes|string',
            'psychic_exam.ego_expansion' => 'sometimes|string',
            'psychic_exam.ego_retraction' => 'sometimes|string',
            'psychic_exam.ego_denial' => 'sometimes|string',
            'psychic_exam.language' => 'sometimes|string',
            'psychic_exam.affectivity' => 'sometimes|string',
            'psychic_exam.mood' => 'sometimes|string|max:50',
            'psychic_exam.disease_awareness' => 'sometimes|string|max:50',

            // Atitude com o entrevistador
            'attitude_with_the_interviewer.cooperative' => 'sometimes|boolean',
            'attitude_with_the_interviewer.resistant' => 'sometimes|boolean',
            'attitude_with_the_interviewer.indifferent' => 'sometimes|boolean',

            // Orientação
            'guidance.self_identification' => 'sometimes|boolean',
            'guidance.body' => 'sometimes|boolean',
            'guidance.temporal' => 'sometimes|boolean',
            'guidance.spatial' => 'sometimes|boolean',
            'guidance.pathology_oriented' => 'sometimes|boolean',

            // Notas
            'notes.attention' => 'sometimes|string',
            'notes.surveillance' => 'sometimes|string',
            'notes.tenacity' => 'sometimes|string',
            'notes.memory' => 'sometimes|string',
            'notes.intelligence' => 'sometimes|string',

            // Hipótese diagnóstica
            'diagnostic_hypothesis.content' => 'sometimes|string',

            // Percepção sensorial
            'sense_perception.normal' => 'sometimes|boolean',
            'sense_perception.hallucination' => 'sometimes|boolean',

            // Pensamento
            'thought.accelerated' => 'sometimes|boolean',
            'thought.slowed' => 'sometimes|boolean',
            'thought.escape' => 'sometimes|boolean',
            'thought.blockage' => 'sometimes|boolean',
            'thought.wordy' => 'sometimes|boolean',
            'thought.repetition' => 'sometimes|boolean',

            // Conteúdos
            'contents.obsessions' => 'sometimes|boolean',
            'contents.hypochondriasis' => 'sometimes|boolean',
            'contents.phobias' => 'sometimes|boolean',
            'contents.delusions' => 'sometimes|boolean',

            // Expansão do ego
            'expansion_of_the_self.greatness' => 'sometimes|boolean',
            'expansion_of_the_self.jealousy' => 'sometimes|boolean',
            'expansion_of_the_self.claim' => 'sometimes|boolean',
            'expansion_of_the_self.genealogical' => 'sometimes|boolean',
            'expansion_of_the_self.mystical_of_a_saving_mission' => 'sometimes|boolean',
            'expansion_of_the_self.deification' => 'sometimes|boolean',
            'expansion_of_the_self.erotic' => 'sometimes|boolean',
            'expansion_of_the_self.invention_or_reform' => 'sometimes|boolean',
            'expansion_of_the_self.fantastic_ideas' => 'sometimes|boolean',
            'expansion_of_the_self.excessive_health' => 'sometimes|boolean',
            'expansion_of_the_self.physical_capacity' => 'sometimes|boolean',
            'expansion_of_the_self.beauty' => 'sometimes|boolean',
            'expansion_of_the_self.others' => 'nullable|string',

            // Negação do self
            'denial_of_self.hypochondriac' => 'sometimes|boolean',
            'denial_of_self.denial_and_bodily_transformation' => 'sometimes|boolean',
            'denial_of_self.self_accusation' => 'sometimes|boolean',
            'denial_of_self.guilt' => 'sometimes|boolean',
            'denial_of_self.ruin' => 'sometimes|boolean',
            'denial_of_self.nihilism' => 'sometimes|boolean',
            'denial_of_self.tendency_to_suicide' => 'sometimes|boolean',
            'denial_of_self.others' => 'nullable|string',

            // Linguagem
            'language.dysarthria' => 'sometimes|boolean',
            'language.aphasia' => 'sometimes|boolean',
            'language.paraphasia' => 'sometimes|boolean',
            'language.neologism' => 'sometimes|boolean',
            'language.mussitation' => 'sometimes|boolean',
            'language.logorrhea' => 'sometimes|boolean',
            'language.para_responses' => 'sometimes|boolean',
            'language.others' => 'nullable|string',

            // Humor
            'mood.normal' => 'sometimes|boolean',
            'mood.elated' => 'sometimes|boolean',
            'mood.low_mood' => 'sometimes|boolean',
            'mood.sudde_change_mood' => 'sometimes|boolean',

            // Consciência da doença atual
            'awareness_of_current_illness.yes' => 'sometimes|boolean',
            'awareness_of_current_illness.partially' => 'sometimes|boolean',
            'awareness_of_current_illness.no' => 'sometimes|boolean',
        ];

        return $rules;
    }

    public function attributes()
    {
        return [
            'patient_id' => 'paciente',
            'service_frequency' => 'frequência de serviço',

            'treatment.main_complaint' => 'queixa principal',
            'treatment.secondary_complaint' => 'queixa secundária',
            'treatment.symptoms' => 'sintomas',

            'history_of_present_illness.beginning_of_the_pathology' => 'início da patologia',
            'history_of_present_illness.frequency' => 'frequência',
            'history_of_present_illness.intensity' => 'intensidade',
            'history_of_present_illness.previous_treatments' => 'tratamentos anteriores',
            'history_of_present_illness.medications' => 'medicações',

            'personal_history.childhood' => 'infância',
            'personal_history.routine' => 'rotina',
            'personal_history.addiction' => 'vícios',
            'personal_history.hobbies' => 'hobbies',
            'personal_history.work' => 'trabalho',

            'family_history.parents' => 'pais',
            'family_history.siblings' => 'irmãos',
            'family_history.partner' => 'companheiro(a)',
            'family_history.children' => 'filhos',
            'family_history.home' => 'lar',

            'psychic_exam.appearance' => 'aparência',
            'psychic_exam.behavior' => 'comportamento',
            'psychic_exam.sense_perception' => 'percepção sensorial',
            'psychic_exam.thought_process' => 'processo do pensamento',
            'psychic_exam.thought_content' => 'conteúdo do pensamento',
            'psychic_exam.ego_expansion' => 'expansão do ego',
            'psychic_exam.ego_retraction' => 'retração do ego',
            'psychic_exam.ego_denial' => 'negação do ego',
            'psychic_exam.language' => 'linguagem',
            'psychic_exam.affectivity' => 'afetividade',
            'psychic_exam.mood' => 'humor',
            'psychic_exam.disease_awareness' => 'consciência da doença',

            'attitude_with_the_interviewer.cooperative' => 'cooperativo',
            'attitude_with_the_interviewer.resistant' => 'resistente',
            'attitude_with_the_interviewer.indifferent' => 'indiferente',

            'guidance.self_identification' => 'auto-identificação',
            'guidance.body' => 'corpo',
            'guidance.temporal' => 'temporal',
            'guidance.spatial' => 'espacial',
            'guidance.pathology_oriented' => 'orientação patológica',

            'notes.attention' => 'atenção',
            'notes.surveillance' => 'vigilância',
            'notes.tenacity' => 'tenacidade',
            'notes.memory' => 'memória',
            'notes.intelligence' => 'inteligência',

            'diagnostic_hypothesis.content' => 'conteúdo',

            'sense_perception.normal' => 'normal',
            'sense_perception.hallucination' => 'alucinação',

            'thought.accelerated' => 'acelerado',
            'thought.slowed' => 'lento',
            'thought.escape' => 'fuga',
            'thought.blockage' => 'bloqueio',
            'thought.wordy' => 'verboso',
            'thought.repetition' => 'repetição',

            'contents.obsessions' => 'obsessões',
            'contents.hypochondriasis' => 'hipocondria',
            'contents.phobias' => 'fobias',
            'contents.delusions' => 'delírios',

            'expansion_of_the_self.greatness' => 'grandiosidade',
            'expansion_of_the_self.jealousy' => 'ciúme',
            'expansion_of_the_self.claim' => 'reivindicação',
            'expansion_of_the_self.genealogical' => 'genealógico',
            'expansion_of_the_self.mystical_of_a_saving_mission' => 'missão salvífica mística',
            'expansion_of_the_self.deification' => 'deificação',
            'expansion_of_the_self.erotic' => 'erótico',
            'expansion_of_the_self.invention_or_reform' => 'invenção ou reforma',
            'expansion_of_the_self.fantastic_ideas' => 'ideias fantásticas',
            'expansion_of_the_self.excessive_health' => 'saúde excessiva',
            'expansion_of_the_self.physical_capacity' => 'capacidade física',
            'expansion_of_the_self.beauty' => 'beleza',
            'expansion_of_the_self.others' => 'outros',

            'denial_of_self.hypochondriac' => 'hipocondríaco',
            'denial_of_self.denial_and_bodily_transformation' => 'negação e transformação corporal',
            'denial_of_self.self_accusation' => 'auto-acusação',
            'denial_of_self.guilt' => 'culpa',
            'denial_of_self.ruin' => 'ruína',
            'denial_of_self.nihilism' => 'niilismo',
            'denial_of_self.tendency_to_suicide' => 'tendência ao suicídio',
            'denial_of_self.others' => 'outros',

            'language.dysarthria' => 'disartria',
            'language.aphasia' => 'afasia',
            'language.paraphasia' => 'parafasia',
            'language.neologism' => 'neologismo',
            'language.mussitation' => 'musitação',
            'language.logorrhea' => 'logorreia',
            'language.para_responses' => 'pararespostas',
            'language.others' => 'outros',

            'mood.normal' => 'normal',
            'mood.elated' => 'elevado',
            'mood.low_mood' => 'humor baixo',
            'mood.sudde_change_mood' => 'mudança súbita de humor',

            'awareness_of_current_illness.yes' => 'sim',
            'awareness_of_current_illness.partially' => 'parcialmente',
            'awareness_of_current_illness.no' => 'não',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
            'string' => 'O campo :attribute deve ser um texto.',
            'max' => 'O campo :attribute não pode ter mais que :max caracteres.',
            'uuid' => 'O campo :attribute deve ser um UUID válido.',
            'exists' => 'O :attribute selecionado é inválido.',
        ];
    }
}