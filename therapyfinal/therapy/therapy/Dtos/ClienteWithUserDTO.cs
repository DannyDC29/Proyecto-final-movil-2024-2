namespace therapy.Dtos
{
    public class ClienteWithUserDTO
    {
        public int ClienteId { get; set; } // ID del cliente
        public int UsuarioId { get; set; } // ID del usuario relacionado
        public string Nombre { get; set; } // Nombre del usuario
        public string Apellido { get; set; } // Apellido del usuario
        public string Correo { get; set; } // Correo electrónico del usuario
        public string Telefono { get; set; } // Teléfono del usuario
        public string Direccion { get; set; } // Dirección del usuario
    }
}
