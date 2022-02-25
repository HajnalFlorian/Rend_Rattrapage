#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------


#------------------------------------------------------------
# Table: Profile
#------------------------------------------------------------

CREATE TABLE Profile(
        id     Int  Auto_increment  NOT NULL ,
        Pseudo Varchar (50) NOT NULL ,
        mdp    Varchar (50) NOT NULL
	,CONSTRAINT Profile_PK PRIMARY KEY (id)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Score
#------------------------------------------------------------

CREATE TABLE Score(
        idscore Int  Auto_increment  NOT NULL ,
        Score   Int NOT NULL ,
        date    Date NOT NULL ,
        id      Int NOT NULL
	,CONSTRAINT Score_PK PRIMARY KEY (idscore)

	,CONSTRAINT Score_Profile_FK FOREIGN KEY (id) REFERENCES Profile(id)
)ENGINE=InnoDB;

