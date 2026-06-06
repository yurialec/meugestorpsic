<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Relatório do Paciente - {{ $patient->full_name }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #2c3e50;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
            background-color: #ffffff;
        }

        h2, h3 {
            text-align: center;
            color: #2c3e50;
        }

        h2 {
            margin-top: 30px;
            margin-bottom: 20px;
        }

        h3 {
            margin-top: 30px;
            margin-bottom: 15px;
        }

        .header-table,
        .section-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
            border-radius: 6px;
            overflow: hidden;
        }

        .header-table th,
        .header-table td,
        .section-table th,
        .section-table td {
            padding: 12px;
            vertical-align: top;
        }

        .header-table {
            background-color: #e6f0fa;
        }

        .header-table td:first-child {
            text-align: center;
        }

        .section-table th {
            background-color: #e6f0fa;
            text-align: left;
            font-weight: bold;
            color: #2c3e50;
        }

        .section-table td {
            border-top: 1px solid #e0e6ed;
        }

        .section-table tr:first-child td {
            border-top: none;
        }

        .field-label {
            width: 25%;
            font-weight: bold;
        }

        .checkbox-group {
            display: inline-block;
            margin-right: 15px;
            margin-bottom: 6px;
        }

        .checkbox-group input,
        .option-cell input {
            margin-right: 4px;
        }

        .option-cell {
            width: 33.33%;
        }

        p.empty {
            color: #7f8c8d;
            font-style: italic;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 0.9em;
            color: #7f8c8d;
            background-color: #e6f0fa;
            padding: 12px;
            border-radius: 6px;
        }

        img {
            max-width: 120px;
            height: auto;
        }
    </style>
</head>

