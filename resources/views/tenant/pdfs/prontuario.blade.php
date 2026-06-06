<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Prontuário - {{ $patient->full_name }}</title>
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

    <h2>PRONTUÁRIO PSICOLÓGICO</h2>

    <!-- DADOS DO PACIENTE -->
    <table class="section-table">
        <tr>
            <th colspan="2">DADOS DO PACIENTE</th>
        </tr>
        <tr>
            <td style="width: 50%;">
                <strong>Nome:</strong> {{ $patient->full_name }}<br>
                <strong>CPF:</strong> {{ $patient->cpf }}<br>
                <strong>E-mail:</strong> {{ $patient->email }}
            </td>
            <td style="width: 50%;">
                <strong>Data de Nascimento:</strong>
                {{ \Carbon\Carbon::parse($patient->date_of_birth)->format('d/m/Y') }}<br>
                <strong>Gênero:</strong> {{ $patient->gender === 'M' ? 'Masculino' : 'Feminino' }}<br>
                <strong>Telefone:</strong> {{ $patient->phone }}
            </td>
        </tr>
    </table>

    <!-- ANAMNESE -->
    @if($patient->anamnese)
        <table class="section-table">
            <tr>
                <th colspan="2">ANAMNESE</th>
            </tr>
            <tr>
                <td colspan="2">
                    <strong>Frequência do Serviço:</strong> {{ $patient->anamnese->service_frequency }}
                </td>
            </tr>
        </table>

        @if($patient->anamnese->historyOfPresentIllness)
            <table class="section-table">
                <tr>
                    <th colspan="2">HISTÓRIA DA DOENÇA ATUAL</th>
                </tr>
                <tr>
                    <td colspan="2">
                        <strong>Início:</strong> {{ $patient->anamnese->historyOfPresentIllness->beginning_of_the_pathology }}<br>
                        <strong>Frequência:</strong> {{ $patient->anamnese->historyOfPresentIllness->frequency }}<br>
                        <strong>Intensidade:</strong> {{ $patient->anamnese->historyOfPresentIllness->intensity }}<br>
                        <strong>Tratamentos Anteriores:</strong> {{ $patient->anamnese->historyOfPresentIllness->previous_treatments }}<br>
                        <strong>Medicações:</strong> {{ $patient->anamnese->historyOfPresentIllness->medications }}
                    </td>
                </tr>
            </table>
        @endif
    @endif

    <div style="page-break-after: always;"></div>

    <!-- SESSÕES -->
    <h3>REGISTRO DAS SESSÕES</h3>

    @forelse($patient->consultations as $index => $consultation)
        <table class="section-table">
            <tr>
                <th colspan="2">Sessão {{ $index + 1 }} —
                    {{ \Carbon\Carbon::parse($consultation->scheduled_at)->format('d/m/Y') }}</th>
            </tr>
            <tr>
                <td style="width: 25%;"><strong>Status</strong></td>
                <td>{{ ucfirst($consultation->status) }}</td>
            </tr>
            <tr>
                <td><strong>Local</strong></td>
                <td>{{ $consultation->location }}</td>
            </tr>
            <tr>
                <td><strong>Objetivos</strong></td>
                <td>{{ $consultation->objectives }}</td>
            </tr>
            <tr>
                <td><strong>Conteúdo Trabalhado</strong></td>
                <td>{{ $consultation->content_worked }}</td>
            </tr>
            <tr>
                <td><strong>Observações Clínicas</strong></td>
                <td>{{ $consultation->clinical_observations }}</td>
            </tr>
            <tr>
                <td><strong>Intervenções</strong></td>
                <td>{{ $consultation->interventions }}</td>
            </tr>
            <tr>
                <td><strong>Planejamento</strong></td>
                <td>{{ $consultation->planning }}</td>
            </tr>
            <tr>
                <td><strong>Estado Emocional</strong></td>
                <td>{{ $consultation->emotional_state }}</td>
            </tr>
            <tr>
                <td><strong>Nível de Engajamento</strong></td>
                <td>{{ $consultation->engagement_level }}</td>
            </tr>
            <tr>
                <td><strong>Insights</strong></td>
                <td>{{ $consultation->insights }}</td>
            </tr>
        </table>
    @empty
        <p class="empty">Nenhuma sessão registrada.</p>
    @endforelse

    <div style="page-break-after: always;"></div>

    <!-- HIPÓTESE DIAGNÓSTICA -->
    @if(optional($patient->anamnese)->diagnosticHypothesis)
        <table class="section-table">
            <tr>
                <th>HIPÓTESE DIAGNÓSTICA</th>
            </tr>
            <tr>
                <td>{{ $patient->anamnese->diagnosticHypothesis->content }}</td>
            </tr>
        </table>
    @endif

    <!-- RODAPÉ -->
    <div class="footer">
        Documento confidencial — Sigilo profissional<br>
        Gerado em {{ now()->format('d/m/Y H:i') }}
    </div>

</body>

</html>