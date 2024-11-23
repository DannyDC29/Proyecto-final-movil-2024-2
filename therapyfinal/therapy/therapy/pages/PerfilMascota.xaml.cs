using System;
using System.Text;
using Microsoft.Maui.Controls;
using therapy.Dtos;
using therapy.Services;

namespace therapy.pages
{
    public partial class PerfilMascota : ContentPage
    {
        private readonly ApiService _apiService;
        private readonly AnimalDTO _animal;

        public string FotoAnimalBase64 { get; set; }

        public PerfilMascota(AnimalDTO animal)
        {
            InitializeComponent();
            _apiService = new ApiService();
            _animal = animal;

            FotoAnimalBase64 = animal.foto_animal != null
                ? $"data:image/png;base64,{Convert.ToBase64String(animal.foto_animal)}"
                : "default_image.png";

            BindingContext = new
            {
                Nombre = animal.nombre,
                Especialidad = animal.especialidad,
                Estado = animal.estado,
                FotoAnimal = FotoAnimalBase64
            };
        }

        private async void OnElegirMascotaClicked(object sender, EventArgs e)
        {
            var clienteId = Preferences.Get("cliente_id", 0);
            if (clienteId == 0)
            {
                await DisplayAlert("Error", "No se pudo obtener el cliente.", "OK");
                return;
            }

            var confirm = await DisplayAlert(
                "Confirmación",
                $"¿Deseas asignar a {_animal.nombre} como tu mascota?",
                "Sí",
                "No"
            );

            if (!confirm) return;

            // Asignar el animal a la terapia
            var success = await _apiService.AssignAnimalToTherapyAsync(clienteId, _animal.animal_id);
            if (success)
            {
                await DisplayAlert("Éxito", $"{_animal.nombre} ha sido asignado a tu terapia.", "OK");
                await Navigation.PopToRootAsync(); // Volver al inicio o página principal
            }
            else
            {
                await DisplayAlert("Error", "No se pudo asignar la mascota. Inténtalo de nuevo.", "OK");
            }   
        }
    }
}
