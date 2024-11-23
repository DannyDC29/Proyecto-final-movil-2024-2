using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace APIAnimalTherapy.Models
{
    [Table("User")] // Nombre de la tabla en la base de datos
    public class User
    {
        [Key]
        [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
        public int usuario_id { get; set; } // Clave primaria

        [Required]
        public string nombre { get; set; } // Campo obligatorio

        [Required]
        public string apellido { get; set; } // Campo obligatorio

        [Required]
        [EmailAddress]
        public string correo { get; set; } // Campo obligatorio con validación de correo electrónico

        [Required]
        public string contrasena { get; set; } // Campo obligatorio
    }
}
