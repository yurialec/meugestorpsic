# Auditoria LGPD/CFP

Este modulo registra eventos sensiveis em banco relacional (`audit_logs`) e evita uso de arquivos de log para evidencias regulatorias.

Notas de conformidade:

- CPF do operador e do paciente e armazenado com cast `encrypted`; para busca, use apenas os hashes HMAC (`*_cpf_hash`) com sal configuravel em `AUDIT_LOG_CPF_HASH_SALT`.
- A API nao retorna CPF integral. O resource mascara CPF somente quando `include_cpf=true`.
- Registros sao imutaveis no model: updates e deletes disparam excecao. Exclusao por retencao deve ser feita por rotina administrativa controlada e documentada, preferencialmente com exportacao/backup antes do descarte.
- `occurred_at` e gravado em `America/Sao_Paulo` para preservar a linha temporal exigida em prontuarios psicologicos.
- Metadados devem registrar finalidade/motivo do acesso quando aplicavel, sem duplicar conteudo clinico sensivel.

Recomendacoes:

- Ativar backups criptografados do banco e testar restauracao periodicamente.
- Manter a chave `APP_KEY` e `AUDIT_LOG_CPF_HASH_SALT` em cofre de segredos, com rotacao planejada.
- Definir `AUDIT_LOG_RETENTION_DAYS` conforme politica juridica/clinica do SaaS e documentar descarte.
- Restringir as rotas de consulta/exportacao de auditoria a perfis administrativos autorizados.
- Monitorar falhas recorrentes de login e downloads incomuns como sinais de acesso indevido.
