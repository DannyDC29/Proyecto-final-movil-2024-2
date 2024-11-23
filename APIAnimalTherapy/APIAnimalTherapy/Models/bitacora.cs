using System;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace APIAnimalTherapy.Models
{
    [Table("bitacora")] // Nombre de la tabla en la base de datos
    public class Bitacora
    {
        [Key]
        [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
        public int bitacora_id { get; set; } // Corresponde a bitacora_id

        [Required]
        [StringLength(255)]
        public string accion { get; set; } // Corresponde a accion

        [StringLength(255)]
        public string? entidad { get; set; } // Corresponde a entidad, puede ser nulo

        public DateTime fecha_hora { get; set; } = DateTime.Now; // Corresponde a fecha_hora, con valor predeterminado

        [StringLength(255)]
        public string? descripcion { get; set; } // Corresponde a descripcion, puede ser nulo

        // Clave foránea hacia Admin
        [ForeignKey(nameof(Admin))] // Asegúrate de usar el nombre exacto de la propiedad de navegación
        public int? Admin_admin_id { get; set; }
        public Admin? Admin { get; set; } // Propiedad de navegación hacia Admin

        // Clave foránea hacia Especialista
        [ForeignKey(nameof(Especialista))] // Asegúrate de usar el nombre exacto de la propiedad de navegación
        public int? Especialista_especialista_id { get; set; }
        public Especialista? Especialista { get; set; } // Propiedad de navegación hacia Especialista
    }
}
