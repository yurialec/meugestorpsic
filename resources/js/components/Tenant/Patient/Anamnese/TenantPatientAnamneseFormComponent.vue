<template>
    <div class="container-fluid px-4 mt-2">
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-dark">Anamnese</h5>
                            <a class="btn btn-secondary btn-sm" :href="urlIndexPatients">Voltar</a>
                        </div>
                        <div class="progress mt-3" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" :style="'width: ' + progress + '%'"
                                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body bg-soft-white">
                <!-- Stepper -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div v-for="(step, index) in steps" :key="index" class="step"
                        :class="{ 'active': currentStep === index + 1, 'completed': currentStep > index + 1 }">
                        <div class="step-circle">{{ index + 1 }}</div>
                        <div class="step-label">{{ step }}</div>
                    </div>
                </div>

                <div v-if="loading" class="d-flex justify-content-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden"></span>
                    </div>
                </div>

                <form v-else @submit.prevent="submitForm">
                    <!-- ETAPA 1: ATENDIMENTO (TABELA anamnese_treatment) -->
                    <div v-show="currentStep === 1" class="step-content animate__animated animate__fadeIn">
                        <h4 class="mb-4 text-teal">Atendimento</h4>
                        <div class="row">
                            <!-- <div class="col-md-4 mb-3">
                                <label class="form-label">Data do Atendimento</label>
                                <input type="date" class="form-control" v-model="formData.service_date">
                            </div> -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Frequência</label>
                                <input type="text" class="form-control" v-model="formData.service_frequency">
                            </div>
                            <!-- <div class="col-md-4 mb-3">
                                <label class="form-label">Hora</label>
                                <input type="time" class="form-control" v-model="formData.service_time">
                            </div> -->
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Queixa Principal</label>
                                <textarea class="form-control" rows="3" v-model="formData.main_complaint"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Queixa Secundária</label>
                                <textarea class="form-control" rows="3"
                                    v-model="formData.secondary_complaint"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sintomas</label>
                                <textarea class="form-control" rows="3" v-model="formData.symptoms"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ETAPA 2: HISTÓRICO DA DOENÇA ATUAL (TABELA anamnese_history_of_present_illness) -->
                    <div v-show="currentStep === 2" class="step-content animate__animated animate__fadeIn">
                        <h4 class="mb-4 text-teal">Histórico da Doença Atual</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Início da Patologia</label>
                                <textarea class="form-control" rows="3"
                                    v-model="formData.beginning_of_the_pathology"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Frequência</label>
                                <textarea class="form-control" rows="3" v-model="formData.frequency"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Intensidade</label>
                                <textarea class="form-control" rows="3" v-model="formData.intensity"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tratamentos Anteriores</label>
                                <textarea class="form-control" rows="3"
                                    v-model="formData.previous_treatments"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Medicamentos em Uso</label>
                                <textarea class="form-control" rows="3" v-model="formData.medications"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ETAPA 3: HISTÓRICO PESSOAL (TABELA anamnese_personal_history) -->
                    <div v-show="currentStep === 3" class="step-content animate__animated animate__fadeIn">
                        <h4 class="mb-4 text-teal">Histórico Pessoal</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Infância</label>
                                <textarea class="form-control" rows="3" v-model="formData.childhood"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rotina</label>
                                <textarea class="form-control" rows="3" v-model="formData.routine"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Vícios</label>
                                <textarea class="form-control" rows="3" v-model="formData.addiction"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hobbies</label>
                                <textarea class="form-control" rows="3" v-model="formData.hobbies"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Trabalho</label>
                                <textarea class="form-control" rows="3" v-model="formData.work"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ETAPA 4: HISTÓRICO FAMILIAR (TABELA anamnese_family_history) -->
                    <div v-show="currentStep === 4" class="step-content animate__animated animate__fadeIn">
                        <h4 class="mb-4 text-teal">Histórico Familiar</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pais</label>
                                <textarea class="form-control" rows="3" v-model="formData.parents"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Irmãos</label>
                                <textarea class="form-control" rows="3" v-model="formData.siblings"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cônjuge</label>
                                <textarea class="form-control" rows="3" v-model="formData.partner"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Filhos</label>
                                <textarea class="form-control" rows="3" v-model="formData.children"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Lar</label>
                                <textarea class="form-control" rows="3" v-model="formData.home"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ETAPA 5: EXAME PSÍQUICO (TABELA anamnese_psychic_exam e relacionadas) -->
                    <div v-show="currentStep === 5" class="step-content animate__animated animate__fadeIn">
                        <h4 class="mb-4 text-teal">Exame Psíquico</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Aparência</label>
                                <textarea class="form-control" rows="3" v-model="formData.appearance"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Comportamento</label>
                                <textarea class="form-control" rows="3" v-model="formData.behavior"></textarea>
                            </div>
                        </div>

                        <!-- Atitude para com o entrevistador (TABELA anamnese_attitude_with_the_interviewer) -->
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Atitude para com o entrevistador</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" v-model="formData.cooperative"
                                        id="attitude1">
                                    <label class="form-check-label" for="attitude1">Cooperativo</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" v-model="formData.resistant"
                                        id="attitude2">
                                    <label class="form-check-label" for="attitude2">Resistente</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" v-model="formData.indifferent"
                                        id="attitude3">
                                    <label class="form-check-label" for="attitude3">Indiferente</label>
                                </div>
                            </div>

                            <!-- Orientação (TABELA anamnese_guidance) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Orientação</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        v-model="formData.self_identification" id="orientation1">
                                    <label class="form-check-label" for="orientation1">Auto Identificatória</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" v-model="formData.body"
                                        id="orientation2">
                                    <label class="form-check-label" for="orientation2">Corporal</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" v-model="formData.temporal"
                                        id="orientation3">
                                    <label class="form-check-label" for="orientation3">Temporal</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" v-model="formData.spatial"
                                        id="orientation4">
                                    <label class="form-check-label" for="orientation4">Espacial</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        v-model="formData.pathology_oriented" id="orientation5">
                                    <label class="form-check-label" for="orientation5">Orientado em relação a
                                        patologia</label>
                                </div>
                            </div>
                        </div>

                        <!-- Notas (TABELA anamnese_notes) -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Atenção</label>
                                <input type="text" class="form-control" v-model="formData.attention">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Vigilância</label>
                                <input type="text" class="form-control" v-model="formData.surveillance">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tenacidade</label>
                                <input type="text" class="form-control" v-model="formData.tenacity">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Memória</label>
                                <input type="text" class="form-control" v-model="formData.memory">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Inteligência</label>
                                <input type="text" class="form-control" v-model="formData.intelligence">
                            </div>
                        </div>
                    </div>

                    <!-- ETAPA 6: CONTEÚDOS PSÍQUICOS (TABELA anamnese_contents) -->
                    <div v-show="currentStep === 6" class="step-content animate__animated animate__fadeIn">
                        <h4 class="mb-4 text-teal">Conteúdos Psíquicos</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Obsessões</label>
                                <textarea class="form-control" rows="3" v-model="formData.obsessions"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hipocondria</label>
                                <textarea class="form-control" rows="3" v-model="formData.hypochondriasis"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fobias</label>
                                <textarea class="form-control" rows="3" v-model="formData.phobias"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Delírios</label>
                                <textarea class="form-control" rows="3" v-model="formData.delusions"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ETAPA 7: PERCEPÇÃO, PENSAMENTO E LINGUAGEM -->
                    <div v-show="currentStep === 7" class="step-content animate__animated animate__fadeIn">
                        <!-- Senso percepção (TABELA anamnese_sense_perception) -->
                        <div class="row">
                            <div class="col-md-3 mb-4">
                                <label class="fw-bold">Senso percepção:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" v-model="formData.normal">
                                    <label class="form-check-label">normal</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" v-model="formData.hallucination">
                                    <label class="form-check-label">alucinação</label>
                                </div>
                            </div>

                            <!-- Pensamento (TABELA anamnese_thought) -->
                            <div class="col-md-3 mb-4">
                                <label class="fw-bold">Pensamento:</label>
                                <div class="form-check" v-for="item in pensamentoOptions" :key="item">
                                    <input class="form-check-input" type="checkbox" :value="item"
                                        v-model="formData.pensamento">
                                    <label class="form-check-label">{{ item }}</label>
                                </div>
                            </div>

                            <!-- Expansão do eu (TABELA anamnese_expansion_of_the_self) -->
                            <div class="col-md-3 mb-4">
                                <label class="fw-bold">Expansão do eu:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" v-model="formData.greatness">
                                    <label class="form-check-label">grandeza</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" v-model="formData.jealousy">
                                    <label class="form-check-label">ciúme</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" v-model="formData.claim">
                                    <label class="form-check-label">reivindicação</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" v-model="formData.genealogical">
                                    <label class="form-check-label">genealógico</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        v-model="formData.mystical_of_a_saving_mission">
                                    <label class="form-check-label">místico, de missão salvadora</label>
                                </div>
                                <input type="text" class="form-control mt-2" placeholder="outros..."
                                    v-model="formData.others_expansion">
                            </div>
                        </div>
                    </div>

                    <!-- ETAPA 8: RETRAÇÃO, NEGAÇÃO E LINGUAGEM -->
                    <div v-show="currentStep === 8" class="step-content animate__animated animate__fadeIn">
                        <h4 class="mb-4 text-teal">Retratação, Negação do Eu, Linguagem e Afetividade</h4>

                        <!-- Negação do eu (TABELA anamnese_denial_of_self) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Negação do eu:</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            v-model="formData.hypochondriac">
                                        <label class="form-check-label">hipocondríaco</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            v-model="formData.denial_and_bodily_transformation">
                                        <label class="form-check-label">negação e transformação corporal</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            v-model="formData.self_accusation">
                                        <label class="form-check-label">auto acusação</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" v-model="formData.guilt">
                                        <label class="form-check-label">culpa</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" v-model="formData.ruin">
                                        <label class="form-check-label">ruína</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" v-model="formData.nihilism">
                                        <label class="form-check-label">niilismo</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            v-model="formData.tendency_to_suicide">
                                        <label class="form-check-label">tendência ao suicídio</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            v-model="formData.others_denial">
                                        <label class="form-check-label">outros</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Linguagem (TABELA anamnese_language) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Linguagem:</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" v-model="formData.dysarthria">
                                        <label class="form-check-label">disartrias (má articulação)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" v-model="formData.aphasia">
                                        <label class="form-check-label">afasias, verbigeração (repetição de
                                            palavras)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" v-model="formData.paraphasia">
                                        <label class="form-check-label">parafasia</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" v-model="formData.neologism">
                                        <label class="form-check-label">neologismo</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" v-model="formData.mussitation">
                                        <label class="form-check-label">musitação (voz murmurada em tom baixo)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" v-model="formData.logorrhea">
                                        <label class="form-check-label">logorreia (fluxo incessante e
                                            ininteligível)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            v-model="formData.para_responses">
                                        <label class="form-check-label">para-respostas (responde algo que não tem
                                            relação)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            v-model="formData.others_language">
                                        <label class="form-check-label">outros</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Afetividade -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Afetividade:</label>
                            <textarea class="form-control" rows="3" v-model="formData.affectivity"></textarea>
                        </div>
                    </div>

                    <!-- ETAPA 9: HUMOR, CONSCIÊNCIA E HIPÓTESE DIAGNÓSTICA -->
                    <div v-show="currentStep === 9" class="step-content animate__animated animate__fadeIn">
                        <h4 class="mb-4 text-teal">Humor, Consciência da Doença e Hipótese Diagnóstica</h4>

                        <!-- Humor (TABELA anamnese_mood) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Humor:</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" v-model="formData.normal_mood">
                                        <label class="form-check-label">normal</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" v-model="formData.elated">
                                        <label class="form-check-label">exaltado</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" v-model="formData.low_mood">
                                        <label class="form-check-label">baixa de humor</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            v-model="formData.sudde_change_mood">
                                        <label class="form-check-label">quebra súbita da tonalidade do humor durante a
                                            entrevista</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Consciência da doença atual (TABELA anamnese_awareness_of_current_illness) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Consciência da doença atual:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="yes" v-model="formData.awareness"
                                    name="awareness">
                                <label class="form-check-label">sim</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="partially"
                                    v-model="formData.awareness" name="awareness">
                                <label class="form-check-label">parcialmente</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="no" v-model="formData.awareness"
                                    name="awareness">
                                <label class="form-check-label">não</label>
                            </div>
                        </div>

                        <!-- Hipótese Diagnóstica (TABELA anamnese_diagnostic_hypothesis) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hipótese Diagnóstica:</label>
                            <textarea class="form-control" rows="12" v-model="formData.content"
                                placeholder="Escreva aqui..."></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary btn-sm" @click="prevStep"
                            v-if="currentStep > 1">
                            <i class="bi bi-arrow-left me-1"></i> Voltar
                        </button>
                        <div v-else></div>

                        <button type="button" class="btn btn-teal btn-sm" @click="nextStep"
                            v-if="currentStep < steps.length">
                            Continuar <i class="bi bi-arrow-right ms-1"></i>
                        </button>

                        <button type="submit" class="btn btn-success btn-sm" v-if="currentStep === steps.length">
                            <i class="bi bi-check-circle me-1"></i> Finalizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        patient_id: String,
        urlIndexPatients: String,
    },
    data() {
        return {
            loading: false,
            currentStep: 1,
            steps: [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ],
            formData: {
                pensamento: [],

                // Dados gerais
                service_date: '',
                service_frequency: '',
                service_time: '',

                // anamnese_treatment
                main_complaint: '',
                secondary_complaint: '',
                symptoms: '',

                // anamnese_history_of_present_illness
                beginning_of_the_pathology: '',
                frequency: '',
                intensity: '',
                previous_treatments: '',
                medications: '',

                // anamnese_personal_history
                childhood: '',
                routine: '',
                addiction: '',
                hobbies: '',
                work: '',

                // anamnese_family_history
                parents: '',
                siblings: '',
                partner: '',
                children: '',
                home: '',

                // anamnese_attitude_with_the_interviewer
                cooperative: false,
                resistant: false,
                indifferent: false,

                // anamnese_guidance
                self_identification: false,
                body: false,
                temporal: false,
                spatial: false,
                pathology_oriented: false,

                // anamnese_notes
                attention: '',
                surveillance: '',
                tenacity: '',
                memory: '',
                intelligence: '',

                // anamnese_contents
                obsessions: '',
                hypochondriasis: '',
                phobias: '',
                delusions: '',

                // anamnese_sense_perception
                normal: false,
                hallucination: false,

                // anamnese_thought
                accelerated: false,
                slowed: false,
                escape: false,
                blockage: false,
                wordy: false,
                repetition: false,

                // anamnese_expansion_of_the_self
                greatness: false,
                jealousy: false,
                claim: false,
                genealogical: false,
                mystical_of_a_saving_mission: false,
                deification: false,
                erotic: false,
                invention_or_reform: false,
                fantastic_ideas: false,
                excessive_health: false,
                physical_capacity: false,
                beauty: false,
                others_expansion: '',

                // anamnese_denial_of_self
                hypochondriac: false,
                denial_and_bodily_transformation: false,
                self_accusation: false,
                guilt: false,
                ruin: false,
                nihilism: false,
                tendency_to_suicide: false,
                others_denial: '',

                // anamnese_language
                dysarthria: false,
                aphasia: false,
                paraphasia: false,
                neologism: false,
                mussitation: false,
                logorrhea: false,
                para_responses: false,
                others_language: '',

                // anamnese_mood
                normal_mood: false,
                elated: false,
                low_mood: false,
                sudde_change_mood: false,

                // anamnese_awareness_of_current_illness
                awareness: '',

                // anamnese_diagnostic_hypothesis
                content: '',

                // anamnese_psychic_exam
                appearance: '',
                behavior: '',
                affectivity: ''
            },
            pensamentoOptions: [
                'acelerado', 'retardado', 'fuga', 'bloqueio', 'prolixo', 'repetição'
            ]
        }
    },
    computed: {
        progress() {
            return (this.currentStep / this.steps.length) * 100;
        }
    },
    mounted() {
        this.find();
    },
    methods: {
        nextStep() {
            if (this.currentStep < this.steps.length) {
                this.currentStep++;
            }
        },
        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        },
        find() {
            this.loading = true;
            axiosTenant.get(`patients/${this.patient_id}/anamnese/find`)
                .then((response) => {
                    const data = response.data.data;

                    // Preenche os campos principais da anamnese
                    this.formData.service_frequency = data.service_frequency || '';
                    this.formData.created_at = data.created_at || '';
                    this.formData.updated_at = data.updated_at || '';
                    this.formData.deleted_at = data.deleted_at || '';

                    // Tabela: treatment
                    if (data.treatment) {
                        this.formData.main_complaint = data.treatment.main_complaint || '';
                        this.formData.secondary_complaint = data.treatment.secondary_complaint || '';
                        this.formData.symptoms = data.treatment.symptoms || '';
                    }

                    // Tabela: history_of_present_illness
                    if (data.history_of_present_illness) {
                        this.formData.beginning_of_the_pathology = data.history_of_present_illness.beginning_of_the_pathology || '';
                        this.formData.frequency = data.history_of_present_illness.frequency || '';
                        this.formData.intensity = data.history_of_present_illness.intensity || '';
                        this.formData.previous_treatments = data.history_of_present_illness.previous_treatments || '';
                        this.formData.medications = data.history_of_present_illness.medications || '';
                    }

                    // Tabela: personal_history
                    if (data.personal_history) {
                        this.formData.childhood = data.personal_history.childhood || '';
                        this.formData.routine = data.personal_history.routine || '';
                        this.formData.addiction = data.personal_history.addiction || '';
                        this.formData.hobbies = data.personal_history.hobbies || '';
                        this.formData.work = data.personal_history.work || '';
                    }

                    // Tabela: family_history
                    if (data.family_history) {
                        this.formData.parents = data.family_history.parents || '';
                        this.formData.siblings = data.family_history.siblings || '';
                        this.formData.partner = data.family_history.partner || '';
                        this.formData.children = data.family_history.children || '';
                        this.formData.home = data.family_history.home || '';
                    }

                    // Tabela: attitude_with_the_interviewer
                    if (data.attitude_with_the_interviewer) {
                        this.formData.cooperative = !!data.attitude_with_the_interviewer.cooperative;
                        this.formData.resistant = !!data.attitude_with_the_interviewer.resistant;
                        this.formData.indifferent = !!data.attitude_with_the_interviewer.indifferent;
                    }

                    // Tabela: guidance
                    if (data.guidance) {
                        this.formData.self_identification = !!data.guidance.self_identification;
                        this.formData.body = !!data.guidance.body;
                        this.formData.temporal = !!data.guidance.temporal;
                        this.formData.spatial = !!data.guidance.spatial;
                        this.formData.pathology_oriented = !!data.guidance.pathology_oriented;
                    }

                    // Tabela: notes
                    if (data.notes) {
                        this.formData.attention = data.notes.attention || '';
                        this.formData.surveillance = data.notes.surveillance || '';
                        this.formData.tenacity = data.notes.tenacity || '';
                        this.formData.memory = data.notes.memory || '';
                        this.formData.intelligence = data.notes.intelligence || '';
                    }

                    // Tabela: contents
                    if (data.contents) {
                        this.formData.obsessions = data.contents.obsessions || '';
                        this.formData.hypochondriasis = data.contents.hypochondriasis || '';
                        this.formData.phobias = data.contents.phobias || '';
                        this.formData.delusions = data.contents.delusions || '';
                    }

                    // Tabela: sense_perception
                    if (data.sense_perception) {
                        this.formData.normal = !!data.sense_perception.normal;
                        this.formData.hallucination = !!data.sense_perception.hallucination;
                    }

                    // Tabela: thought
                    if (data.thought) {
                        this.formData.accelerated = !!data.thought.accelerated;
                        this.formData.slowed = !!data.thought.slowed;
                        this.formData.escape = !!data.thought.escape;
                        this.formData.blockage = !!data.thought.blockage;
                        this.formData.wordy = !!data.thought.wordy;
                        this.formData.repetition = !!data.thought.repetition;
                    }

                    // Tabela: expansion_of_the_self
                    if (data.expansion_of_the_self) {
                        this.formData.greatness = !!data.expansion_of_the_self.greatness;
                        this.formData.jealousy = !!data.expansion_of_the_self.jealousy;
                        this.formData.claim = !!data.expansion_of_the_self.claim;
                        this.formData.genealogical = !!data.expansion_of_the_self.genealogical;
                        this.formData.mystical_of_a_saving_mission = !!data.expansion_of_the_self.mystical_of_a_saving_mission;
                        this.formData.deification = !!data.expansion_of_the_self.deification;
                        this.formData.erotic = !!data.expansion_of_the_self.erotic;
                        this.formData.invention_or_reform = !!data.expansion_of_the_self.invention_or_reform;
                        this.formData.fantastic_ideas = !!data.expansion_of_the_self.fantastic_ideas;
                        this.formData.excessive_health = !!data.expansion_of_the_self.excessive_health;
                        this.formData.physical_capacity = !!data.expansion_of_the_self.physical_capacity;
                        this.formData.beauty = !!data.expansion_of_the_self.beauty;
                        this.formData.others_expansion = data.expansion_of_the_self.others || '';
                    }

                    // Tabela: denial_of_self
                    if (data.denial_of_self) {
                        this.formData.hypochondriac = !!data.denial_of_self.hypochondriac;
                        this.formData.denial_and_bodily_transformation = !!data.denial_of_self.denial_and_bodily_transformation;
                        this.formData.self_accusation = !!data.denial_of_self.self_accusation;
                        this.formData.guilt = !!data.denial_of_self.guilt;
                        this.formData.ruin = !!data.denial_of_self.ruin;
                        this.formData.nihilism = !!data.denial_of_self.nihilism;
                        this.formData.tendency_to_suicide = !!data.denial_of_self.tendency_to_suicide;
                        this.formData.others_denial = data.denial_of_self.others || '';
                    }

                    // Tabela: language
                    if (data.language) {
                        this.formData.dysarthria = !!data.language.dysarthria;
                        this.formData.aphasia = !!data.language.aphasia;
                        this.formData.paraphasia = !!data.language.paraphasia;
                        this.formData.neologism = !!data.language.neologism;
                        this.formData.mussitation = !!data.language.mussitation;
                        this.formData.logorrhea = !!data.language.logorrhea;
                        this.formData.para_responses = !!data.language.para_responses;
                        this.formData.others_language = data.language.others || '';
                    }

                    // Tabela: mood
                    if (data.mood) {
                        this.formData.normal_mood = !!data.mood.normal;
                        this.formData.elated = !!data.mood.elated;
                        this.formData.low_mood = !!data.mood.low_mood;
                        this.formData.sudde_change_mood = !!data.mood.sudde_change_mood;
                    }

                    // Tabela: awareness_of_current_illness
                    if (data.awareness_of_current_illness) {
                        if (data.awareness_of_current_illness.yes) {
                            this.formData.awareness = 'yes';
                        } else if (data.awareness_of_current_illness.partially) {
                            this.formData.awareness = 'partially';
                        } else if (data.awareness_of_current_illness.no) {
                            this.formData.awareness = 'no';
                        }
                    }

                    // Tabela: diagnostic_hypothesis
                    if (data.diagnostic_hypothesis) {
                        this.formData.content = data.diagnostic_hypothesis.content || '';
                    }

                    // Tabela: psychic_exam
                    if (data.psychic_exam) {
                        this.formData.appearance = data.psychic_exam.appearance || '';
                        this.formData.behavior = data.psychic_exam.behavior || '';
                        this.formData.affectivity = data.psychic_exam.affectivity || '';
                    }

                })
                .catch((errors) => {
                    this.alertDanger(errors);
                    window.scrollTo(0, top);
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        fillRelationshipData(source, fields, isCheckbox = false) {
            if (!source) return;

            fields.forEach(field => {
                if (source[field] !== undefined) {
                    if (isCheckbox) {
                        this.formData[field] = !!source[field];
                    } else {
                        this.formData[field] = source[field] || '';
                    }
                }
            });
        },
        submitForm() {
            this.formData.patient_id = this.patient_id;
            axiosTenant.post(`patients/${this.patient_id}/anamnese/store`, this.formData)
                .then((response) => {
                    this.alertSuccess('Operação realizada com sucesso!');
                    window.scrollTo(0, top);
                })
                .catch((errors) => {
                    this.alertDanger(errors);
                    window.scrollTo(0, top);
                })
                .finally(() => {
                    this.loading = false;
                });
        },

    },
}
</script>

<style scoped>
.bg-light-blue {
    background-color: #5d9cec;
}

.bg-soft-white {
    background-color: #f8fafc;
}

.text-teal {
    color: #48cfad;
}

.btn-teal {
    background-color: #48cfad;
    color: white;
}

.btn-teal:hover {
    background-color: #37bc9b;
    color: white;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
}

.step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 15px;
    left: 50%;
    width: 100%;
    height: 2px;
    background-color: #e0e0e0;
    z-index: 0;
}

.step.completed:not(:last-child)::after {
    background-color: #48cfad;
}

.step-circle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #e0e0e0;
    color: #999;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 8px;
    z-index: 1;
    font-weight: bold;
}

.step.active .step-circle {
    background-color: #5d9cec;
    color: white;
}

.step.completed .step-circle {
    background-color: #48cfad;
    color: white;
}

.step-label {
    color: #999;
    font-size: 14px;
    text-align: center;
}

.step.active .step-label {
    color: #5d9cec;
    font-weight: bold;
}

.step.completed .step-label {
    color: #48cfad;
}

.step-content {
    padding: 20px;
    border-radius: 8px;
    background-color: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.form-control,
.form-select {
    border-radius: 6px;
    padding: 10px 15px;
    border: 1px solid #e0e0e0;
    transition: all 0.3s;
}

.form-control:focus,
.form-select:focus {
    border-color: #5d9cec;
    box-shadow: 0 0 0 0.25rem rgba(93, 156, 236, 0.25);
}

.form-check-input:checked {
    background-color: #48cfad;
    border-color: #48cfad;
}

@media (max-width: 768px) {
    .step-label {
        font-size: 12px;
    }

    .step:not(:last-child)::after {
        top: 12px;
    }
}
</style>