create table ct_sections(
	pk_section int PRIMARY key not null AUTO_INCREMENT,
    name varchar(50) not null,
    description varchar(255) not null,
    path varchar(255) not null,
    fk_parent int,
    available bit not null,
    foreign key (fk_parent) REFERENCES ct_sections(pk_section)
);

create table ct_documents(
    pk_document int primary key not null AUTO_INCREMENT,
    name varchar(100) not null,
    description varchar(2500) not null,
    fk_section int,
    creation_date timestamp default current_timestamp,
    last_update_date timestamp default current_timestamp,
    available bit not null,
    foreign key (fk_section) REFERENCES ct_sections(pk_section)
);
