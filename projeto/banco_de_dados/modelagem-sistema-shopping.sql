drop database if exists dbShopping;

create database dbShopping;

use dbShopping;

create table tbUsuario(
	id int auto_increment,
    nome varchar(50) not null,
    email varchar(255) unique not null,
    senha varchar(100) not null,
    cpf char(11) unique not null,
    cargo varchar(30),
    constraint pkUsuario primary key(id)
);

create table tbServico(
	id int auto_increment,
    nome varchar(100) not null,
    descricao text,
    imagem longblob not null,
    tipo_imagem varchar(50) default('image/png'),
    data_registro date default(curdate()),
    constraint pkServico primary key(id)
);

create table tbLoja(
	id int not null,
    categoria varchar(30),
    posicao char(5) not null,
    telefone_contato char(11),
    cnpj char(14) unique,
    loja_restaurante varchar(11) not null,
    constraint pkLoja primary key(cnpj),
    constraint fkLojaServico foreign key(id) references tbServico(id)
);

create table tbEvento(
	id int,
    data_inicial date,
    data_final date,
    constraint pkEvento primary key(id),
    constraint fkEventoServico foreign key(id) references tbServico(id)
);

create table tbHorarioFuncionamento(
    horario_inicial time,
    horario_final time,
    id_servico int, -- Pode ser usado por loja ou evento
    constraint pkHorario primary key(horario_inicial, horario_final, id_servico),
    constraint fkHorarioServico foreign key(id_servico) references tbServico(id)
);

create table tbFilme(
	id int not null,
    genero varchar(50),
    constraint pkFilme primary key(id),
    constraint fkFilmeServico foreign key(id) references tbServico(id)
);

create table tbHorarioExibicaoFilme(
	id_filme int not null,
    data_hora datetime not null,
    sala_filme int not null,
    legendado_dublado varchar(9) not null,
    modo_exibicao varchar(15) not null, -- 3D, 2D, IMAX
    constraint pkHorarioExibicao primary key(id_filme, data_hora, sala_filme),
    constraint fkHorarioFilme foreign key(id_filme) references tbFilme(id)
);

create table tbAnuncio(
	id int,
    formato_anuncio varchar(30) not null,
    categoria_anuncio varchar(30) not null,
    constraint pkAnuncio primary key(id),
    constraint fkAnuncioServico foreign key(id) references tbServico(id)
);

insert into tbUsuario (nome, email, senha, cpf, cargo) values('Dono do shopping', 'admin@exemplo.com', '$2y$10$n7avFbK6dB6joBDXa2hVIe4QOyxLc6VobFjqErUj57aqcb14tDYMe', '11111111111', 'administrador');