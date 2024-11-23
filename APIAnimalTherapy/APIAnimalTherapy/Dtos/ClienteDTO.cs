namespace APIAnimalTherapy.Dtos
{
    public class ClienteDTO
    {
        public int cliente_id { get; set; }
        public int User_usuario_id { get; set; }
        public string direccion { get; set; }
        public string telefono { get; set; }
        public string Preferencia_animal { get; set; }
        public string diagnostico { get; set; }
    }
}
