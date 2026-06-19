-- Table des rôles
CREATE TABLE Role (
    idRole SERIAL PRIMARY KEY,
    nomRole VARCHAR(100) UNIQUE NOT NULL
);

-- Table des utilisateurs
CREATE TABLE Utilisateur (
    idUtilisateur SERIAL PRIMARY KEY,
    nomUtilisateur VARCHAR(255) NOT NULL,
    prenomUtilisateur VARCHAR(255) NOT NULL,
    emailUtilisateur VARCHAR(255) UNIQUE NOT NULL,
    mdpUtilisateur VARCHAR(255) NOT NULL,
    dateInscription DATE DEFAULT CURRENT_DATE,
    idRole INT REFERENCES Role(idRole)
);

-- Table des auteurs
CREATE TABLE Auteur (
    idAuteur SERIAL PRIMARY KEY,
    nomAuteur VARCHAR(255) NOT NULL,
    prenomAuteur VARCHAR(255),
    dateNaissanceAuteur DATE NOT NULL
);

-- Table des œuvres
CREATE TABLE Oeuvre (
    idOeuvre SERIAL PRIMARY KEY,
    titreOeuvre VARCHAR(255) NOT NULL,
    dateCreationOeuvre DATE DEFAULT CURRENT_DATE,
    descriptionOeuvre TEXT NOT NULL,
    idAuteur INT REFERENCES Auteur(idAuteur)
);

-- Table des critiques
CREATE TABLE Critique (
    idCritique SERIAL PRIMARY KEY,
    contenuCritique TEXT NOT NULL,
    dateCritique DATE DEFAULT CURRENT_DATE,
    noteCritique INT CHECK (noteCritique BETWEEN 0 AND 5),
    idOeuvre INT REFERENCES Oeuvre(idOeuvre),
    idUtilisateur INT REFERENCES Utilisateur(idUtilisateur)
);

-- Table des festivals
CREATE TABLE Festival (
    idFestival SERIAL PRIMARY KEY,
    nomFestival VARCHAR(255) NOT NULL
);

-- Table des éditions de festivals
CREATE TABLE EditionFestival (
    idEditionFestival SERIAL PRIMARY KEY,
    anneeFestival INT NOT NULL,
    lieuFestival VARCHAR(255) NOT NULL,
    idFestival INT REFERENCES Festival(idFestival)
);

-- Table des prix
CREATE TABLE Prix (
    idPrix SERIAL PRIMARY KEY,
    nomPrix VARCHAR(255) NOT NULL,
    descriptionPrix TEXT,
    idEditionFestival INT REFERENCES EditionFestival(idEditionFestival)
);

-- Table des attributions de prix
CREATE TABLE AttributionPrix (
    idOeuvre INT REFERENCES Oeuvre(idOeuvre),
    idPrix INT REFERENCES Prix(idPrix),
    PRIMARY KEY (idOeuvre, idPrix)
);

-- Table des types d'événements
CREATE TABLE TypeEvenement (
    idTypeEvenement SERIAL PRIMARY KEY,
    libelleTypeEvenement VARCHAR(255) NOT NULL
);

-- Table des événements
CREATE TABLE Evenement (
    idEvenement SERIAL PRIMARY KEY,
    nomEvenement VARCHAR(255) NOT NULL,
    dateEvenement DATE NOT NULL,
    descriptionEvenement TEXT,
    idTypeEvenement INT REFERENCES TypeEvenement(idTypeEvenement),
    idUtilisateur INT REFERENCES Utilisateur(idUtilisateur)
);

-- Table des participations aux événements
CREATE TABLE Participation (
    idUtilisateur INT REFERENCES Utilisateur(idUtilisateur),
    idEvenement INT REFERENCES Evenement(idEvenement),
    PRIMARY KEY (idUtilisateur, idEvenement)
);

-- Table des discussions
CREATE TABLE Discussion (
    idDiscussion SERIAL PRIMARY KEY,
    dateCreationDiscussion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    idCritique INT REFERENCES Critique(idCritique)
);

-- Table des messages
CREATE TABLE Message (
    idMessage SERIAL PRIMARY KEY,
    contenuMessage TEXT NOT NULL,
    dateMessage TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    idDiscussion INT REFERENCES Discussion(idDiscussion),
    idUtilisateur INT REFERENCES Utilisateur(idUtilisateur)
);

-- Table des permissions
CREATE TABLE Permission (
    idPermission SERIAL PRIMARY KEY,
    reponsePermission BOOLEAN NOT NULL
);


