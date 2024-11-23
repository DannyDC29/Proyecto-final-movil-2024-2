using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace APIAnimalTherapy.Models
{
    [Table("admin")] // Nombre de la tabla en la base de datos
    public class Admin
    {
        [Key]
        [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
        public int admin_id { get; set; }

        [ForeignKey("User")]
        public int User_usuario_id { get; set; } // Clave foránea hacia User

        public User User { get; set; } // Propiedad de navegación hacia User

    }
}
