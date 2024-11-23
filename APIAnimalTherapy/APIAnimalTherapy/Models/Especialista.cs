using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace APIAnimalTherapy.Models
{
    [Table("especialista")] // Nombre de la tabla en la base de datos
    public class Especialista
    {
        [Key]
        [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
        public int especialista_id { get; set; } // Corresponde a especialista_id

        [Required]
        [ForeignKey("User")]
        public int User_usuario_id { get; set; } // Clave foránea hacia User
        public User user { get; set; } // Propiedad de navegación hacia User

        // Propiedad de navegación hacia Terapia (uno a muchos)
        public ICollection<Terapia> terapias { get; set; } = new List<Terapia>();
    }
}