-- Insertion des rôles par défaut
INSERT INTO Role (nomRole) VALUES ('admin'), ('membre'), ('visiteur');

INSERT INTO Utilisateur (nomUtilisateur, prenomUtilisateur, emailUtilisateur, mdpUtilisateur, idRole) VALUES 
('clement', 'raphael', 'raphael.clement@gmail.com', 'NieE#O42Z', 1), -- Admin
('dupont', 'antoine', 'antoine.dupont@example.com', 'pAs$woRd1!2?3', 2), -- Membre
('martin', 'claire', 'claire.martin@example.com', 'Se(ur€p4s5', 2), -- Membre
('doe', 'john', 'john.doe@example.com', 'guestpass', 3); -- Visiteur


INSERT INTO Auteur (nomAuteur, prenomAuteur, dateNaissanceAuteur) VALUES
('Nolan', 'Christopher', '1970-07-30'),
('Van Gogh', 'Vincent', '1853-03-30'),
('Austen', 'Jane', '1775-12-16'),
('Tchaïkovski', 'Piotr Ilitch', '1840-05-07'),
('Mercury', 'Freddie', '1946-09-05');


INSERT INTO Oeuvre (titreOeuvre, descriptionOeuvre, idAuteur) VALUES
('Inception', 'Film de science-fiction captivant', 1),
('La Nuit étoilée', 'Peinture célèbre de Van Gogh', 2),
('Orgueil et Préjugés', 'Roman emblématique du XIXe siècle', 3),
('Le Lac des cygnes', 'Ballet classique de Tchaïkovski', 4),
('Bohemian Rhapsody', 'Chanson légendaire de Queen', 5),
('Interstellar', 'Film de science-fiction sur l exploration spatiale', 1),
('Les Tournesols', 'Peinture célèbre de Van Gogh', 2),
('Emma', 'Roman classique de Jane Austen', 3),
('Casse-Noisette', 'Ballet emblématique de Tchaïkovski', 4),
('We Will Rock You', 'Chanson iconique de Queen', 5);


INSERT INTO Critique (contenuCritique, dateCritique, noteCritique, idOeuvre, idUtilisateur) VALUES
('Un chef-d œuvre visuel et émotionnel.', '2025-05-02', 5, 1, 1),
('Une peinture qui capture l essence de la nature.', '2025-05-03', 4, 2, 2),
('Un roman captivant avec des personnages mémorables.', '2025-05-04', 5, 3, 3),
('Un ballet magnifique et intemporel.', '2025-05-05', 4, 4, 4),
('Une chanson qui reste gravée dans les mémoires.', '2025-05-06', 5, 5, 1),
('Un film qui repousse les limites de l imagination.', '2025-05-07', 5, 6, 2),
('Une peinture vibrante et pleine de vie.', '2025-05-08', 4, 7, 3),
('Un roman plein d esprit et de charme.', '2025-05-09', 4, 8, 4),
('Un ballet enchanteur qui émerveille le public.', '2025-05-10', 5, 9, 1),
('Une chanson qui fait vibrer les foules.', '2025-05-11', 4, 10, 2);

-- Ajout des types d'événements
INSERT INTO TypeEvenement (libelleTypeEvenement) VALUES
('Projection de film'),
('Exposition artistique'),
('Conférence littéraire'),
('Spectacle de danse'),
('Concert');

INSERT INTO Evenement (nomEvenement, dateEvenement, descriptionEvenement, idTypeEvenement, idUtilisateur) VALUES
('Projection spéciale d Inception', '2025-06-20', 'Une projection spéciale du film Inception avec une discussion.', 1, 1),
('Atelier peinture Van Gogh', '2025-07-15', 'Un atelier pour recréer les œuvres de Van Gogh.', 2, 2),
('Lecture publique de Jane Austen', '2025-08-25', 'Une lecture publique des œuvres de Jane Austen.', 3, 3),
('Spectacle de danse classique', '2025-09-10', 'Une performance inspirée du Lac des cygnes.', 4, 4),
('Concert hommage à Queen', '2025-10-20', 'Un concert en hommage à Freddie Mercury et Queen.', 5, 1);

INSERT INTO Participation (idUtilisateur, idEvenement) VALUES
(1, 1), -- Raphael participe à la projection
(2, 1),
(2, 2),
(3, 3),
(4, 4),
(1, 4),
(2, 4),
(3, 5),
(4, 5);
