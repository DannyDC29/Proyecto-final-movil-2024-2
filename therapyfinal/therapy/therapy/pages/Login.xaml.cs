using System;
using therapy.Dtos;
using therapy.Services;
using Microsoft.Maui.Controls;
using Plugin.Maui.Audio;
using therapy.modelos;


namespace therapy.pages
{
    public partial class Login : ContentPage
    {
        public Login()
        {
            InitializeComponent(); // Inicializa los componentes
        }

        private void TogglePasswordVisibility(object sender, EventArgs e)
        {
            PasswordEntry.IsPassword = !PasswordEntry.IsPassword; // Alterna entre ocultar y mostrar la contraseña
        }

        private async void OnSignInClicked(object sender, EventArgs e)
        {
            var loginDto = new LoginDto
            {
                correo = EmailEntry.Text?.Trim(),
                contrasena = PasswordEntry.Text?.Trim()
            };

            if (string.IsNullOrWhiteSpace(loginDto.correo) || string.IsNullOrWhiteSpace(loginDto.contrasena))
            {
                await DisplayAlert("Error", "Por favor, completa todos los campos.", "OK");
                return;
            }

            var apiService = new ApiService();

            try
            {
                // Autenticar al usuario
                var user = await apiService.AuthenticateUser(loginDto);

                if (user != null)
                {
                    // Guardar `usuario_id` en SecureStorage
                    await SecureStorage.SetAsync("usuario_id", user.usuario_id.ToString());

                    await DisplayAlert("Éxito", "Inicio de sesión exitoso.", "OK");

                    // Redirigir a la página principal
                    var audioManager = DependencyService.Get<IAudioManager>();
                    await Navigation.PushAsync(new Comenzar(audioManager));
                }
                else
                {
                    await DisplayAlert("Error", "Credenciales incorrectas.", "OK");
                }
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", $"Ocurrió un problema: {ex.Message}", "OK");
            }
        }

    }
}
