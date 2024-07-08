// Importe o módulo 'mysql' (instale-o com 'npm install mysql')
const mysql = require('mysql');

// Configurações de conexão
const connection = mysql.createConnection({
    host: 'localhost',
    user: 'seu_usuario',
    password: ' ',
    database: 'cadas.time'
});

// Conecta ao banco de dados
connection.connect((err) => {
    if (err) {
        console.error('Erro ao conectar ao banco de dados:', err);
        return;
    }
    console.log('Conexão estabelecida com sucesso!');
});

// Função para inserir um novo time
function inserirTime(nomeTime, nomeTecnico, logoTime) {
    const query = 'INSERT INTO times (nome_time, nome_tecnico, logo_time) VALUES (?, ?, ?)';
    connection.query(query, [nomeTime, nomeTecnico, logoTime], (err, result) => {
        if (err) {
            console.error('Erro ao inserir time:', err);
            return;
        }
        console.log('Time inserido com sucesso! ID:', result.insertId);
    });
}

// Exemplo de uso
inserirTime('Meu Time FC', 'Técnico A', '/caminho/para/logo.png');
