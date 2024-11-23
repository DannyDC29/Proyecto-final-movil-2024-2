using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace therapy.modelos
{
    public class Cliente
    {
        public int cliente_id { get; set; } // Clave primaria
        public int User_usuario_id { get; set; } // Clave foránea hacia `user`
        public string direccion { get; set; } // Dirección del cliente
        public string telefono { get; set; } // Teléfono
        public string Preferencia_animal { get; set; } // Preferencia de animal
        public string diagnostico { get; set; } // Diagnóstico asociado
    }
}
