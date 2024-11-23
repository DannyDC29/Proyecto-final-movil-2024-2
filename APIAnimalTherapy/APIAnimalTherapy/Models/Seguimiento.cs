using System;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace APIAnimalTherapy.Models
{
    [Table("seguimiento")]
    public class Seguimiento
    {
        [Key]
        [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
        public int seguimiento_id { get; set; } // Clave primaria

        [StringLength(255)]
        public string? descripcion { get; set; } // Permitir valores nulos para la descripción

        public DateTime? fecha { get; set; } // Fecha del seguimiento

        [ForeignKey("Terapia")]
        public int Terapia_idTerapia { get; set; } // Clave foránea hacia Terapia
        public Terapia terapia { get; set; } // Propiedad de navegación hacia Terapia

        public byte[]? foto_seguimiento { get; set; } // Imagen en formato byte[], permite valores nulos
    }
}
