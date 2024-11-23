using Microsoft.Maui.Controls;
using Plugin.Maui.Audio;
using System;
using System.Threading.Tasks;
using therapy.Services;
using Microsoft.Maui.Storage;

namespace therapy.pages
{
    public partial class Preferencia : ContentPage
    {
        private readonly IAudioManager _audioManager;
        private readonly ApiService _apiService;

        // Obtenemos el cliente_id desde el almacenamiento seguro o una variable global
        private readonly int _clienteId;

        public Preferencia(IAudioManager audioManager, ApiService apiService)
        {
            InitializeComponent();
            _audioManager = audioManager;
            _apiService = apiService;

            // Obtener cliente_id almacenado al iniciar sesión
            _clienteId = Preferences.Get("cliente_id", -1);

            if (_clienteId == -1)
            {
                Console.WriteLine("Error: Cliente no autenticado.");
                DisplayAlert("Error", "Cliente no autenticado. Por favor, inicia sesión.", "OK");
            }
        }

        private async void OnGatoButtonClicked(object sender, EventArgs e)
        {
            try
            {
                await UpdateAnimalPreference(new RequestPreference
                {
                    preferencia = "gato"

                });
                await PlaySound("miau.mp3");
                await NavigateToMascotasPage();
            }
            catch (Exception ex)
            {

               var error = ex.Message;
            }
            
        }

        private async void OnPerroButtonClicked(object sender, EventArgs e)
        {
            await UpdateAnimalPreference(new RequestPreference
            {
                preferencia = "perro"

            });
            await PlaySound("guau.mp3");
            await NavigateToMascotasPage();
        }

        private async Task PlaySound(string soundFile)
        {
            try
            {
                var stream = await FileSystem.OpenAppPackageFileAsync(soundFile);
                var player = _audioManager.CreatePlayer(stream);
                player.Play();
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al reproducir el sonido: {ex.Message}");
            }
        }

        private async Task NavigateToMascotasPage()
        {
            try
            {
                await Navigation.PushAsync(new Mascotas());
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al navegar a Mascotas: {ex.Message}");
            }
        }

        private async Task UpdateAnimalPreference(RequestPreference preference)
        {
            try
            {
                // Validar que el cliente esté autenticado
                if (_clienteId == -1)
                {
                    await DisplayAlert("Error", "No se puede actualizar la preferencia. Cliente no autenticado.", "OK");
                    return;
                }

                // Llamar al servicio para actualizar la preferencia en la API
                bool result = await _apiService.UpdatePreferenceAsync(_clienteId, preference);

                if (result)
                {
                    Console.WriteLine("Preferencia actualizada correctamente.");
                }
                else
                {
                    await DisplayAlert("Error", "No se pudo actualizar la preferencia en el servidor.", "OK");
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al actualizar la preferencia: {ex.Message}");
                await DisplayAlert("Error", "Ocurrió un error al actualizar la preferencia.", "OK");
            }
        }

    }
    public class RequestPreference
    {
        public string preferencia { get; set; }
    }
}
