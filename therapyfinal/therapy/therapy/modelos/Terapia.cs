using System;
using System.Collections.Generic;

namespace therapy.modelos
{
    public class Terapia
    {
        public int idTerapia { get; set; } // Clave primaria
        public int Cliente_cliente_id { get; set; } // Clave foránea hacia `cliente`
        public int Especialista_especialista_id { get; set; } // Clave foránea hacia `especialista`
        public int? Animal_animal_id { get; set; } // Clave foránea hacia `animal` (puede ser null)
        public DateTime? fecha_inicio { get; set; } // Fecha de inicio
        public DateTime? fecha_fin { get; set; } // Fecha de fin
        public string estado { get; set; } // Estado de la terapia
        public string notas { get; set; } // Notas adicionales
        public string experiencia { get; set; } // Experiencia registrada

        // Propiedad para almacenar los seguimientos relacionados
        public List<Seguimiento> seguimientos { get; set; } = new List<Seguimiento>();
    }
}
