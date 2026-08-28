# Sistema de Fidelidade em PHP 🎁

Um sistema completo de gestão de programa de fidelidade e pontos desenvolvido em **PHP 8** e **MySQL**, focado em segurança, auditabilidade de movimentações e facilidade de integração.

---

## 📌 Visão Geral do Projeto

O **Sistema de Fidelidade** permite que estabelecimentos comerciais gerenciem o acúmulo e resgate de pontos de seus clientes. 

Diferente de sistemas legados que apenas alteram um número fixo de saldo, este projeto utiliza uma **arquitetura baseada em livro-razão (ledger)**. Todo acúmulo ou resgate gera um lançamento histórico imutável na tabela de extrato, garantindo auditabilidade, controle de validade dos pontos e proteção contra *race conditions*.

---

## 🚀 Funcionalidades

### 👤 Módulo do Cliente
- [x] Cadastro e autenticação segura (com hash de senha).
- [x] Consulta do saldo total em tempo real.
- [x] Extrato detalhado de movimentações (entradas, saídas e expirações).
- [x] Visualização do catálogo de prêmios disponíveis.
- [x] Solicitação de resgate de prêmios.

### 💼 Módulo do Operador / Caixa
- [x] Lançamento de pontos vinculado ao CPF do cliente no momento da compra.
- [x] Validação e confirmação da entrega de prêmios resgatados.
- [x] Gestão e controle de estoque de prêmios.
- [x] Relatórios e histórico de transações por período.

---

## 🛠️ Tecnologias Utilizadas

- **Backend:** PHP 8.x
- **Driver de Conexão:** PDO (PHP Data Objects) com Prepared Statements
- **Frontend UI:** HTML5, CSS3, Bootstrap 5
- **Controle de Versão:** Git & GitHub

---

## 🗄️ Modelagem de Dados & Cálculo de Saldo

O saldo do cliente é calculado dinamicamente agregando as movimentações registradas no extrato:

```sql
SELECT SUM(pontos) AS saldo_atual 
FROM transacoes_pontos 
WHERE usuario_id = :usuario_id;
