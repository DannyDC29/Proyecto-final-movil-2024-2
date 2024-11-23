using System.Collections.ObjectModel;
using System.Reflection.Metadata;

namespace therapy.modelos
{
    public class Mascota
    {
        public int animal_id { get; set; }
        public string nombre { get; set; }
        public string tipo { get; set; }
        public string estado { get; set; }
        public string especialidad { get; set; }
        public string foto_animal { get; set; }
    }
}
