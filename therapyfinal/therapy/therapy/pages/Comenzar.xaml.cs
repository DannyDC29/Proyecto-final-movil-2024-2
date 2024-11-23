using Microsoft.Maui.Controls;
using Plugin.Maui.Audio;
using therapy.Services; // Asegúrate de incluir el espacio de nombres correcto para ApiService

namespace therapy.pages
{
    public partial class Comenzar : ContentPage
    {
        private readonly IAudioManager _audioManager;
        private readonly ApiService _apiService; // Instancia del servicio API

        public Comenzar(IAudioManager audioManager)
        {
            InitializeComponent();
            _audioManager = audioManager;
            _apiService = new ApiService(); // Crear la instancia de ApiService

        }


        private async void OnComenzarClicked(object sender, EventArgs e)
        {
            // Obtener el cliente ID almacenado en las preferencias
            int clienteId = Preferences.Get("cliente_id", 0);
            Console.WriteLine($"Cliente ID obtenido de preferencias: {clienteId}"); // Imprime el cliente_id

            if (clienteId == 0)
            {
                await DisplayAlert("Error", "No se pudo obtener el cliente. Intenta iniciar sesión nuevamente.", "OK");
                return;
            }

            // Verificar si el cliente tiene un animal asignado
            bool tieneAnimalAsignado = await _apiService.HasAssignedAnimalAsync(clienteId);

            Console.WriteLine($"Tiene animal asignado: {tieneAnimalAsignado}");

            if (tieneAnimalAsignado)
            {
                // Si tiene un animal asignado, redirigir al perfil
                await Navigation.PushAsync(new Perfil()); // Perfil no debe necesitar argumentos
            }
            else
            {
                // Si no tiene un animal asignado, redirigir a la página de preferencias
                await Navigation.PushAsync(new Preferencia(_audioManager, _apiService));
            }
        }

    }
}
