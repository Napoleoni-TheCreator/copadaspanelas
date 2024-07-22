<?php
// save_link.php
include '../../config/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['linklive']) && !empty($_POST['linklive'])) {
        $linklive = $_POST['linklive'];

        // Excluir o registro existente
        $sql_delete = "DELETE FROM linklive";
        if ($conn->query($sql_delete) === TRUE) {
            // Inserir o novo valor
            $sql_insert = "INSERT INTO linklive (codlive) VALUES (?)";
            if ($stmt_insert = $conn->prepare($sql_insert)) {
                $stmt_insert->bind_param('s', $linklive);

                if ($stmt_insert->execute()) {
                    echo "Link atualizado com sucesso!<br>";
                } else {
                    echo "Erro ao inserir o link: " . $stmt_insert->error . "<br>";
                }

                $stmt_insert->close();
            } else {
                echo "Erro ao preparar a declaração INSERT: " . $conn->error . "<br>";
            }
        } else {
            echo "Erro ao excluir o registro: " . $conn->error . "<br>";
        }
    } else {
        echo "O valor de 'linklive' não foi enviado ou está vazio.<br>";
    }
} else {
    echo "Método de requisição não é POST.<br>";
}
 
$conn->close();
?>
