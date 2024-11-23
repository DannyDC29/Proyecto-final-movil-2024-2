using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace therapy.modelos
{
    public class Especialista
    {
        public int especialista_id { get; set; } // Clave primaria
        public int User_usuario_id { get; set; } // Clave foránea hacia `user`
    }
}
