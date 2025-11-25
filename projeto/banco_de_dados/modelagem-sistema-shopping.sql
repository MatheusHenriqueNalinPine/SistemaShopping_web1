drop database if exists dbShopping;

create database dbShopping;

use dbShopping;

create table tbUsuario
(
    id    int auto_increment,
    nome  varchar(50)         not null,
    email varchar(255) unique not null,
    senha varchar(100)        not null,
    cpf   char(11) unique     not null,
    cargo varchar(30),
    constraint pkUsuario primary key (id)
);

create table tbServico
(
    id            int auto_increment,
    nome          varchar(100) not null,
    descricao     text,
    imagem        longblob     not null,
    nome_imagem   varchar(255),
    tipo_imagem   varchar(50) default ('image/png'),
    url_imagem    longtext,
    data_registro date        default (curdate()),
    constraint pkServico primary key (id)
);

create table tbCategoriaLoja
(
    id        int auto_increment,
    categoria varchar(30) not null,
    constraint pkCategoriaLoja primary key (id)
);

create table tbLoja
(
    id               int         not null,
    id_categoria     int         not null,
    posicao          char(5)     not null,
    telefone_contato char(11),
    cnpj             char(14) unique,
    loja_restaurante varchar(11) not null,
    constraint pkLoja primary key (id),
    constraint fkLojaServico foreign key (id) references tbServico (id),
    constraint fkLojaCategoria foreign key (id_categoria) references tbCategoriaLoja (id)
);

create table tbHorarioFuncionamento
(
    horario_inicial time,
    horario_final   time,
    dia_semana      varchar(7),
    constraint pkHorario primary key (horario_inicial, horario_final, dia_semana)
);

create table tbHorarioServico
(
    id              int auto_increment,
    horario_inicial time,
    horario_final   time,
    dia_semana      varchar(7),
    id_servico      int,
    constraint pkHorarioServico primary key (id),
    constraint fkHorarioServicoServico foreign key (id_servico) references tbServico (id),
    constraint fkHorarioServicoHorario foreign key (horario_inicial, horario_final, dia_semana) references tbHorarioFuncionamento (horario_inicial, horario_final, dia_semana)
);

create table tbCategoriaFilme
(
    id        int auto_increment,
    categoria varchar(30) not null,
    constraint pkCategoriaFilme primary key (id)
);

create table tbFilme
(
    id                 int auto_increment,
    nome               varchar(100) not null,
    id_categoria_filme int,
    sala               varchar(50),
    formato            varchar(30),
    horarios           longtext,
    nome_imagem        varchar(255),
    tipo_imagem        varchar(50) default ('image/png'),
    imagem             longblob,
    constraint pkFilme primary key (id),
    constraint fkFilmeCategoria foreign key (id_categoria_filme) references tbCategoriaFilme (id)
);

create table tbHorarioExibicaoFilme
(
    id_filme          int         not null,
    data_hora         datetime    not null,
    sala_filme        int         not null,
    legendado_dublado varchar(9)  not null,
    modo_exibicao     varchar(15) not null, -- 3D, 2D, IMAX
    constraint pkHorarioExibicao primary key (id_filme, data_hora, sala_filme),
    constraint fkHorarioFilme foreign key (id_filme) references tbFilme (id)
);

create table tbCategoriaAnuncio
(
    id        int auto_increment,
    categoria varchar(30) not null,
    constraint pkCategoriaAnuncio primary key (id)
);

create table tbAnuncio
(
    id                   int,
    formato_anuncio      varchar(30) not null,
    id_categoria_anuncio int         not null,
    constraint pkAnuncio primary key (id),
    constraint fkAnuncioServico foreign key (id) references tbServico (id),
    constraint fkAnuncioCategoria foreign key (id_categoria_anuncio) references tbCategoriaAnuncio (id)
);


insert into tbUsuario (nome, email, senha, cpf, cargo)
values ('Dono do shopping', 'admin@exemplo.com', '$2y$10$n7avFbK6dB6joBDXa2hVIe4QOyxLc6VobFjqErUj57aqcb14tDYMe',
        '11111111111', 'administrador');

-- inserts de exemplo para categorias de filme
insert into tbCategoriaFilme (categoria) values ('Ação'), ('Comédia'), ('Drama'), ('Infantil');