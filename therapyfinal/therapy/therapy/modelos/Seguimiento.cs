using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace therapy.modelos
{
    public class Seguimiento
    {
        public int seguimiento_id { get; set; } // Clave primaria
        public string descripcion { get; set; } // Descripción del seguimiento
        public DateTime? fecha { get; set; } // Fecha del seguimiento
        public int Terapia_idTerapia { get; set; } // Clave foránea hacia `terapia`
        public byte[] foto_seguimiento { get; set; } // Foto asociada al seguimiento (almacenada como BLOB)
    }
}
