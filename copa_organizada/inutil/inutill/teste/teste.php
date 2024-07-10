<!DOCTYPE html>
<html>
<head>
  <title>Diagram</title>
  <style>
    body {
      background-color: #f5f5f5;
      font-family: sans-serif;
      margin: 0;
      padding: 0;
    }

    .container {
      display: flex;
      justify-content: space-around;
      align-items: center;
      height: 100vh;
    }

    .diagram {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .box {
      width: 40px;
      height: 20px;
      border: 1px solid black;
      margin: 0px;
      border-radius: 5px;
    }
    .box1 {
      width: 40px;
      height: 20px;
      border: 1px solid black;
      margin: 0px;
      margin-top: 80px;
      border-radius: 5px;
    }
    .line {
      width: 100px;
      height: 1px;
      background-color: black;
      margin: 0px 0;
    }

    .vertical-line {
      width: 2px;
      height: 40px;
      background-color: black;
      margin: 0px 0;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- GRUPO A -->
    <!-- primeiro  -->
    <div class="diagram">
        <!-- 1 JOGO -->
      <div class="box">TIME-A</div>
      <div class="vertical-line"></div>
      <div class="vertical-line"></div>
      <div class="vertical-line"></div>
      <div class="box">TIME-B</div>
        <!-- 2 JOGO -->
      <div class="box1">TIME-A</div>
      <div class="vertical-line"></div>
      <div class="vertical-line"></div>
      <div class="vertical-line"></div>
      <div class="box">TIME-B</div>
    </div>
    <!-- GRUPO B -->
    <!-- segundo -->
    <div class="diagram">
     <!-- 1 JOGO -->
     <div class="box">timeA</div>
      <div class="vertical-line"></div>
      <div class="vertical-line"></div>
      <div class="vertical-line"></div>
      <div class="box">timeB</div>
    </div>

    <!-- terceiro -->
    <div class="diagram">
     <!-- 1 JOGO final-->
     <div class="box">final</div>
      <div class="vertical-line"></div>
      <div class="box">final</div>
    </div>
    <!-- GRUPO C -->
    <!-- quarto -->
    <div class="diagram">
     <!-- 1 JOGO final-->
     <div class="box">timeA</div>
      <div class="vertical-line"></div>
      <div class="vertical-line"></div>
      <div class="vertical-line"></div>
      <div class="box">TimeB</div>
    </div>
    <!--GRUPO D  -->
    <!-- quinto -->
    <div class="diagram">
     <!-- 1 JOGO -->
     <div class="box">timaA</div>
      <div class="vertical-line"></div>
      <div class="vertical-line"></div>
      <div class="vertical-line"></div>
      <div class="box">timaB</div>
    <!-- 2 JOGO -->
      <div class="box1">timaA</div>
      <div class="vertical-line"></div>
      <div class="vertical-line"></div>
      <div class="vertical-line"></div>
      <div class="box">TimeB</div>

    </div>
  </div>
</body>
</html>