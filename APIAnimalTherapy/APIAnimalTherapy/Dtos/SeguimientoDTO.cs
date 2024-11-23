namespace APIAnimalTherapy.Dtos
{
    public class SeguimientoDTO
    {
        public int seguimiento_id { get; set; } // ID único del seguimiento (PK)
        public string? descripcion { get; set; } // Descripción del seguimiento
        public DateTime? fecha { get; set; } // Fecha del seguimiento
        public int Terapia_idTerapia { get; set; } // ID de la Terapia asociada
        public byte[]? foto_seguimiento { get; set; } // Foto en formato byte[] (blob en la BD)
    }
}