<body>
    @php
        $blank = '_________________________________________________________________________________________';
    @endphp

    <!-- CABEÇALHO -->
    <table class="header-table">
        <tr>
            <td style="width: 20%;">
                @if($psychologistLogo)
                    <img src="{{ $psychologistLogo }}" alt="Logo">
                @endif
            </td>
            <td style="width: 80%;">
                <strong>{{ $client['name'] }}</strong><br>
                CPF: {{ $client['cpf'] }}<br>
                Telefone: {{ $client['phone'] }}<br>
                CRP: {{ $client['crp'] }}
            </td>
        </tr>
    </table>

    <h2>RELATÓRIO DO PACIENTE</h2>

    <!-- IDENTIFICAÇÃO -->
    <table class="section-table">
        <tr>
            <th colspan="2">1 - IDENTIFICAÇÃO</th>
        </tr>
        <tr>
            <td style="width: 50%;">
                <strong>Nome:</strong> {{ $patient->full_name }}<br>
                <strong>Idade:</strong> {{ $patient->age }}
            </td>
            <td style="width: 50%;">
                <strong>Sexo:</strong> {{ $patient->gender }}<br>
                <strong>Telefones para contato:</strong> {{ $patient->phone ?? '________________' }}
            </td>
        </tr>
    </table>

    <!-- ATENDIMENTO -->
    <table class="section-table">
        <tr>
            <th colspan="2">2 - ATENDIMENTO</th>
        </tr>
        <tr>
            <td style="width: 50%;">
                <strong>Frequência:</strong> {{ data_get($patient, 'anamnese.service_frequency', '________________') }}
            </td>
            <td style="width: 50%;">
                <strong>Data/hora:</strong> {{ now()->format('d/m/Y H:i') }}
            </td>
        </tr>
        <tr>
            <td class="field-label">Queixa Principal</td>
            <td>{{ data_get($patient, 'anamnese.treatment.main_complaint', $blank) }}</td>
        </tr>
        <tr>
            <td class="field-label">Secundária</td>
            <td>{{ data_get($patient, 'anamnese.treatment.secondary_complaint', $blank) }}</td>
        </tr>
        <tr>
            <td class="field-label">Sintomas</td>
            <td>{{ data_get($patient, 'anamnese.treatment.symptoms', $blank) }}</td>
        </tr>
    </table>

    <!-- HISTÓRICO DA DOENÇA ATUAL -->
    <table class="section-table">
        <tr>
            <th colspan="2">3 - HISTÓRICO DA DOENÇA ATUAL</th>
        </tr>
        <tr>
            <td class="field-label">Início da patologia</td>
            <td>{{ data_get($patient, 'anamnese.treatment.beginning_of_the_pathology', $blank) }}</td>
        </tr>
        <tr>
            <td class="field-label">Frequência</td>
            <td>{{ data_get($patient, 'anamnese.treatment.frequency', $blank) }}</td>
        </tr>
        <tr>
            <td class="field-label">Intensidade</td>
            <td>{{ data_get($patient, 'anamnese.treatment.intensity', $blank) }}</td>
        </tr>
        <tr>
            <td class="field-label">Tratamentos anteriores</td>
            <td>{{ data_get($patient, 'anamnese.treatment.previous_treatments', $blank) }}</td>
        </tr>
        <tr>
            <td class="field-label">Medicamentos</td>
            <td>{{ data_get($patient, 'anamnese.treatment.medications', $blank) }}</td>
        </tr>
    </table>

    <!-- HISTÓRICO PESSOAL -->
    <table class="section-table">
        <tr>
            <th colspan="2">4 - HISTÓRICO PESSOAL</th>
        </tr>
        <tr>
            <td class="field-label">Infância</td>
            <td>{{ data_get($patient, 'anamnese.personalHistory.childhood', $blank) }}</td>
        </tr>
        <tr>
            <td class="field-label">Rotina</td>
            <td>{{ data_get($patient, 'anamnese.personalHistory.routine', $blank) }}</td>
        </tr>
        <tr>
            <td class="field-label">Vícios</td>
            <td>{{ data_get($patient, 'anamnese.personalHistory.addiction', $blank) }}</td>
        </tr>
        <tr>
            <td class="field-label">Hobbies</td>
            <td>{{ data_get($patient, 'anamnese.personalHistory.hobbies', $blank) }}</td>
        </tr>
        <tr>
            <td class="field-label">Trabalho</td>
            <td>{{ data_get($patient, 'anamnese.personalHistory.work', $blank) }}</td>
        </tr>
    </table>

    <!-- HISTÓRICO FAMILIAR -->
    <table class="section-table">
        <tr>
            <th colspan="2">5 - HISTÓRICO FAMILIAR</th>
        </tr>
        <tr>
            <td class="field-label">Pais</td>
            <td>{{ data_get($patient, 'anamnese.familyHistory.parents', $blank) }}</td>
        </tr>
        <tr>
            <td class="field-label">Irmãos</td>
            <td>{{ data_get($patient, 'anamnese.familyHistory.siblings', $blank) }}</td>
        </tr>
        <tr>
            <td class="field-label">Cônjuge</td>
            <td>{{ data_get($patient, 'anamnese.familyHistory.partner', $blank) }}</td>
        </tr>
        <tr>
            <td class="field-label">Filhos</td>
            <td>{{ data_get($patient, 'anamnese.familyHistory.children', $blank) }}</td>
        </tr>
        <tr>
            <td class="field-label">Lar</td>
            <td>{{ data_get($patient, 'anamnese.familyHistory.home', $blank) }}</td>
        </tr>
    </table>

    <div style="page-break-after: always;"></div>

    <!-- EXAME PSÍQUICO -->
    <h3>6 - EXAME PSÍQUICO</h3>

    <table class="section-table">
        <tr>
            <th colspan="2">APARÊNCIA E ORIENTAÇÃO</th>
        </tr>
        <tr>
            <td class="field-label">Aparência</td>
            <td>{{ data_get($patient, 'anamnese.sensePerception.appearance', $blank) }}</td>
        </tr>
        <tr>
            <td class="field-label">Comportamento</td>
            <td>{{ data_get($patient, 'anamnese.sensePerception.behavior', $blank) }}</td>
        </tr>
        <tr>
            <td class="field-label">Atitude para com o entrevistador</td>
            <td>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.attitudeWithTheInterviewer.cooperative') ? 'checked' : '' }}> Cooperativo</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.attitudeWithTheInterviewer.resistant') ? 'checked' : '' }}> Resistente</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.attitudeWithTheInterviewer.indifferent') ? 'checked' : '' }}> Indiferente</span>
            </td>
        </tr>
        <tr>
            <td class="field-label">Orientação</td>
            <td>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.historyOfPresentIllness.self_identification') ? 'checked' : '' }}> Auto Identificatória</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.historyOfPresentIllness.body') ? 'checked' : '' }}> Corporal</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.historyOfPresentIllness.temporal') ? 'checked' : '' }}> Temporal</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.historyOfPresentIllness.spatial') ? 'checked' : '' }}> Espacial</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.historyOfPresentIllness.pathology_oriented') ? 'checked' : '' }}> Orientado em relação a patologia</span>
            </td>
        </tr>
        <tr>
            <td class="field-label">Observações</td>
            <td>{{ data_get($patient, 'anamnese.notes.content', $blank) }}</td>
        </tr>
    </table>

    <table class="section-table">
        <tr>
            <th colspan="2">FUNÇÕES PSÍQUICAS</th>
        </tr>
        <tr>
            <td style="width: 50%;">
                <strong>Atenção:</strong> {{ data_get($patient, 'anamnese.psychicExam.attention') ? 'Sim' : 'Não' }}<br>
                <strong>Vigilância:</strong> {{ data_get($patient, 'anamnese.psychicExam.surveillance') ? 'Sim' : 'Não' }}
            </td>
            <td style="width: 50%;">
                <strong>Tenacidade:</strong> {{ data_get($patient, 'anamnese.psychicExam.tenacity') ? 'Sim' : 'Não' }}<br>
                <strong>Memória:</strong> {{ data_get($patient, 'anamnese.psychicExam.memory') ? 'Sim' : 'Não' }}
            </td>
        </tr>
        <tr>
            <td class="field-label">Inteligência</td>
            <td>{{ data_get($patient, 'anamnese.psychicExam.intelligence') ? 'Sim' : 'Não' }}</td>
        </tr>
        <tr>
            <td class="field-label">Senso percepção</td>
            <td>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.thought.normal') ? 'checked' : '' }}> Normal</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.thought.hallucination') ? 'checked' : '' }}> Alucinação</span>
            </td>
        </tr>
        <tr>
            <td class="field-label">Pensamento</td>
            <td>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.thought.accelerated') ? 'checked' : '' }}> Acelerado</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.thought.slowed') ? 'checked' : '' }}> Retardado</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.thought.escape') ? 'checked' : '' }}> Fuga</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.thought.blockage') ? 'checked' : '' }}> Bloqueio</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.thought.wordy') ? 'checked' : '' }}> Prolixo</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.thought.repetition') ? 'checked' : '' }}> Repetição</span>
            </td>
        </tr>
        <tr>
            <td class="field-label">Conteúdo</td>
            <td>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.contents.obsessions') ? 'checked' : '' }}> Obsessões</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.contents.hypochondriasis') ? 'checked' : '' }}> Hipocondrias</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.contents.phobias') ? 'checked' : '' }}> Fobias</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.contents.delusions') ? 'checked' : '' }}> Delírios</span>
            </td>
        </tr>
    </table>

    <table class="section-table">
        <tr>
            <th colspan="3">EXPANSÃO DO EU</th>
        </tr>
        <tr>
            <td class="option-cell"><input type="checkbox" {{ data_get($patient, 'anamnese.expansionOfTheSelf.greatness') ? 'checked' : '' }}> Grandeza</td>
            <td class="option-cell"><input type="checkbox" {{ data_get($patient, 'anamnese.expansionOfTheSelf.jealousy') ? 'checked' : '' }}> Ciúme</td>
            <td class="option-cell"><input type="checkbox" {{ data_get($patient, 'anamnese.expansionOfTheSelf.claim') ? 'checked' : '' }}> Reivindicação</td>
        </tr>
        <tr>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.expansionOfTheSelf.genealogical') ? 'checked' : '' }}> Genealógico</td>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.expansionOfTheSelf.mystical_of_a_saving_mission') ? 'checked' : '' }}> Místico, de missão salvadora</td>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.expansionOfTheSelf.deification') ? 'checked' : '' }}> Deficação</td>
        </tr>
        <tr>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.expansionOfTheSelf.erotic') ? 'checked' : '' }}> Erótico</td>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.expansionOfTheSelf.invention_or_reform') ? 'checked' : '' }}> Invenção ou reforma</td>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.expansionOfTheSelf.fantastic_ideas') ? 'checked' : '' }}> Ideias fantásticas</td>
        </tr>
        <tr>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.expansionOfTheSelf.excessive_health') ? 'checked' : '' }}> Excessiva saúde</td>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.expansionOfTheSelf.physical_capacity') ? 'checked' : '' }}> Capacidade física</td>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.expansionOfTheSelf.beauty') ? 'checked' : '' }}> Beleza</td>
        </tr>
        <tr>
            <td colspan="3"><strong>Outros:</strong> {{ data_get($patient, 'anamnese.expansionOfTheSelf.others', '_________________________') }}</td>
        </tr>
    </table>

    <table class="section-table">
        <tr>
            <th colspan="3">RETRAÇÃO DO EU</th>
        </tr>
        <tr>
            <td class="option-cell"><input type="checkbox" {{ data_get($patient, 'anamnese.denialOfSelf.hypochondriac') ? 'checked' : '' }}> Prejuízo</td>
            <td class="option-cell"><input type="checkbox" {{ data_get($patient, 'anamnese.denialOfSelf.denial_and_bodily_transformation') ? 'checked' : '' }}> Auto-referência</td>
            <td class="option-cell"><input type="checkbox" {{ data_get($patient, 'anamnese.denialOfSelf.self_accusation') ? 'checked' : '' }}> Perseguição</td>
        </tr>
        <tr>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.denialOfSelf.guilt') ? 'checked' : '' }}> Influência</td>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.denialOfSelf.ruin') ? 'checked' : '' }}> Possessão</td>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.denialOfSelf.nihilism') ? 'checked' : '' }}> Humildes</td>
        </tr>
        <tr>
            <td colspan="3"><strong>Outros:</strong> {{ data_get($patient, 'anamnese.denialOfSelf.others', '_________________________') }}</td>
        </tr>
    </table>

    <table class="section-table">
        <tr>
            <th colspan="3">NEGAÇÃO DO EU</th>
        </tr>
        <tr>
            <td class="option-cell"><input type="checkbox" {{ data_get($patient, 'anamnese.denialOfSelf.hypochondriac') ? 'checked' : '' }}> Hipocondríaco</td>
            <td class="option-cell"><input type="checkbox" {{ data_get($patient, 'anamnese.denialOfSelf.denial_and_bodily_transformation') ? 'checked' : '' }}> Negação e transformação corporal</td>
            <td class="option-cell"><input type="checkbox" {{ data_get($patient, 'anamnese.denialOfSelf.self_accusation') ? 'checked' : '' }}> Auto acusação</td>
        </tr>
        <tr>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.denialOfSelf.guilt') ? 'checked' : '' }}> Culpa</td>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.denialOfSelf.ruin') ? 'checked' : '' }}> Ruína</td>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.denialOfSelf.nihilism') ? 'checked' : '' }}> Niilismo</td>
        </tr>
        <tr>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.denialOfSelf.tendency_to_suicide') ? 'checked' : '' }}> Tendência ao suicídio</td>
            <td colspan="2"><strong>Outros:</strong> {{ data_get($patient, 'anamnese.denialOfSelf.others', '_________________________') }}</td>
        </tr>
    </table>

    <table class="section-table">
        <tr>
            <th colspan="2">LINGUAGEM, AFETIVIDADE E HUMOR</th>
        </tr>
        <tr>
            <td style="width: 50%;"><input type="checkbox" {{ data_get($patient, 'anamnese.language.dysarthria') ? 'checked' : '' }}> Disartrias (má articulação)</td>
            <td style="width: 50%;"><input type="checkbox" {{ data_get($patient, 'anamnese.language.aphasia') ? 'checked' : '' }}> Afasias, verbigeração (repetição de palavras)</td>
        </tr>
        <tr>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.language.paraphasia') ? 'checked' : '' }}> Parafasia</td>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.language.neologism') ? 'checked' : '' }}> Neologismo</td>
        </tr>
        <tr>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.language.mussitation') ? 'checked' : '' }}> Mussitação (voz murmurada em tom baixo)</td>
            <td><input type="checkbox" {{ data_get($patient, 'anamnese.language.logorrhea') ? 'checked' : '' }}> Logorréia (fluxo incessante e incoercível de palavras)</td>
        </tr>
        <tr>
            <td colspan="2"><input type="checkbox" {{ data_get($patient, 'anamnese.language.para_responses') ? 'checked' : '' }}> Para-respostas (responde a uma indagação com algo que não tem nada a ver com o que foi perguntado)</td>
        </tr>
        <tr>
            <td class="field-label">Afetividade</td>
            <td>{{ data_get($patient, 'anamnese.sensePerception.affectivity', $blank) }}</td>
        </tr>
        <tr>
            <td class="field-label">Humor</td>
            <td>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.mood.normal') ? 'checked' : '' }}> Normal</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.mood.elated') ? 'checked' : '' }}> Exaltado</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.mood.low_mood') ? 'checked' : '' }}> Baixa de humor</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.mood.sudde_change_mood') ? 'checked' : '' }}> Quebra súbita da tonalidade do humor durante a entrevista</span>
            </td>
        </tr>
        <tr>
            <td class="field-label">Consciência da doença atual</td>
            <td>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.awarenessOfCurrentIllness.yes') ? 'checked' : '' }}> Sim</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.awarenessOfCurrentIllness.partially') ? 'checked' : '' }}> Parcialmente</span>
                <span class="checkbox-group"><input type="checkbox" {{ data_get($patient, 'anamnese.awarenessOfCurrentIllness.no') ? 'checked' : '' }}> Não</span>
            </td>
        </tr>
    </table>

    <!-- HIPÓTESE DIAGNÓSTICA -->
    <table class="section-table">
        <tr>
            <th>7 - HIPÓTESE DIAGNÓSTICA</th>
        </tr>
        <tr>
            <td>{{ data_get($patient, 'anamnese.diagnosticHypothesis.content', $blank) }}</td>
        </tr>
    </table>

    <!-- RODAPÉ -->
    <div class="footer">
        Documento confidencial — Sigilo profissional<br>
        Gerado em {{ now()->format('d/m/Y H:i') }}
    </div>

</body>

</html>
