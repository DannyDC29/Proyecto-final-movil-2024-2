using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace therapy.Dtos
{
    public class AnimalDTO
    {

        public int animal_id { get; set; }
        public string nombre { get; set; }
        public string tipo { get; set; }
        public string estado { get; set; }
        public string especialidad { get; set; }
        public byte[] foto_animal { get; set; } // Formato binario de la imagen
    }
}

