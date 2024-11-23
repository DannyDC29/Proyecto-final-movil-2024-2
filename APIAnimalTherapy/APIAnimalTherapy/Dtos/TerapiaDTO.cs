namespace APIAnimalTherapy.Dtos
{
    public class TerapiaDTO
    {
        public int idTerapia { get; set; }
        public int Cliente_cliente_id { get; set; }
        public int Especialista_especialista_id { get; set; }
        public int? Animal_animal_id { get; set; }
        public DateTime? fecha_inicio { get; set; }
        public DateTime? fecha_fin { get; set; }
        public string estado { get; set; }
        public string notas { get; set; }
        public string experiencia { get; set; }
    }
}
