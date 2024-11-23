using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace APIAnimalTherapy.Models
{
    [Table("terapia")]
    public class Terapia
    {
        [Key]
        [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
        public int idTerapia { get; set; }

        [ForeignKey("Cliente")]
        public int Cliente_cliente_id { get; set; }
        public Cliente cliente { get; set; }

        [ForeignKey("Especialista")]
        public int Especialista_especialista_id { get; set; }
        public Especialista especialista { get; set; }

        [ForeignKey("Animal")]
        public int? Animal_animal_id { get; set; }
        public Animal animal { get; set; }

        public DateTime? fecha_inicio { get; set; }
        public DateTime? fecha_fin { get; set; }

        public string estado { get; set; } = "activo";
        public string notas { get; set; } = "Sin notas";
        public string experiencia { get; set; } = "Sin experiencia";

        // Propiedad de navegación para Seguimientos
        public ICollection<Seguimiento> seguimientos { get; set; } = new List<Seguimiento>();
    }
}
