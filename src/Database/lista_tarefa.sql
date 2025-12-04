-- criacao de tabelas

CREATE TABLE cliente (
  idCli PRIMARY KEY AUTO_INCREMENT,
  nome char(255) ,
  email char(255) ,
  senha char(50) 
);


CREATE TABLE tarefas (
  id PRIMARY KEY AUTO_INCREMENT,
  descricao varchar(255) ,
  feita  DEFAULT 0,
  criada_em timestamp  DEFAULT current_timestamp()
);