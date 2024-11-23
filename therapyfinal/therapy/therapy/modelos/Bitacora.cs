using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace therapy.modelos
{
    public class Bitacora
    {
        public int bitacora_id { get; set; } // Clave primaria
        public string accion { get; set; } // Acción realizada
        public string entidad { get; set; } // Entidad afectada
        public DateTime fecha_hora { get; set; } // Fecha y hora del evento
        public string descripcion { get; set; } // Descripción del evento
        public int? Admin_admin_id { get; set; } // Clave foránea hacia `admin` (puede ser null)
        public int? Especialista_especialista_id { get; set; } // Clave foránea hacia `especialista` (puede ser null)
    }
}
