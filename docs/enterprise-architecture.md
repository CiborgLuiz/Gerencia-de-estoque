# Arquitetura Enterprise - Gestão de Estoque (Laravel 12)

## Organização por domínio
- `app/Domains/User`: registro com chave de acesso obrigatória.
- `app/Domains/Product`: cadastro de produtos, upload seguro e regras fiscais.
- `app/Domains/Sales`: catálogo e fechamento de venda com transação + lock pessimista.
- `app/Domains/Invoice`: emissão/cancelamento NF-e e trilha de auditoria.

## Fluxo de venda até NF-e
1. Usuário autenticado cria venda (`SaleService`).
2. Cada item executa `lockForUpdate` no produto e valida estoque.
3. Estoque é baixado sem permitir saldo negativo.
4. Venda é persistida e `InvoiceService` é disparado.
5. Serviço NF-e gera XML, assina e envia ao `SefazClient`.
6. XML/protocolo/status são gravados em `invoices` e em `storage/app/nfe`.
7. Logs completos de request/response vão para `invoice_logs`.

## Segurança
- Rate limit no login (`LoginRequest`).
- Password hash nativo do Laravel (`hashed`).
- FormRequest para cadastro de usuário, produto e venda.
- Upload restrito por MIME e tamanho.
- Storage local privado para XML.
- Policies para Product e Sale.
- Middleware de papel (`role`) e admin (`is.admin`).

## Homologação SEFAZ (checklist)
- [ ] Certificado A1 válido e senha armazenada em `.env` criptografada.
- [ ] Ambiente de homologação ativo.
- [ ] CSC/ID token do estado configurado.
- [ ] Emissão de NF-e 4.00 com schema validado.
- [ ] Assinatura digital XML validada.
- [ ] Testes de rejeição e contingência executados.
- [ ] DANFE PDF gerado e arquivado.

## Produção (checklist)
- [ ] Filas e workers para emissão assíncrona.
- [ ] Observabilidade (logs estruturados + alertas).
- [ ] Política de retenção XML por 5 anos.
- [ ] Backup diário de banco e storage privado.
- [ ] Rotação de chaves/segredos e revisão de acessos.
- [ ] Monitoramento de indisponibilidade da SEFAZ.
- [ ] Plano de rollback e runbook operacional.
