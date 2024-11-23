using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace therapy.modelos
{
    public class User
    {
        public int usuario_id { get; set; } // Clave primaria
        public string nombre { get; set; } // Nombre del usuario
        public string apellido { get; set; } // Apellido del usuario
        public string correo { get; set; } // Correo electrónico (único)
        public string contrasena { get; set; } // Contraseña
    }
}
