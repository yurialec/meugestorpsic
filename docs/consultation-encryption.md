# Criptografia de Consultas

Os campos clinicos de `consultations` sao criptografados em repouso por meio do cast `EncryptedSessionCast`, usando o `APP_KEY` do Laravel.

## Modelo de chaves

- A chave mestra continua em `APP_KEY`.
- Esta versao nao depende de `employee_id`, porque esse campo nao e usado de forma confiavel no fluxo atual.
- A estrutura pode ser trocada futuramente por chave por profissional, AWS KMS ou HashiCorp Vault mantendo o cast.

## Leitura

- O cast descriptografa automaticamente quando o campo e acessado pelo Eloquent.
- Falhas criptograficas retornam `null` e geram log tecnico sem conteudo clinico.

## Busca e indexacao

Campos criptografados nao suportam `where()`, `orderBy()`, `like()` ou indices nativos com conteudo em claro. Alternativas:

- Criar colunas espelho com HMAC/token para igualdade exata.
- Usar Scout/Elastic com indice seguro e politica de acesso compativel com LGPD.
- Manter filtros por metadados nao sensiveis como `patient_id`, `scheduled_at`, `status`, `employee_id`.

## Rotacao e backup

- Apos aplicar a migration de colunas, execute `php artisan consultations:encrypt-existing --dry-run` e depois `php artisan consultations:encrypt-existing` para criptografar legado.
- Antes de rotacionar `APP_KEY`, exporte backup criptografado e teste restauracao.
- Para rotacao online, criar rotina que leia cada consulta com a chave antiga e regrave com a nova chave, em lotes pequenos e com auditoria.
- Backups e dumps permanecem com ciphertext, mas devem ser armazenados tambem em midia criptografada e com controle de acesso.

## Performance

- Listagens devem usar `select()` sem campos clinicos, evitando decrypt em `paginate()`.
- Downloads de prontuario selecionam os campos clinicos e dependem do contexto autenticado do profissional titular.
