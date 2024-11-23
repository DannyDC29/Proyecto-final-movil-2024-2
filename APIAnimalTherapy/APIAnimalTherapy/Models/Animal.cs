using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace APIAnimalTherapy.Models
{
    [Table("animal")] // Nombre de la tabla en la base de datos
    public class Animal
    {
        [Key]
        [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
        public int animal_id { get; set; } // Corresponde a animal_id

        [Required]
        [StringLength(255)]
        public string nombre { get; set; } // Nombre del animal

        [Required]
        [StringLength(255)]
        public string tipo { get; set; } // Tipo del animal

        [StringLength(255)]
        public string? estado { get; set; } = "disponible"; // Estado, con valor predeterminado

        [StringLength(255)]
        public string? especialidad { get; set; } // Especialidad, puede ser nulo

        public byte[]? foto_animal { get; set; } // Foto del animal, puede ser nulo

        // Propiedad de navegación hacia Terapia (uno a muchos)
        public ICollection<Terapia> terapias { get; set; } = new List<Terapia>();
    }
}
