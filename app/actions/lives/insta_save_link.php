<?php
// Inclui a conexão com o banco de dados
include "../../config/conexao.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Função para extrair o código do link
    function extractCode($link) {
        // Regex para extrair códigos de diferentes tipos de links do Instagram
        $pattern = '/https:\/\/www\.instagram\.com\/(?:p|reel)\/([A-Za-z0-9_-]+)(?:[\/?]|$)/';
        if (preg_match($pattern, $link, $matches)) {
            return $matches[1];
        }
        return null;
    }

    // Processa os campos de entrada dos três formulários
    for ($i = 1; $i <= 3; $i++) {
        $fieldName = "instagram_link_$i";
        $codinsta = $i; // Define o codinsta com base no número do formulário

        if (isset($_POST[$fieldName]) && !empty($_POST[$fieldName])) {
            $instagramLink = $_POST[$fieldName];
            $code = extractCode($instagramLink);
            if ($code) {
                // Prepara a consulta SQL para atualizar o código do Instagram
                $stmt = $conn->prepare("UPDATE linkinstagram SET linklive = ? WHERE codinsta = ?");
                
                // Verifica se a preparação foi bem-sucedida
                if ($stmt) {
                    // Vincula os parâmetros
                    $stmt->bind_param("si", $code, $codinsta);

                    // Executa a consulta
                    if ($stmt->execute()) {
                        if ($stmt->affected_rows === 0) {
                            // Se nenhuma linha foi atualizada, insere um novo registro
                            $stmt->close();
                            $stmt = $conn->prepare("INSERT INTO linkinstagram (linklive, codinsta) VALUES (?, ?)");
                            if ($stmt) {
                                $stmt->bind_param("si", $code, $codinsta);
                                if ($stmt->execute()) {
                                    echo "Código do Instagram no Formulário $i inserido com sucesso!<br>";
                                } else {
                                    echo "Erro ao inserir o código do Instagram no Formulário $i: " . $stmt->error . "<br>";
                                }
                            } else {
                                echo "Erro na preparação da consulta de inserção: " . $conn->error . "<br>";
                            }
                        } else {
                            echo "Código do Instagram no Formulário $i atualizado com sucesso!<br>";
                        }
                    } else {
                        echo "Erro ao atualizar o código do Instagram no Formulário $i: " . $stmt->error . "<br>";
                    }

                    // Fecha a declaração
                    $stmt->close();
                } else {
                    echo "Erro na preparação da consulta: " . $conn->error . "<br>";
                }
            } else {
                echo "O link do Instagram no Formulário $i não está no formato esperado.<br>";
            }
        } else {
            echo "Por favor, digite um link do Instagram no Formulário $i.<br>";
        }
    }

    // Redireciona após o processamento
    header('Location: ../../pages/Jogos Proximos.php');
    exit(); // Adicionado para garantir que o redirecionamento ocorra
} else {
    echo "Método de requisição inválido.";
}
?>
