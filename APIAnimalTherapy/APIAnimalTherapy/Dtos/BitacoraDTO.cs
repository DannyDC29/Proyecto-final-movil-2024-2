namespace APIAnimalTherapy.Dtos
{
    public class BitacoraDTO
    {
        public int bitacora_id { get; set; }
        public string accion { get; set; }
        public string entidad { get; set; }
        public DateTime? fecha_hora { get; set; }
        public string descripcion { get; set; }
        public int? Admin_admin_id { get; set; }
        public int? Especialista_especialista_id { get; set; }
    }
}
