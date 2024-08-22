SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `admin` (
  cod_adm varchar(200) NOT NULL,
  nome varchar(60) NOT NULL,
  email varchar(100) NOT NULL,
  senha varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE configuracoes (
  id int(11) NOT NULL,
  equipes_por_grupo int(11) NOT NULL,
  numero_grupos int(11) NOT NULL,
  fase_final enum('oitavas','quartas','semifinais','final') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE fase_execucao (
  id int(11) NOT NULL,
  fase varchar(50) NOT NULL,
  executado tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE final (
  id int(11) NOT NULL,
  time_id int(11) NOT NULL,
  grupo_nome varchar(50) DEFAULT NULL,
  time_nome varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE final_confrontos (
  id int(11) NOT NULL,
  timeA_id int(11) NOT NULL,
  timeB_id int(11) NOT NULL,
  fase enum('final') NOT NULL,
  gols_marcados_timeA int(11) DEFAULT NULL,
  gols_marcados_timeB int(11) DEFAULT NULL,
  gols_contra_timeA int(11) DEFAULT NULL,
  gols_contra_timeB int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE grupos (
  id int(11) NOT NULL,
  nome varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE jogadores (
  id int(11) NOT NULL,
  nome varchar(255) NOT NULL,
  gols int(11) DEFAULT 0,
  posicao varchar(255) DEFAULT NULL,
  numero int(11) DEFAULT NULL,
  assistencias int(11) DEFAULT 0,
  cartoes_amarelos int(11) DEFAULT 0,
  cartoes_vermelhos int(11) DEFAULT 0,
  token varchar(64) DEFAULT NULL,
  imagem longblob DEFAULT NULL,
  time_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE jogos (
  id int(11) NOT NULL,
  time_id int(11) NOT NULL,
  resultado char(1) DEFAULT NULL,
  data_jogo date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE jogos_fase_grupos (
  id int(11) NOT NULL,
  grupo_id int(11) NOT NULL,
  timeA_id int(11) NOT NULL,
  timeB_id int(11) NOT NULL,
  nome_timeA varchar(100) NOT NULL,
  nome_timeB varchar(100) NOT NULL,
  gols_marcados_timeA int(11) DEFAULT 0,
  gols_marcados_timeB int(11) DEFAULT 0,
  resultado_timeA char(1) DEFAULT NULL,
  resultado_timeB char(1) DEFAULT NULL,
  data_jogo datetime NOT NULL,
  rodada int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE jogos_finais (
  id int(11) NOT NULL,
  timeA_id int(11) NOT NULL,
  timeB_id int(11) NOT NULL,
  nome_timeA varchar(100) NOT NULL,
  nome_timeB varchar(100) NOT NULL,
  gols_marcados_timeA int(11) NOT NULL,
  gols_marcados_timeB int(11) NOT NULL,
  resultado_timeA char(1) DEFAULT NULL,
  resultado_timeB char(1) DEFAULT NULL,
  data_jogo datetime NOT NULL,
  fase varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE linkinstagram (
  codinsta int(11) NOT NULL,
  linklive varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE linklive (
  codlive varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE noticias (
  id int(11) NOT NULL,
  titulo varchar(255) NOT NULL,
  descricao text NOT NULL,
  imagem longblob NOT NULL,
  link varchar(255) NOT NULL,
  data_adicao timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE oitavas_de_final (
  id int(11) NOT NULL,
  time_id int(11) NOT NULL,
  grupo_nome varchar(50) DEFAULT NULL,
  time_nome varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE oitavas_de_final_confrontos (
  id int(11) NOT NULL,
  timeA_id int(11) NOT NULL,
  timeB_id int(11) NOT NULL,
  fase enum('oitavas') NOT NULL,
  gols_marcados_timeA int(11) DEFAULT NULL,
  gols_marcados_timeB int(11) DEFAULT NULL,
  gols_contra_timeA int(11) DEFAULT NULL,
  gols_contra_timeB int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE posicoes_jogadores (
  id int(11) NOT NULL,
  jogador_id int(11) NOT NULL,
  categoria enum('gols','assistencias','cartoes_amarelos','cartoes_vermelhos') NOT NULL,
  posicao int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE quartas_de_final (
  id int(11) NOT NULL,
  time_id int(11) NOT NULL,
  grupo_nome varchar(50) DEFAULT NULL,
  time_nome varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE quartas_de_final_confrontos (
  id int(11) NOT NULL,
  timeA_id int(11) NOT NULL,
  timeB_id int(11) NOT NULL,
  fase enum('quartas') NOT NULL,
  gols_marcados_timeA int(11) DEFAULT NULL,
  gols_marcados_timeB int(11) DEFAULT NULL,
  gols_contra_timeA int(11) DEFAULT NULL,
  gols_contra_timeB int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE semifinais (
  id int(11) NOT NULL,
  time_id int(11) NOT NULL,
  grupo_nome varchar(50) DEFAULT NULL,
  time_nome varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE semifinais_confrontos (
  id int(11) NOT NULL,
  timeA_id int(11) NOT NULL,
  timeB_id int(11) NOT NULL,
  fase enum('semifinais') NOT NULL,
  gols_marcados_timeA int(11) DEFAULT NULL,
  gols_marcados_timeB int(11) DEFAULT NULL,
  gols_contra_timeA int(11) DEFAULT NULL,
  gols_contra_timeB int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE times (
  id int(11) NOT NULL,
  nome varchar(100) NOT NULL,
  logo blob NOT NULL,
  grupo_id int(11) NOT NULL,
  token varchar(64) DEFAULT NULL,
  pts int(11) DEFAULT 0,
  vitorias int(11) DEFAULT 0,
  empates int(11) DEFAULT 0,
  derrotas int(11) DEFAULT 0,
  gm int(11) DEFAULT 0,
  gc int(11) DEFAULT 0,
  sg int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE admin
  ADD PRIMARY KEY (cod_adm),
  ADD UNIQUE KEY email (email);

ALTER TABLE configuracoes
  ADD PRIMARY KEY (id);

ALTER TABLE fase_execucao
  ADD PRIMARY KEY (id);

ALTER TABLE final
  ADD PRIMARY KEY (id),
  ADD KEY time_id (time_id);

ALTER TABLE final_confrontos
  ADD PRIMARY KEY (id);

ALTER TABLE grupos
  ADD PRIMARY KEY (id);

ALTER TABLE jogadores
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY token (token),
  ADD KEY time_id (time_id);

ALTER TABLE jogos
  ADD PRIMARY KEY (id),
  ADD KEY time_id (time_id);

ALTER TABLE jogos_fase_grupos
  ADD PRIMARY KEY (id),
  ADD KEY grupo_id (grupo_id),
  ADD KEY timeA_id (timeA_id),
  ADD KEY timeB_id (timeB_id);

ALTER TABLE jogos_finais
  ADD PRIMARY KEY (id),
  ADD KEY timeA_id (timeA_id),
  ADD KEY timeB_id (timeB_id);

ALTER TABLE linkinstagram
  ADD PRIMARY KEY (codinsta);

ALTER TABLE linklive
  ADD PRIMARY KEY (codlive);

ALTER TABLE noticias
  ADD PRIMARY KEY (id);

ALTER TABLE oitavas_de_final
  ADD PRIMARY KEY (id),
  ADD KEY time_id (time_id);

ALTER TABLE oitavas_de_final_confrontos
  ADD PRIMARY KEY (id);

ALTER TABLE posicoes_jogadores
  ADD PRIMARY KEY (id),
  ADD KEY jogador_id (jogador_id);

ALTER TABLE quartas_de_final
  ADD PRIMARY KEY (id),
  ADD KEY time_id (time_id);

ALTER TABLE quartas_de_final_confrontos
  ADD PRIMARY KEY (id);

ALTER TABLE semifinais
  ADD PRIMARY KEY (id),
  ADD KEY time_id (time_id);

ALTER TABLE semifinais_confrontos
  ADD PRIMARY KEY (id);

ALTER TABLE times
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY token (token),
  ADD KEY grupo_id (grupo_id);


ALTER TABLE configuracoes
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE fase_execucao
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE final
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE final_confrontos
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE grupos
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE jogadores
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE jogos
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE jogos_fase_grupos
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE jogos_finais
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE linkinstagram
  MODIFY codinsta int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE noticias
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE oitavas_de_final
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE oitavas_de_final_confrontos
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE posicoes_jogadores
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE quartas_de_final
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE quartas_de_final_confrontos
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE semifinais
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE semifinais_confrontos
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE times
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE final
  ADD CONSTRAINT final_ibfk_1 FOREIGN KEY (time_id) REFERENCES `times` (id);

ALTER TABLE jogadores
  ADD CONSTRAINT jogadores_ibfk_1 FOREIGN KEY (time_id) REFERENCES `times` (id);

ALTER TABLE jogos
  ADD CONSTRAINT jogos_ibfk_1 FOREIGN KEY (time_id) REFERENCES `times` (id);

ALTER TABLE jogos_fase_grupos
  ADD CONSTRAINT jogos_fase_grupos_ibfk_1 FOREIGN KEY (grupo_id) REFERENCES grupos (id),
  ADD CONSTRAINT jogos_fase_grupos_ibfk_2 FOREIGN KEY (timeA_id) REFERENCES `times` (id),
  ADD CONSTRAINT jogos_fase_grupos_ibfk_3 FOREIGN KEY (timeB_id) REFERENCES `times` (id);

ALTER TABLE jogos_finais
  ADD CONSTRAINT jogos_finais_ibfk_1 FOREIGN KEY (timeA_id) REFERENCES `times` (id),
  ADD CONSTRAINT jogos_finais_ibfk_2 FOREIGN KEY (timeB_id) REFERENCES `times` (id);

ALTER TABLE oitavas_de_final
  ADD CONSTRAINT oitavas_de_final_ibfk_1 FOREIGN KEY (time_id) REFERENCES `times` (id);

ALTER TABLE posicoes_jogadores
  ADD CONSTRAINT posicoes_jogadores_ibfk_1 FOREIGN KEY (jogador_id) REFERENCES jogadores (id);

ALTER TABLE quartas_de_final
  ADD CONSTRAINT quartas_de_final_ibfk_1 FOREIGN KEY (time_id) REFERENCES `times` (id);

ALTER TABLE semifinais
  ADD CONSTRAINT semifinais_ibfk_1 FOREIGN KEY (time_id) REFERENCES `times` (id);

ALTER TABLE times
  ADD CONSTRAINT times_ibfk_1 FOREIGN KEY (grupo_id) REFERENCES grupos (id);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
