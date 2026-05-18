# Atualização 14/05/2026

1. Adicionado o campo `interagiu_whatsapp` (BOOLEAN) na tabela `devedores` para registrar se o cliente interagiu pelo WhatsApp.
   - Para marcar um devedor como tendo interagido, utilize:
     UPDATE devedores SET interagiu_whatsapp = TRUE WHERE id = ...;
   - Essa alteração exige atualização do banco de dados (rodar o novo schema.sql).

2. Implementada função de importação de devedores (importa devedores via arquivo ou integração).
   - Consulte a documentação ou a tela de importação para detalhes de uso.
