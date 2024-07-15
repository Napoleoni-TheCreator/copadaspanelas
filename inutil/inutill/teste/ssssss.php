<!DOCTYPE html>
<html>
<head>
    <title>Quartas de Finais</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f8ff;
            font-family: Arial, sans-serif;
        }
        #confrontos-wrapper {
            background-color: #f0f8ff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 1200px;
            overflow-x: auto;
        }
        h1 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        .confronto {
            margin-bottom: 20px;
        }
        .confronto-header {
            background-color: #f4f4f4;
            padding: 10px;
            font-weight: bold;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .matchup {
            font-size: 18px;
            text-align: center;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .team-info {
            display: flex;
            align-items: center;
        }
        .team-info img {
            width: 30px;
            height: 30px;
            margin-right: 10px;
        }
        .team-name {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div id="confrontos-wrapper">
        <h1>CONFRONTOS - QUARTAS DE FINAIS</h1>
        <?php include 'quartas_de_finais.php'; ?>
    </div>
</body>
</html>
