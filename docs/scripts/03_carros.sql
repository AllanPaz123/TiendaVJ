CREATE TABLE carros(  
    id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nombre varchar(255) NOT NULL,
    maraca varchar(128) NOT NULL,
    estado CHAR(3) 
) COMMENT 'tabla de carros de la flota de ventas';
