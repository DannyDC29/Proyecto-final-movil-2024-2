using Microsoft.Maui.Controls;
using therapy.Services;
using System;

namespace therapy.pages
{
    public partial class Perfil : ContentPage
    {
        private readonly ApiService _apiService;
        private int _clienteId; // Almacena el ID del cliente

        // Constructor predeterminado
        public Perfil()
        {
            InitializeComponent();
            _apiService = new ApiService();
            LoadUserData();
        }

        // Nuevo constructor que acepta clienteId
        public Perfil(int clienteId)
        {
            InitializeComponent();
            _apiService = new ApiService();
            _clienteId = clienteId; // Asignar el clienteId
            LoadUserData();
        }

        private async void LoadUserData()
        {
            try
            {
                // Usar el clienteId pasado al constructor, o desde las preferencias
                var clienteId = _clienteId != 0 ? _clienteId : Preferences.Get("cliente_id", 0);

                if (clienteId == 0)
                {
                    await DisplayAlert("Error", "No se pudo obtener el cliente desde las preferencias.", "OK");
                    return;
                }

                // Obtener datos del cliente con el usuario relacionado desde la API
                var clienteConUsuario = await _apiService.GetClienteWithUserAsync(clienteId);
                if (clienteConUsuario != null)
                {
                    UserNameLabel.Text = $"{clienteConUsuario.Nombre} {clienteConUsuario.Apellido}";
                    UserEmailLabel.Text = clienteConUsuario.Correo;
                }
                else
                {
                    await DisplayAlert("Error", "No se pudo obtener la información del usuario.", "OK");
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al cargar los datos del usuario: {ex.Message}");
                await DisplayAlert("Error", "Ocurrió un error al cargar los datos del usuario.", "OK");
            }
        }

        private async void OnDatosPersonalesClicked(object sender, EventArgs e)
        {
            try
            {
                await Navigation.PushAsync(new DatosPersonales());
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al navegar a DatosPersonales: {ex.Message}");
            }
        }

        private async void OnSeguimientoClicked(object sender, EventArgs e)
        {
            try
            {
                await Navigation.PushAsync(new Seguimiento());
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al navegar a Seguimiento: {ex.Message}");
            }
        }

        private async void OnAnimalesClicked(object sender, EventArgs e)
        {
            try
            {
                await Navigation.PushAsync(new GaleriaMascotas());
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error al navegar a Mascotas: {ex.Message}");
            }
        }

        private async void OnCerrarSesionClicked(object sender, EventArgs e)
        {
            bool confirm = await DisplayAlert("Cerrar Sesión", "¿Estás seguro que quieres cerrar sesión?", "Sí", "No");
            if (confirm)
            {
                Preferences.Clear();
                await Navigation.PopToRootAsync();
            }
        }
    }
}
