using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace APIAnimalTherapy.Models
{
    [Table("cliente")] // Nombre de la tabla en la base de datos
    public class Cliente
    {
        [Key]
        [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
        public int cliente_id { get; set; } // Corresponde a cliente_id

        [Required]
        [ForeignKey("User")]
        public int User_usuario_id { get; set; } // Relación con la tabla "user"

        [StringLength(255)]
        public string direccion { get; set; } = "en espera"; // Corresponde a direccion, con valor predeterminado

        [StringLength(225)]
        public string? telefono { get; set; } // Corresponde a telefono, puede ser nulo

        [StringLength(255)]
        public string? Preferencia_animal { get; set; } // Corresponde a Preferencia_animal, puede ser nulo

        [StringLength(255)]
        public string? diagnostico { get; set; } // Corresponde a diagnostico, puede ser nulo

        // Propiedad de navegación para la relación con User
        public User user { get; set; }

        // Propiedad de navegación para la relación con Terapia (uno a muchos)
        public ICollection<Terapia> terapias { get; set; } = new List<Terapia>();
    }
}
