namespace therapy.Dtos
{
    public class SeguimientoDTO
    {
        public int seguimiento_id { get; set; } // ID único del seguimiento
        public string descripcion { get; set; } // Comentario del usuario
        public DateTime? fecha { get; set; } // Fecha del seguimiento
        public int Terapia_idTerapia { get; set; } // ID de la terapia relacionada
        public byte[] foto_seguimiento { get; set; } // Foto del seguimiento en formato byte[]
    }
}
