using Microsoft.Maui.Controls;
using System.Threading.Tasks;
using therapy.Services;
using therapy.Dtos;

namespace therapy.pages
{
    public partial class DatosPersonales : ContentPage
    {
        private readonly ApiService _apiService;

        public DatosPersonales()
        {
            InitializeComponent();
            _apiService = new ApiService(); // Instanciar el servicio API
            LoadUserData();
        }

        private async void LoadUserData()
        {
            try
            {
                // Obtener el cliente ID almacenado en las preferencias
                int clienteId = Preferences.Get("cliente_id", 0);
                if (clienteId == 0)
                {
                    await DisplayAlert("Error", "No se pudo obtener los datos del usuario.", "OK");
                    return;
                }

                // Llamar a la API para obtener los datos del cliente
                var userData = await _apiService.GetClienteWithUserAsync(clienteId);
                if (userData != null)
                {
                    UserNameLabel.Text = $"{userData.Nombre} {userData.Apellido}";
                    UserEmailLabel.Text = userData.Correo;
                    UserPhoneLabel.Text = string.IsNullOrWhiteSpace(userData.Telefono) ? "No registrado" : userData.Telefono;
                    UserAddressLabel.Text = string.IsNullOrWhiteSpace(userData.Direccion) ? "No registrada" : userData.Direccion;
                }
                else
                {
                    await DisplayAlert("Error", "No se pudieron cargar los datos del usuario.", "OK");
                }
            }
            catch (Exception ex)
            {
                await DisplayAlert("Error", $"Ocurrió un problema al cargar los datos: {ex.Message}", "OK");
            }
        }
    }
}
