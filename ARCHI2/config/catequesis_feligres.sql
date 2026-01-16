CREATE TABLE IF NOT EXISTS catequesis_feligres (
    id INT PRIMARY KEY AUTO_INCREMENT,
    catequesis_id INT NOT NULL,
    feligres_id INT NOT NULL,
    fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('inscrito', 'completado', 'abandonado') DEFAULT 'inscrito',
    nota_final DECIMAL(4,2),
    observaciones TEXT,
    FOREIGN KEY (catequesis_id) REFERENCES catequesis(id),
    FOREIGN KEY (feligres_id) REFERENCES feligreses(id),
    UNIQUE KEY unique_catequesis_feligres (catequesis_id, feligres_id)
);